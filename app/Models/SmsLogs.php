<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToTenant;

class SmsLogs extends Model
{
    use BelongsToTenant;

    protected $table = 'sms_logs';
    protected $fillable = [
        'doctor_id','sms_to','sms_from','sid','body','status','broadcast_id'
    ];
    
    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

}