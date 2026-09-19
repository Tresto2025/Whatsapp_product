<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToTenant;

class BroadcastMessages extends Model
{
    use BelongsToTenant;

    protected $table = 'broadcast_messages';
    protected $fillable = [
        'send_by','send_to','title','description','image','total_send_messages','status'
    ];
    
    public function doctor()
    {
        return $this->belongsTo(User::class, 'send_by');
    }

}