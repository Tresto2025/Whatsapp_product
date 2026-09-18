<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class DoctorTimings extends Model
{
    protected $table = 'doctor_timings';
    protected $fillable = [
        'doctor_id','day','slot_type','slot_time_gap','start_time','end_time','first_half_start','first_half_end','second_half_start','second_half_end','generated_slots'
    ];

}