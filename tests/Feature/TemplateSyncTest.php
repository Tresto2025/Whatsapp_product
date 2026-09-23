<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use App\Models\WhatsappAccount;
use App\Models\WhatsappTemplate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Mirroring Meta's templates: a one-way sync that parses their component
 * structure into the columns campaigns and flows build against.
 */
class TemplateSyncTest extends TestCase
{
    use RefreshDatabase;

    private function metaResponse(array $templates): array
    {
        return ['data' => $templates, 'paging' => ['cursors' => ['after' => 'CURSOR']]];
    }

    private function marketingTemplate(string $name = 'order_update', string $status = 'APPROVED'): array
    {
        return [
            'id' => '111222333',
            'name' => $name,
            'language' => 'en_US',
            'category' => 'MARKETING',
            'status' => $status,
            'components' => [
                ['type' => 'HEADER', 'format' => 'TEXT', 'text' => 'Update'],
                ['type' => 'BODY', 'text' => 'Hi {{1}}, your order {{2}} is on its way.'],
                ['type' => 'FOOTER', 'text' => 'Reply STOP to opt out'],
                ['type' => 'BUTTONS', 'buttons' => [['type' => 'QUICK_REPLY', 'text' => 'Track']]],
            ],
        ];
    }

    private function actingAsTenantAdmin(Tenant $tenant): User
    {
        $user = User::factory()->forTenant($tenant)->tenantAdmin()->create();
        $this->actingAs($user);

        return $user;
    }

    public function test_syncing_mirrors_metas_templates_into_rows(): void
    {
        Http::fake([
            'graph.facebook.com/*/message_templates*' => Http::response(
                $this->metaResponse([$this->marketingTemplate()]) + ['paging' => []],
                200,
            ),
        ]);

        $tenant = Tenant::factory()->create();
        WhatsappAccount::factory()->forTenant($tenant)->create(['waba_id' => '555000']);
        $this->actingAsTenantAdmin($tenant);

        $this->post(route('tenant.templates.sync'))
            ->assertRedirect()
            ->assertSessionHas('success');

        $template = WhatsappTemplate::withoutGlobalScopes()->first();

        $this->assertNotNull($template);
        $this->assertSame('order_update', $template->name);
        $this->assertSame('en_US', $template->language);
        $this->assertSame(WhatsappTemplate::STATUS_APPROVED, $template->status);
        $this->assertSame(WhatsappTemplate::CATEGORY_MARKETING, $template->category);
        $this->assertSame('text', $template->header_type);
        $this->assertStringContainsString('your order', $template->body);
        $this->assertSame(['1', '2'], $template->variables);
        $this->assertSame($tenant->id, $template->tenant_id);
    }

    public function test_re_syncing_updates_rather_than_duplicates(): void
    {
        $tenant = Tenant::factory()->create();
        WhatsappAccount::factory()->forTenant($tenant)->create(['waba_id' => '555000']);
        $this->actingAsTenantAdmin($tenant);

        // Pending on the first pull, approved on the second — the same row moves.
        Http::fakeSequence('graph.facebook.com/*/message_templates*')
            ->push(['data' => [$this->marketingTemplate('order_update', 'PENDING')], 'paging' => []], 200)
            ->push(['data' => [$this->marketingTemplate('order_update', 'APPROVED')], 'paging' => []], 200);

        $this->post(route('tenant.templates.sync'));

        $this->assertSame(WhatsappTemplate::STATUS_PENDING, WhatsappTemplate::withoutGlobalScopes()->first()->status);

        $this->post(route('tenant.templates.sync'));

        $this->assertSame(1, WhatsappTemplate::withoutGlobalScopes()->count());
        $this->assertSame(WhatsappTemplate::STATUS_APPROVED, WhatsappTemplate::withoutGlobalScopes()->first()->status);
    }

    public function test_sync_needs_a_connected_number(): void
    {
        $tenant = Tenant::factory()->create();
        $this->actingAsTenantAdmin($tenant);

        $this->post(route('tenant.templates.sync'))->assertSessionHasErrors('sync');

        $this->assertSame(0, WhatsappTemplate::withoutGlobalScopes()->count());
    }

    public function test_templates_are_listed_only_for_your_tenant(): void
    {
        $mine = Tenant::factory()->create();
        $theirs = Tenant::factory()->create();

        WhatsappTemplate::factory()->forTenant($mine)->create(['name' => 'mine_welcome']);
        WhatsappTemplate::factory()->forTenant($theirs)->create(['name' => 'their_welcome']);

        $this->actingAsTenantAdmin($mine);

        $this->get(route('tenant.templates.index'))
            ->assertOk()
            ->assertSee('mine_welcome')
            ->assertDontSee('their_welcome');
    }
}
