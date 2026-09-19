<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToTenant;

class WalletPayments extends Model
{
    use BelongsToTenant;

    protected $table = 'wallet_payments';
    protected $fillable = [
        'doctor_id','transaction_id','amount','payment_gateway','status'
    ];
    
    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

}