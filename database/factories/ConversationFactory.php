<?php

namespace Database\Factories;

use App\Models\Contact;
use App\Models\Conversation;
use App\Models\Tenant;
use App\Models\WhatsappAccount;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Conversation>
 */
class ConversationFactory extends Factory
{
    protected $model = Conversation::class;

    public function definition(): array
    {
        $tenant = Tenant::factory()->create();

        return [
            'tenant_id' => $tenant->id,
            'contact_id' => Contact::factory()->forTenant($tenant),
            'whatsapp_account_id' => WhatsappAccount::factory()->forTenant($tenant),
            'status' => Conversation::STATUS_OPEN,
            'last_message_at' => now(),
            'unread_count' => 0,
        ];
    }

    /** Give the conversation, its contact and its number the same tenant. */
    public function forTenant(Tenant $tenant): static
    {
        return $this->state(fn () => [
            'tenant_id' => $tenant->id,
            'contact_id' => Contact::factory()->forTenant($tenant),
            'whatsapp_account_id' => WhatsappAccount::factory()->forTenant($tenant),
        ]);
    }
}
