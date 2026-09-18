<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class UserInfo extends Model
{
    protected $table = 'user_info';
    protected $fillable = [
        'user_id','height','weight','goals','target_weight','target_date','exercise_level','diet_preference','daily_water_intake','sleep_hours','supplements_taken','food_allergies'
    ];
}