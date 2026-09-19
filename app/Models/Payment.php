<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToTenant;

class Payment extends Model
{
    use HasFactory, BelongsToTenant;
    protected $table = 'payments';

    protected $fillable = ['name', 'email', 'phone', 'amount', 'order_id', 'razorpay_payment_id', 'status'];
}
