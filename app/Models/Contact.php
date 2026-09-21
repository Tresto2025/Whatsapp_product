<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Someone a tenant talks to on WhatsApp.
 *
 * `wa_id` is the phone number exactly as Meta sends it: E.164 digits with no
 * leading '+'. Normalising on the way in matters — the same person arriving
 * as "+91 98…" from an import and "9198…" from a webhook must be one contact.
 */
class Contact extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'wa_id', 'name', 'profile_name', 'email', 'locale',
        'attributes', 'opted_in_at', 'opted_out_at', 'last_inbound_at', 'last_outbound_at',
    ];

    protected $casts = [
        'attributes' => 'array',
        'opted_in_at' => 'datetime',
        'opted_out_at' => 'datetime',
        'last_inbound_at' => 'datetime',
        'last_outbound_at' => 'datetime',
    ];

    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function displayName(): string
    {
        return $this->name ?: ($this->profile_name ?: $this->wa_id);
    }

    public function hasOptedOut(): bool
    {
        return $this->opted_out_at !== null;
    }

    /** Digits only, so one person is one contact however the number was written. */
    public static function normaliseWaId(string $raw): string
    {
        return ltrim(preg_replace('/\D+/', '', $raw), '0');
    }
}
