<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A mirror of one Meta message template.
 *
 * Templates are authored and approved on Meta, never here. We mirror them so
 * campaigns and flows can be built against a known-approved list, and so a send
 * is never attempted against a rejected or paused template.
 */
class WhatsappTemplate extends Model
{
    use HasFactory, BelongsToTenant;

    public const STATUS_APPROVED = 'approved';
    public const STATUS_PENDING = 'pending';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_PAUSED = 'paused';

    public const CATEGORY_UTILITY = 'utility';
    public const CATEGORY_MARKETING = 'marketing';
    public const CATEGORY_AUTHENTICATION = 'authentication';

    protected $fillable = [
        'tenant_id', 'whatsapp_account_id', 'meta_template_id', 'name', 'language',
        'category', 'status', 'header_type', 'body', 'footer', 'buttons', 'variables',
        'rejection_reason', 'synced_at',
    ];

    protected $casts = [
        'buttons' => 'array',
        'variables' => 'array',
        'synced_at' => 'datetime',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(WhatsappAccount::class, 'whatsapp_account_id');
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /** How many positional {{n}} body parameters this template expects. */
    public function variableCount(): int
    {
        return is_array($this->variables) ? count($this->variables) : 0;
    }
}
