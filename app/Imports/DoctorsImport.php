<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class DoctorsImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * Map each row into a User (doctor).
     */
    public function model(array $row)
    {
        // Doctor create/update
        $doctor = User::updateOrCreate(
            [
                'email' => $row['email'],
                'phone' => $row['phone'],
            ],
            [
                'title'           => $row['title'],
                'first_name'      => $row['first_name'],
                'last_name'       => $row['last_name'],
                'password'        => Hash::make($row['password']),
                'show_password'   => $row['password'],
                'profession_type' => $row['profession_type'],
                'gender'          => $row['gender'],
                'city'            => $row['city'],
                'address'         => $row['address'],
                'pan_number'      => $row['pan_number'],
                'gst_number'      => $row['gst_number'],
                'start_time'      => $row['start_time'] ?? '10:00:00',
                'end_time'        => $row['end_time']   ?? '16:00:00',
            ]
        );
    
        // SMS Balance add only if NOT already added
        \App\Models\SmsBalance::firstOrCreate(
            ['doctor_id' => $doctor->id],
            [
                'total_sms'   => 500,
                'pending_sms' => 500,
                'spent_sms'   => 0,
                'status'      => 1,
            ]
        );
    
        return $doctor;
    }


    /**
     * Validation rules for each row.
     */
    public function rules(): array
    {
        return [
            '*.title'           => 'required|string|max:10',
            '*.first_name'      => 'required|string|max:255',
            '*.last_name'       => 'required|string|max:255',
            '*.email'           => 'required|email|unique:users,email',
            '*.phone'           => 'required|min:10|unique:users,phone',
            '*.password'        => 'required|min:6',
            '*.profession_type' => 'required|string|max:255',
            '*.gender'          => 'required|string|max:20',
            '*.city'            => 'required|string|max:255',
            '*.address'         => 'required|string|max:255',
        ];
    }
}
