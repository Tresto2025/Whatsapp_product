<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ChatSessions extends Model
{
    protected $table = 'chat_sessions';
    protected $fillable = [
        'doctor_id','phone','step','mode','data','completed'
    ];
}
