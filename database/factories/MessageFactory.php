<?php

namespace Database\Factories;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Message>
 */
class MessageFactory extends Factory
{
    protected $model = Message::class;

    public function definition(): array
    {
        $conversation = Conversation::factory()->create();

        return [
            'tenant_id' => $conversation->tenant_id,
            'conversation_id' => $conversation->id,
            'direction' => Message::IN,
            'type' => 'text',
            'body' => fake()->sentence(),
            'status' => Message::STATUS_DELIVERED,
            'sent_at' => now(),
        ];
    }

    /** Attach to an existing conversation, inheriting its tenant. */
    public function forConversation(Conversation $conversation): static
    {
        return $this->state(fn () => [
            'tenant_id' => $conversation->tenant_id,
            'conversation_id' => $conversation->id,
        ]);
    }

    public function inbound(): static
    {
        return $this->state(fn () => ['direction' => Message::IN]);
    }

    public function outbound(): static
    {
        return $this->state(fn () => [
            'direction' => Message::OUT,
            'status' => Message::STATUS_SENT,
        ]);
    }
}
