<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class TrainerProfession extends Model
{
    protected $table = 'trainer_profession';
    protected $fillable = [
        'profession','status'
    ];
    
    public function trainer_skills(){
        return $this->hasMany('App\Models\TrainerSkills','trainer_type_id','id');
    }
}
