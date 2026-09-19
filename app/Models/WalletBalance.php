<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToTenant;

class WalletBalance extends Model
{
    use BelongsToTenant;

    protected $table = 'wallet_balance';
    protected $fillable = [
        'doctor_id','total_recharged','wallet_balance','total_spent','status'
    ];
    
    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

}