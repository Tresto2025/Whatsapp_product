<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Appointments extends Model
{
    protected $table = 'appointments';
    protected $fillable = [
        'doctor_id','service_type','phone','name','date','time','start_time','new_date','new_time','purpose','status','is_reschedule','cancel_reason'
    ];
    
    public function getStartTimeAttribute(){
        if (!$this->time) {
            return null;
        }

        // Split by " - " and take first part
        $parts = explode(' - ', $this->time);
        if (count($parts) === 0) {
            return null;
        }

        try {
            return Carbon::createFromFormat('h:i A', trim($parts[0]));
        } catch (\Exception $e) {
            return null;
        }
    }
    
    public function doctor_detail(){
        return $this->hasOne('App\Models\User','id','doctor_id');
    }
}