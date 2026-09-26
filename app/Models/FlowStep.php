<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One action a flow performs. `FlowMatcher` (the MVP engine) only executes
 * send_text and send_template; the remaining action types are schema support
 * for the later multi-step engine (ask_question, save_attribute, add_tag,
 * record_response, handoff, end) and are not interpreted anywhere yet.
 */
class FlowStep extends Model
{
    use HasFactory, BelongsToTenant;

    public const ACTION_SEND_TEXT = 'send_text';
    public const ACTION_SEND_TEMPLATE = 'send_template';

    protected $fillable = [
        'tenant_id', 'flow_id', 'parent_step_id', 'order', 'action_type', 'payload',
    ];

    protected $casts = [
        'payload' => 'array',
    ];

    public function flow(): BelongsTo
    {
        return $this->belongsTo(ChatbotFlow::class, 'flow_id');
    }
}
