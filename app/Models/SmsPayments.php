<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToTenant;

class SmsPayments extends Model
{
    use BelongsToTenant;

    protected $table = 'sms_payments';
    protected $fillable = [
        'doctor_id','plan_id','transaction_id','amount'
    ];
    
    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

}