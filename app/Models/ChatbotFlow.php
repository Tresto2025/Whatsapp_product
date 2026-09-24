<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A no-code automation: one or more triggers, and what to do when one fires.
 *
 * MVP scope is deliberately narrow: a flow has exactly one top-level reply
 * step, not a branching tree. `flow_steps.parent_step_id` and the richer
 * action types (ask_question, save_attribute, handoff, ...) are schema
 * support for the multi-step engine, not implemented yet.
 */
class ChatbotFlow extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'chatbot_flows';

    protected $fillable = [
        'tenant_id', 'name', 'description', 'is_active', 'default_reply', 'priority',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function triggers(): HasMany
    {
        return $this->hasMany(FlowTrigger::class, 'flow_id');
    }

    public function steps(): HasMany
    {
        return $this->hasMany(FlowStep::class, 'flow_id');
    }
}
