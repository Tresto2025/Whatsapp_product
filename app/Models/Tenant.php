<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'status',
        'owner_user_id',
        'plan_id',
        'contact_email',
        'contact_phone',
        'settings',
    ];

    protected $casts = [
        'settings' => 'array',
    ];

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function owner(): ?User
    {
        return $this->owner_user_id ? User::find($this->owner_user_id) : null;
    }
}
