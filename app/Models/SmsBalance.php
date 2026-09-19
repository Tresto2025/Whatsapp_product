<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToTenant;

class SmsBalance extends Model
{
    use BelongsToTenant;

    protected $table = 'sms_balance';
    protected $fillable = [
        'doctor_id','total_sms','pending_sms','spent_sms','status','deleted_at'
    ];
    
    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

}