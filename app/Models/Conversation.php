<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * One contact's thread on one of the tenant's numbers.
 */
class Conversation extends Model
{
    use HasFactory, BelongsToTenant;

    public const STATUS_OPEN = 'open';
    public const STATUS_SNOOZED = 'snoozed';
    public const STATUS_CLOSED = 'closed';

    protected $fillable = [
        'tenant_id', 'contact_id', 'whatsapp_account_id', 'status',
        'assigned_user_id', 'last_message_at', 'unread_count',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
        'unread_count' => 'integer',
    ];

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(WhatsappAccount::class, 'whatsapp_account_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }
}
