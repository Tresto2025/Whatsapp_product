<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * What a conversation produced — the generic replacement for "appointments".
 *
 * A clinic books a consultation, a restaurant a table, a gym a trial: all the
 * same row with a different `type` and whatever the flow collected in `data`.
 */
class Response extends Model
{
    use HasFactory, BelongsToTenant;

    public const TYPE_BOOKING = 'booking';
    public const TYPE_ENQUIRY = 'enquiry';
    public const TYPE_INTEREST = 'interest';
    public const TYPE_LEAD = 'lead';

    protected $fillable = [
        'tenant_id', 'contact_id', 'conversation_id', 'flow_id', 'campaign_id',
        'type', 'subject', 'status', 'scheduled_for', 'data', 'handled_by', 'notes',
    ];

    protected $casts = [
        'data' => 'array',
        'scheduled_for' => 'datetime',
    ];

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }
}
