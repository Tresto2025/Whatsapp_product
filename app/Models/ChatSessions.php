<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToTenant;

class ChatSessions extends Model
{
    use BelongsToTenant;

    protected $table = 'chat_sessions';
    protected $fillable = [
        'doctor_id','phone','step','mode','data','completed'
    ];
}
