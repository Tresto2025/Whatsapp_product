<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Services extends Model
{
    protected $table = 'services';
    protected $fillable = [
        'service_name','status','deleted_at'
    ];

}