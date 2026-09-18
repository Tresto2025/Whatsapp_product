<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class MessagePrices extends Model
{
    protected $table = 'message_prices';
    protected $fillable = [
        'price_per_message'
    ];
}
