<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * An operator of the platform. Not a customer — the people a tenant messages
 * are Contacts.
 *
 * Deliberately not tenant-scoped: authentication looks users up before any
 * tenant context exists. Isolation is enforced by ResolveTenant pinning the
 * request to $user->tenant_id.
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public const ROLE_SUPER_ADMIN = 0;   // platform owner, no tenant
    public const ROLE_TENANT_ADMIN = 1;  // owns a workspace
    public const ROLE_AGENT = 2;         // works the inbox

    protected $fillable = [
        'tenant_id',
        'name',
        'email',
        'password',
        'role',
        'phone',
        'avatar_path',
        'is_active',
        'last_seen_at',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_seen_at' => 'datetime',
        'is_active' => 'boolean',
        'password' => 'hashed',
        'role' => 'integer',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === self::ROLE_SUPER_ADMIN;
    }

    public function isTenantAdmin(): bool
    {
        return $this->role === self::ROLE_TENANT_ADMIN;
    }

    public function isAgent(): bool
    {
        return $this->role === self::ROLE_AGENT;
    }

    /** Anyone who may administer a workspace. */
    public function administersWorkspace(): bool
    {
        return $this->isSuperAdmin() || $this->isTenantAdmin();
    }
}
