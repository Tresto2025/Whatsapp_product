<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\CustomResetPassword;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
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
    
    public function timings() {
        return $this->hasOne(DoctorTimings::class,'doctor_id');
    }
    
    public function doctortimings(){
        return $this->hasOne('App\Models\DoctorTimings','doctor_id','id');
    }

}
