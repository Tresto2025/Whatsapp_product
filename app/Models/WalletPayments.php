<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class WalletPayments extends Model
{
    protected $table = 'wallet_payments';
    protected $fillable = [
        'doctor_id','transaction_id','amount','payment_gateway','status'
    ];
    
    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

}