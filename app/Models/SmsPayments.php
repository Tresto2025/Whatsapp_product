<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class SmsPayments extends Model
{
    protected $table = 'sms_payments';
    protected $fillable = [
        'doctor_id','plan_id','transaction_id','amount'
    ];
    
    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

}