<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class TrainerSkills extends Model
{
    protected $table = 'trainer_skills';
    protected $fillable = [
        'trainer_type_id','skill_name','status'
    ];
}