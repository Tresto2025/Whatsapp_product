<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Tenant;
use App\Notifications\CustomResetPassword;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Role values (legacy integer column).
     *   0 = platform super admin (SaaS owner)
     *   1 = tenant admin (client business admin)
     *   2 = tenant staff / doctor
     */
    public const ROLE_SUPER_ADMIN = 0;
    public const ROLE_TENANT_ADMIN = 1;
    public const ROLE_TENANT_STAFF = 2;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tenant_id',
        'title','first_name','last_name',
        'email',
        'password','show_password','phone','profile_image',
        'experience','city','role','profession_type','gender',
        'address','tax_details','pan_number','gst_number','status',
        'booking_enabled','start_time','end_time','appointment_mode','service_template_id','timing_template_id',
        'slot_type','slot_gap','timing_template_id_1','timing_template_id_2'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomResetPassword($token, $this));
    }

    /*
    |--------------------------------------------------------------------------
    | Tenancy & roles
    |--------------------------------------------------------------------------
    | User is intentionally NOT tenant-scoped via a global scope: authentication
    | and password-reset lookups run before a tenant context exists. Scope user
    | listings explicitly with ->where('tenant_id', ...) inside tenant panels.
    */

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function isSuperAdmin(): bool
    {
        return (int) $this->role === self::ROLE_SUPER_ADMIN;
    }

    public function isTenantAdmin(): bool
    {
        return (int) $this->role === self::ROLE_TENANT_ADMIN;
    }

    public function isTenantStaff(): bool
    {
        return (int) $this->role === self::ROLE_TENANT_STAFF;
    }
    
    public function timings() {
        return $this->hasOne(DoctorTimings::class,'doctor_id');
    }
    
    public function doctortimings(){
        return $this->hasOne('App\Models\DoctorTimings','doctor_id','id');
    }

}
