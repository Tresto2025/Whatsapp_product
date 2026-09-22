<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A single WhatsApp message, inbound or outbound.
 *
 * `meta_message_id` is how a later delivery receipt finds this row: Meta sends
 * status updates (sent → delivered → read, or failed) on a separate webhook
 * that carries only that id.
 */
class Message extends Model
{
    use HasFactory, BelongsToTenant;

    public const IN = 'in';
    public const OUT = 'out';

    public const STATUS_QUEUED = 'queued';
    public const STATUS_SENT = 'sent';
    public const STATUS_DELIVERED = 'delivered';
    public const STATUS_READ = 'read';
    public const STATUS_FAILED = 'failed';

    protected $fillable = [
        'tenant_id', 'conversation_id', 'direction', 'type', 'body', 'template_id',
        'payload', 'meta_message_id', 'status', 'error',
        'sent_at', 'delivered_at', 'read_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
        'read_at' => 'datetime',
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function isInbound(): bool
    {
        return $this->direction === self::IN;
    }

    /**
     * Statuses only ever move forward. Meta can deliver receipts out of order,
     * and without this a late "sent" would overwrite an already-recorded
     * "read".
     */
    public function advanceStatus(string $status, ?\DateTimeInterface $at = null): bool
    {
        $rank = [
            self::STATUS_QUEUED => 0,
            self::STATUS_SENT => 1,
            self::STATUS_DELIVERED => 2,
            self::STATUS_READ => 3,
        ];

        if ($status === self::STATUS_FAILED) {
            $this->status = self::STATUS_FAILED;
            return true;
        }

        if (($rank[$status] ?? -1) <= ($rank[$this->status] ?? -1)) {
            return false;
        }

        $this->status = $status;
        $at = $at ?: now();

        match ($status) {
            self::STATUS_SENT => $this->sent_at = $this->sent_at ?: $at,
            self::STATUS_DELIVERED => $this->delivered_at = $this->delivered_at ?: $at,
            self::STATUS_READ => $this->read_at = $this->read_at ?: $at,
            default => null,
        };

        return true;
    }
}
