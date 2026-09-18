<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\DoctorService;
use App\Models\DoctorTimings;
use Auth;

class ProfileController extends Controller
{
    public function index(){
        $userId = Auth::user()->id;
        //echo "data";die;
        $admin = User::where('id', $userId)->first();
        return view('dashboard.profile.index', ['admin' => $admin]);
    }
    public function update(Request $request){
        $validatedData = $request->validate([
            'name' => 'required',
            'phone' => 'min:10',
            'email' => 'required|email|unique:users,email,' . $request->id,
        ]);
        $user = User::where('id', $request->id)->update([
            'name' => Str::ucfirst($request->name),
            'phone' => $request->phone,
            'email' => $request->email,
        ]);
        return redirect()->route('profile.index')->with('success', 'Profile Updated successfully.');
    }
    public function UpdatePassword(Request $request){
        $validatedData = $request->validate([
            'password' => 'min:8|required_with:confirm_password|same:confirm_password',
            'confirm_password' => 'min:8',
            'current_password' => ['required', function ($attribute, $value, $fail) {
                if (!\Hash::check($value, Auth::user()->password)) {
                    return $fail(__('The current password is incorrect.'));
                }
            }]
        ]);
        $user_details = User::where('id', $request->id)->update([
            'password' => Hash::make($request->password),
            'show_password' => $request->password
        ]);
        return redirect()->route('profile.index')->with('success', 'Profile Updated successfully.');
    }
    
    public function DoctorProfile(){
        $userId = Auth::user()->id;
        //echo "data";die;
        $user = User::where('id', $userId)->first();
        return view('doctor-dashboard.profile.index', ['user' => $user]);
    }
    
