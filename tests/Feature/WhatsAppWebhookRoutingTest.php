<?php

namespace Tests\Feature;

use App\Jobs\ProcessInboundWhatsAppMessage;
use App\Models\Tenant;
use App\Models\WhatsappAccount;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

/**
 * Every tenant pastes the same callback URL into their own Meta app, so the
 * webhook has to work out on its own which tenant a message belongs to. That
 * decision is made from metadata.phone_number_id, and the signature is checked
 * with the app secret of the account it resolved to.
 */
class WhatsAppWebhookRoutingTest extends TestCase
{
    use RefreshDatabase;

    private function payloadFor(string $phoneNumberId, string $body = 'hi'): array
    {
        return [
            'object' => 'whatsapp_business_account',
            'entry' => [[
                'id' => '000000000000000',
                'changes' => [[
                    'field' => 'messages',
                    'value' => [
                        'messaging_product' => 'whatsapp',
                        'metadata' => [
                            'display_phone_number' => '919000000000',
                            'phone_number_id' => $phoneNumberId,
                        ],
                        'messages' => [[
                            'from' => '919111111111',
                            'id' => 'wamid.TEST',
                            'timestamp' => (string) time(),
                            'type' => 'text',
                            'text' => ['body' => $body],
                        ]],
                    ],
                ]],
            ]],
        ];
    }

    private function postWebhook(array $payload, ?string $appSecret, bool $validSignature = true)
    {
        $raw = json_encode($payload);
        $server = ['CONTENT_TYPE' => 'application/json'];

        if ($appSecret !== null) {
            $signature = 'sha256='.hash_hmac('sha256', $raw, $validSignature ? $appSecret : 'wrong-secret');
            $server['HTTP_X_HUB_SIGNATURE_256'] = $signature;
        }

        return $this->call('POST', '/api/webhook', [], [], [], $server, $raw);
    }

    public function test_message_is_routed_to_the_account_that_owns_the_number(): void
    {
        Bus::fake();

        $tenantA = Tenant::factory()->create(['name' => 'Tenant A']);
        $tenantB = Tenant::factory()->create(['name' => 'Tenant B']);

        $accountA = WhatsappAccount::factory()->forTenant($tenantA)->create();
        WhatsappAccount::factory()->forTenant($tenantB)->create();

        $this->postWebhook($this->payloadFor($accountA->phone_number_id), $accountA->app_secret)
            ->assertOk();

        Bus::assertDispatched(
            ProcessInboundWhatsAppMessage::class,
            fn (ProcessInboundWhatsAppMessage $job) => $job->whatsappAccountId === $accountA->id
        );
    }

    public function test_a_second_number_routes_to_its_own_tenant(): void
    {
        Bus::fake();

        $accountA = WhatsappAccount::factory()->create();
        $accountB = WhatsappAccount::factory()->create();

        $this->postWebhook($this->payloadFor($accountB->phone_number_id), $accountB->app_secret)
            ->assertOk();

        Bus::assertDispatched(
            ProcessInboundWhatsAppMessage::class,
            fn (ProcessInboundWhatsAppMessage $job) => $job->whatsappAccountId === $accountB->id
                && $job->whatsappAccountId !== $accountA->id
        );
    }

    public function test_unknown_phone_number_id_is_rejected(): void
    {
        Bus::fake();

        WhatsappAccount::factory()->create();

        $this->postWebhook($this->payloadFor('999999999999999'), null)
            ->assertNotFound();

        Bus::assertNothingDispatched();
    }

    public function test_a_bad_signature_is_rejected(): void
    {
        Bus::fake();

        $account = WhatsappAccount::factory()->create();

        $this->postWebhook($this->payloadFor($account->phone_number_id), $account->app_secret, false)
            ->assertForbidden();

        Bus::assertNothingDispatched();
    }

    public function test_a_missing_signature_is_rejected_when_the_account_has_a_secret(): void
    {
        Bus::fake();

        $account = WhatsappAccount::factory()->create();

        $this->postWebhook($this->payloadFor($account->phone_number_id), null)
            ->assertForbidden();

        Bus::assertNothingDispatched();
    }
}
