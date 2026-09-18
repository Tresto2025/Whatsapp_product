<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class SmsBalance extends Model
{
    protected $table = 'sms_balance';
    protected $fillable = [
        'doctor_id','total_sms','pending_sms','spent_sms','status','deleted_at'
    ];
    
    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

}