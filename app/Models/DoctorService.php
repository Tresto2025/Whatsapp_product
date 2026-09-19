<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToTenant;

class DoctorService extends Model
{
    use BelongsToTenant;

    protected $table = 'doctor_service';
    protected $fillable = [
        'doctor_id','service_id','service_name'
    ];

}