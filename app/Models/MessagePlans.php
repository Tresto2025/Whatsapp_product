<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class MessagePlans extends Model
{
    protected $table = 'message_plans';
    protected $fillable = [
        'plan_name','no_of_messages','price','description','status','deleted_at'
    ];
    

}