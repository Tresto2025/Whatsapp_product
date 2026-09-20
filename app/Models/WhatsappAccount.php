<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use App\Models\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * A Meta WhatsApp Cloud API number connected by a tenant.
 *
 * access_token and app_secret use Laravel's encrypted casts, so they are
 * ciphertext at rest and never readable from a database dump. They are also
 * hidden from array/JSON serialisation so they cannot leak into a view or an
 * API response by accident.
 */
class WhatsappAccount extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'whatsapp_accounts';

    public const STATUS_PENDING = 'pending';
    public const STATUS_CONNECTED = 'connected';
    public const STATUS_FAILED = 'failed';
    public const STATUS_DISABLED = 'disabled';

    protected $fillable = [
        'tenant_id',
        'label',
        'phone_number_id',
        'waba_id',
        'display_phone_number',
        'meta_business_id',
        'access_token',
        'app_secret',
        'verify_token',
        'provider',
        'connection_status',
        'webhook_status',
        'is_default',
        'last_verified_at',
        'last_error',
    ];

    protected $casts = [
        'access_token' => 'encrypted',
        'app_secret' => 'encrypted',
        'is_default' => 'boolean',
        'last_verified_at' => 'datetime',
    ];

    protected $hidden = ['access_token', 'app_secret', 'verify_token'];

    /**
     * Resolve the account that owns an inbound phone_number_id.
     *
     * Deliberately ignores the tenant scope: at webhook time no tenant context
     * exists yet — this lookup is what establishes it.
     */
    public static function findByPhoneNumberId(?string $phoneNumberId): ?self
    {
        if (empty($phoneNumberId)) {
            return null;
        }

        return static::withoutGlobalScope(TenantScope::class)
            ->where('phone_number_id', $phoneNumberId)
            ->first();
    }

    public function isConnected(): bool
    {
        return $this->connection_status === self::STATUS_CONNECTED;
    }

    public function isUsable(): bool
    {
        return $this->isConnected() && !empty($this->access_token);
    }

    public function markConnected(?string $displayNumber = null): void
    {
        $this->forceFill([
            'connection_status' => self::STATUS_CONNECTED,
            'display_phone_number' => $displayNumber ?: $this->display_phone_number,
            'last_verified_at' => now(),
            'last_error' => null,
        ])->save();
    }

    public function markFailed(string $error): void
    {
        $this->forceFill([
            'connection_status' => self::STATUS_FAILED,
            'last_error' => Str::limit($error, 1000),
        ])->save();
    }

    /**
     * A display-safe fragment of the token, for confirming which credential is
     * stored without revealing it.
     */
    public function maskedToken(): string
    {
        $token = (string) $this->access_token;

        if (strlen($token) <= 8) {
            return str_repeat('•', max(strlen($token), 4));
        }

        return substr($token, 0, 4).str_repeat('•', 12).substr($token, -4);
    }

    public static function generateVerifyToken(): string
    {
        return Str::random(40);
    }
}