    public function UpdateDoctorProfile(Request $request)
    {
        $validatedData = $request->validate([
            'id'              => 'required|exists:users,id',
            'title'           => 'required|string|max:10',
            'first_name'      => 'required|string|max:255',
            'last_name'       => 'required|string|max:255',
            'phone'           => 'required|min:10|unique:users,phone,' . $request->id,
            'email'           => 'required|email|unique:users,email,' . $request->id,
            'profession_type' => 'required|string|max:255',
            'gender'          => 'required|string|max:20',
            'city'            => 'nullable|string|max:255',
            'address'         => 'required|string|max:255',
            'pan_number'      => 'nullable|string|max:255',
            'gst_number'      => 'nullable|string|max:255',
    
            'available_days'  => 'nullable|array',
            'timings'         => 'nullable|array',
        ]);
    
        // ======================
        // Get Doctor
        // ======================
        $doctor = User::where('role', 2)->where('id', $request->id)->firstOrFail();
    
        // ======================
        // Handle Profile Image
        // ======================
        if ($request->hasFile('profile_image')) {
            $profile = $request->file('profile_image');
            $profileName = time() . '_' . Str::slug(pathinfo($profile->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $profile->getClientOriginalExtension();
            $profile->move(public_path('uploads/doctors'), $profileName);
            $doctor->profile_image = 'uploads/doctors/' . $profileName;
        }
    
        // ======================
        // Update Doctor Info
        // ======================
        $doctor->update([
            'title'           => $request->title,
            'first_name'      => Str::ucfirst($request->first_name),
            'last_name'       => Str::ucfirst($request->last_name),
            'email'           => $request->email,
            'phone'           => $request->phone,
            'profession_type' => $request->profession_type,
            'gender'          => $request->gender,
            'city'            => $request->city,
            'address'         => $request->address,
            'pan_number'      => $request->pan_number,
            'gst_number'      => $request->gst_number,
        ]);
    
        // ======================
        // Update Services
        // ======================
        if ($request->filled('services')) {
            DoctorService::where('doctor_id', $doctor->id)->delete();
            foreach ($request->services as $service) {
                DoctorService::create([
                    'doctor_id'    => $doctor->id,
                    'service_name' => strtoupper($service),
                ]);
            }
        }
    
        // ======================
        // Update Doctor Timings
        // ======================
        DoctorTimings::where('doctor_id', $doctor->id)->delete();
        if ($request->available_days) {
            foreach ($request->available_days as $day) {
                $dayTiming = $request->timings[$day] ?? null;
                if (!$dayTiming) continue;
                $timing = new DoctorTimings();
                $timing->doctor_id = $doctor->id;
                $timing->day       = $day;
                $timing->slot_type = $dayTiming['slot_type'] ?? null;
                $timing->slot_time_gap = $dayTiming['slot_time_gap'] ?? null;
                $generatedSlots = [];
                if ($timing->slot_type == 'single') {
                    $timing->start_time = $dayTiming['start_time'] ?? null;
                    $timing->end_time   = $dayTiming['end_time'] ?? null;
                    $start = strtotime($timing->start_time);
                    $end   = strtotime($timing->end_time);
                    $gap   = $timing->slot_time_gap * 60;
                    while ($start + $gap <= $end) {
                        $slotStart = date('h:i', $start);
                        $slotEnd   = date('h:i A', $start + $gap);
                        $generatedSlots[] = "$slotStart - $slotEnd";
                        $start += $gap;
                    }
                } elseif ($timing->slot_type == 'double') {
                    $timing->first_half_start  = $dayTiming['first_half_start'] ?? null;
                    $timing->first_half_end    = $dayTiming['first_half_end'] ?? null;
                    $timing->second_half_start = $dayTiming['second_half_start'] ?? null;
                    $timing->second_half_end   = $dayTiming['second_half_end'] ?? null;
                    foreach (['first_half','second_half'] as $half) {
                        $halfStart = strtotime($dayTiming[$half.'_start']);
                        $halfEnd   = strtotime($dayTiming[$half.'_end']);
                        $gap       = $timing->slot_time_gap * 60;
                        while ($halfStart + $gap <= $halfEnd) {
                            $slotStart = date('h:i', $halfStart);
                            $slotEnd   = date('h:i A', $halfStart + $gap);
                            $generatedSlots[] = "$slotStart - $slotEnd";
                            $halfStart += $gap;
                        }
                    }
                }
                $timing->generated_slots = json_encode(array_slice($generatedSlots, 0, 10));
                $timing->save();
            }
        }
    
        return redirect()->route('doctor-my-profile')->with('success', 'Profile updated successfully.');
    }
    
    private function updateTwilioTemplate(User $doctor, array $items, string $friendlyNamePrefix, string $twilioSidField, string $type = "option")
    {
        $twilioAccountSid = config('services.twilio.sid');
        $twilioAuthToken  = config('services.twilio.token');
        $twilioApiUrl     = "https://content.twilio.com/v1/Content";
    
        // Delete old template if exists
        if (!empty($doctor->{$twilioSidField})) {
            $deleteUrl = $twilioApiUrl . "/" . $doctor->{$twilioSidField};
            $ch = curl_init($deleteUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
            curl_setopt($ch, CURLOPT_USERPWD, $twilioAccountSid . ":" . $twilioAuthToken);
            curl_exec($ch);
            curl_close($ch);
        }
    
        // Prepare new items
        $templateItems = [];
        foreach ($items as $item) {
            $templateItems[] = [
                "item"        => $item,
                "description" => "",
                "id"          => $item
            ];
        }
    
        // Body and Button text (same as create)
        $body   = "Select your option";
        $button = "Select";
    
        if (Str::contains($friendlyNamePrefix, 'service') || $type === "service") {
            $body   = "Please select the Service type";
            $button = "Select Service";
        } elseif (Str::contains($friendlyNamePrefix, 'timing') || $type === "timing") {
            $body   = "Select your time for appointment";
            $button = "Select Time";
        }
    
        $postData = [
            "friendly_name" => $friendlyNamePrefix . "_" . Str::slug($doctor->first_name . $doctor->last_name, '_') . '_' . time(),
            "language"      => "en",
            "types" => [
                "twilio/list-picker" => [
                    "body"   => $body,
                    "button" => $button,
                    "items"  => $templateItems
                ]
            ]
        ];
    
        $ch = curl_init($twilioApiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $twilioAccountSid . ":" . $twilioAuthToken);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
    
        if ($httpCode == 201) {
            $result = json_decode($response, true);
            if (isset($result['sid'])) {
                $doctor->{$twilioSidField} = $result['sid'];
                $doctor->save();
            }
        } else {
            \Log::error("{$friendlyNamePrefix} Twilio Update Error: " . $response);
        }
    }
    
    public function DoctorPassword(){
        $userId = Auth::user()->id;
        //echo "data";die;
        $user = User::where('id', $userId)->first();
        return view('doctor-dashboard.profile.password', ['user' => $user]);
    }
    
    public function UpdateDoctorPassword(Request $request){
        $validatedData = $request->validate([
            'password' => 'min:8|required_with:confirm_password|same:confirm_password',
            'confirm_password' => 'min:8',
            'current_password' => ['required', function ($attribute, $value, $fail) {
                if (!\Hash::check($value, Auth::user()->password)) {
                    return $fail(__('The current password is incorrect.'));
                }
            }]
        ]);
        $user_details = User::where('id', $request->id)->update([
            'password' => Hash::make($request->password),
            'show_password' => $request->password
        ]);
        return redirect()->route('doctor-my-password')->with('success', 'Password Updated successfully.');
    }

}