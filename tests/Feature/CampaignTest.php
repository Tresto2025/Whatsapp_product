<?php

namespace Tests\Feature;

use App\Models\Campaign;
use App\Models\CampaignRecipient;
use App\Models\Contact;
use App\Models\Message;
use App\Models\Tag;
use App\Models\Tenant;
use App\Models\User;
use App\Models\WhatsappAccount;
use App\Models\WhatsappTemplate;
use App\Services\WhatsApp\InboundMessageHandler;
use App\Tenancy\TenantManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Broadcasting a template to a segment: recipients are materialised, sent on a
 * (sync) queue, recorded per person, and reconciled against delivery receipts.
 */
class CampaignTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $tenant;
    private WhatsappAccount $account;

    protected function setUp(): void
    {
        parent::setUp();

        Http::fake(['graph.facebook.com/*' => Http::response(['messages' => [['id' => 'wamid.CAMP1']]], 200)]);

        $this->tenant = Tenant::factory()->create();
        $this->account = WhatsappAccount::factory()->forTenant($this->tenant)->create();

        $user = User::factory()->forTenant($this->tenant)->tenantAdmin()->create();
        $this->actingAs($user);
    }

    private function template(string $category = WhatsappTemplate::CATEGORY_MARKETING): WhatsappTemplate
    {
        return WhatsappTemplate::factory()->forAccount($this->account)->create([
            'category' => $category,
            'status' => WhatsappTemplate::STATUS_APPROVED,
            'body' => 'Hi {{1}}, a deal for you.',
            'variables' => ['1'],
        ]);
    }

    public function test_creating_a_campaign_sends_and_records_each_recipient(): void
    {
        $template = $this->template();
        Contact::factory()->forTenant($this->tenant)->count(3)->create();

        $this->post(route('tenant.campaigns.store'), [
            'name' => 'Diwali offer',
            'template_id' => $template->id,
            'segment_type' => 'all',
            'variable_map' => ['1' => ['mode' => 'name']],
        ])->assertRedirect();

        $campaign = Campaign::withoutGlobalScopes()->first();

        $this->assertNotNull($campaign);
        $this->assertSame(Campaign::STATUS_SENT, $campaign->status);
        $this->assertSame(3, CampaignRecipient::withoutGlobalScopes()->count());
        $this->assertSame(3, CampaignRecipient::withoutGlobalScopes()->where('status', CampaignRecipient::STATUS_SENT)->count());
        $this->assertSame(3, Message::withoutGlobalScopes()->where('direction', Message::OUT)->where('type', 'template')->count());
        $this->assertSame(3, $campaign->counts['sent']);

        Http::assertSentCount(3);
    }

    public function test_a_marketing_campaign_skips_opted_out_contacts(): void
    {
        $template = $this->template(WhatsappTemplate::CATEGORY_MARKETING);
        Contact::factory()->forTenant($this->tenant)->create();
        Contact::factory()->forTenant($this->tenant)->optedOut()->create();

        $this->post(route('tenant.campaigns.store'), [
            'name' => 'Marketing blast',
            'template_id' => $template->id,
            'segment_type' => 'all',
            'variable_map' => ['1' => ['mode' => 'name']],
        ]);

        $campaign = Campaign::withoutGlobalScopes()->first();

        $this->assertSame(1, $campaign->counts['sent']);
        $this->assertSame(1, $campaign->counts['skipped']);
        $this->assertSame(1, CampaignRecipient::withoutGlobalScopes()->where('status', CampaignRecipient::STATUS_SKIPPED)->count());
        Http::assertSentCount(1);
    }

    public function test_a_tag_segment_only_targets_tagged_contacts(): void
    {
        $template = $this->template();
        $tag = Tag::factory()->forTenant($this->tenant)->create();

        $tagged = Contact::factory()->forTenant($this->tenant)->create();
        $tagged->tags()->attach($tag->id);
        Contact::factory()->forTenant($this->tenant)->create(); // untagged

        $this->post(route('tenant.campaigns.store'), [
            'name' => 'VIP only',
            'template_id' => $template->id,
            'segment_type' => 'tags',
            'tags' => [$tag->id],
            'variable_map' => ['1' => ['mode' => 'name']],
        ]);

        $this->assertSame(1, CampaignRecipient::withoutGlobalScopes()->count());
        $this->assertSame($tagged->id, CampaignRecipient::withoutGlobalScopes()->first()->contact_id);
    }

    public function test_a_delivery_receipt_updates_the_recipient(): void
    {
        $template = $this->template(WhatsappTemplate::CATEGORY_UTILITY);
        Contact::factory()->forTenant($this->tenant)->create();

        $this->post(route('tenant.campaigns.store'), [
            'name' => 'Reminder',
            'template_id' => $template->id,
            'segment_type' => 'all',
            'variable_map' => ['1' => ['mode' => 'name']],
        ]);

        $recipient = CampaignRecipient::withoutGlobalScopes()->first();
        $this->assertSame(CampaignRecipient::STATUS_SENT, $recipient->status);

        // Meta reports the message was read; that must land on the recipient.
        $payload = ['entry' => [['changes' => [['value' => [
            'metadata' => ['phone_number_id' => $this->account->phone_number_id],
            'statuses' => [['id' => 'wamid.CAMP1', 'status' => 'read', 'timestamp' => '1758400100']],
        ]]]]]];

        app(TenantManager::class)->runForTenant(
            $this->tenant,
            fn () => app(InboundMessageHandler::class)->handle($this->account, $payload),
        );

        $this->assertSame(CampaignRecipient::STATUS_READ, $recipient->fresh()->status);
    }

    public function test_a_campaign_needs_an_approved_template(): void
    {
        $pending = WhatsappTemplate::factory()->forAccount($this->account)->pending()->create();
        Contact::factory()->forTenant($this->tenant)->create();

        $this->post(route('tenant.campaigns.store'), [
            'name' => 'Too early',
            'template_id' => $pending->id,
            'segment_type' => 'all',
        ])->assertSessionHasErrors('template_id');

        $this->assertSame(0, Campaign::withoutGlobalScopes()->count());
    }
}
