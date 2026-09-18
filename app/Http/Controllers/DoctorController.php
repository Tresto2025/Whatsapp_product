<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\DoctorService;
use App\Models\DoctorTimings;
use App\Models\SmsBalance;
use App\Models\Services;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Imports\DoctorsImport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Response;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class DoctorController extends Controller
{
    public function index(){
        $doctors_list = User::where('role', 2)->whereNull('deleted_at')->orderBy('first_name', 'ASC')->get();
        return view('dashboard.doctors.index', compact('doctors_list'));
    }

    public function create(){
        return view('dashboard.doctors.create');
    }

    public function store(Request $request)
{
    // ======================
    // Validation
    // ======================
    $validatedData = $request->validate([
        'title'           => 'required|string|max:10',
        'first_name'      => 'required|string|max:255',
        'last_name'       => 'required|string|max:255',
        'email'           => 'required|email|unique:users,email',
        'phone'           => 'required|min:10|unique:users,phone',
        'password'        => 'required|min:8',
        'profile_image'   => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        'gender'          => 'required|string|max:20',
        'city'            => 'nullable|string|max:255',
        'address'         => 'required|string|max:255',
        'pan_number'      => 'nullable|string|max:255',
        'gst_number'      => 'nullable|string|max:255',
        'services'        => 'required|array|max:10',
        'services.*'      => 'required|string|max:50',

        'available_days'  => 'required|array|min:1',
        'timings'         => 'required|array',
    ]);

    // ======================
    // Upload Profile Image
    // ======================
    $profilePath = null;
    if ($request->hasFile('profile_image')) {
        $profile = $request->file('profile_image');
        $profileName = time() . '_' . Str::slug(pathinfo($profile->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $profile->getClientOriginalExtension();
        $profile->move(public_path('uploads/doctors'), $profileName);
        $profilePath = 'uploads/doctors/' . $profileName;
    }

    // ======================
    // Create Doctor
    // ======================
    $doctor = User::create([
        'title'            => $request->title,
        'first_name'       => Str::ucfirst($request->first_name),
        'last_name'        => Str::ucfirst($request->last_name),
        'email'            => $request->email,
        'phone'            => $request->phone,
        'password'         => Hash::make($request->password),
        'show_password'    => $request->password,
        'profile_image'    => $profilePath,
        'role'             => 2,
        'profession_type'  => $request->profession_type,
        'gender'           => $request->gender,
        'city'             => $request->city,
        'address'          => $request->address,
        'pan_number'       => $request->pan_number,
        'gst_number'       => $request->gst_number,
    ]);

    // ======================
    // Save Services
    // ======================
    foreach ($request->services as $service) {
        DoctorService::create([
            'doctor_id'    => $doctor->id,
            'service_name' => strtoupper($service),
        ]);
    }

    // ======================
    // Prepare Time Slots
    // ======================
    foreach ($request->available_days as $day) {

        if (!isset($request->timings[$day])) {
            continue;
        }

        $timing = $request->timings[$day];
        $gap    = $timing['slot_time_gap'] ?? null;

        if (!$gap) continue;

        $gapSeconds = $gap * 60;

        // SINGLE SLOT
        if ($timing['slot_type'] === 'single') {

            $start = strtotime($timing['start_time']);
            $end   = strtotime($timing['end_time']);
            $slots = [];

            while ($start + $gapSeconds <= $end) {
                $slotStart = date('h:i A', $start);
                $slotEnd   = date('h:i A', $start + $gapSeconds);
                $slots[]   = "$slotStart - $slotEnd";
                $start += $gapSeconds;
            }

            $slots = array_slice($slots, 0, 10);

            DoctorTimings::create([
                'doctor_id'     => $doctor->id,
                'day'           => $day,
                'slot_type'     => 'single',
                'slot_time_gap' => $gap,
                'start_time'    => $timing['start_time'],
                'end_time'      => $timing['end_time'],
                'generated_slots' => json_encode($slots),
            ]);

        }

        // DOUBLE SLOT
        if ($timing['slot_type'] === 'double') {

            $allSlots = [];

            foreach (['first_half', 'second_half'] as $half) {

                if (empty($timing[$half.'_start']) || empty($timing[$half.'_end'])) {
                    continue;
                }

                $start = strtotime($timing[$half.'_start']);
                $end   = strtotime($timing[$half.'_end']);
                $slots = [];

                while ($start + $gapSeconds <= $end) {
                    $slotStart = date('h:i A', $start);
                    $slotEnd   = date('h:i A', $start + $gapSeconds);
                    $slots[]   = "$slotStart - $slotEnd";
                    $start += $gapSeconds;
                }

                $allSlots[$half] = array_slice($slots, 0, 10);
            }

            DoctorTimings::create([
                'doctor_id'         => $doctor->id,
                'day'               => $day,
                'slot_type'         => 'double',
                'slot_time_gap'     => $gap,
                'first_half_start'  => $timing['first_half_start'] ?? null,
                'first_half_end'    => $timing['first_half_end'] ?? null,
                'second_half_start' => $timing['second_half_start'] ?? null,
                'second_half_end'   => $timing['second_half_end'] ?? null,
                'generated_slots'   => json_encode($allSlots),
            ]);
        }
    }
    /*SmsBalance::create([
        'doctor_id'   => $doctor->id,
        'total_sms'   => 500,
        'pending_sms' => 500,
        'spent_sms'   => 0,
        'status'      => 1,
    ]);*/

    return redirect()->route('doctor.index')->with('success', 'Doctor created successfully.');
}






    public function details($id){
        $doctor = User::where('id', $id)->whereNull('deleted_at')->firstOrFail();
        return view('dashboard.doctors.view', compact('doctor'));
    }
    
    public function edit($id){
        $user = User::where('role', 2)->where('id', $id)->firstOrFail();
        return view('dashboard.doctors.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'id'              => 'required|exists:users,id',
            'title'           => 'required|string|max:10',
            'first_name'      => 'required|string|max:255',
            'last_name'       => 'required|string|max:255',
            'phone'           => 'required|min:10|unique:users,phone,' . $request->id,
            'email'           => 'required|email|unique:users,email,' . $request->id,
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
    
        return redirect()->route('doctor.index')->with('success', 'Doctor updated successfully.');
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


    public function updateDoctorStatus(Request $request){
        $doctor = User::findOrFail($request->id);
        $doctor->status = $doctor->status == 1 ? 0 : 1;
        $doctor->save();
        return response()->json(['status' => 1, 'message' => 'Doctor status updated successfully.']);
    }

    // Filter
    public function filterDoctors(Request $request){
        $doctors = User::query()->where('role', 2)->whereNull('deleted_at');
        if ($request->search == "active") {
            $doctors->where('status', 1);
        } elseif (in_array(strtolower($request->search), ["inactive", "in-active", "in active"])) {
            $doctors->where('status', 0);
        } elseif (!empty($request->search)) {
            $search = $request->search;
            $doctors->where(function ($q) use ($search) {
                $q->where('first_name', 'LIKE', "%$search%")
                  ->orWhere('last_name', 'LIKE', "%$search%")
                  ->orWhere('email', 'LIKE', "%$search%")
                  ->orWhere('phone', 'LIKE', "%$search%")
                  ->orWhere('city', 'LIKE', "%$search%");
            });
        }

        $doctors_list = $doctors->orderBy('first_name', 'ASC')->get();
        return view('dashboard.doctors.filter', compact('doctors_list'));
    }

    public function destroy($id){
        User::where('id', $id)->update(['deleted_at' => now()]);
        return response()->json(['success' => 'Doctor deleted successfully.']);
    }
    
    public function downloadQr($id){
        $doctor = User::findOrFail($id);
        $doctorUrl = route('doctor.details', $doctor->id);
        $qrCode = QrCode::format('png')->size(300)->errorCorrection('H')->generate($doctorUrl);
        $fileName = 'doctor-' . $doctor->id . '-qrcode.png';
        return Response::streamDownload(function () use ($qrCode) {
            echo $qrCode;
        }, $fileName, [
            'Content-Type' => 'image/png',
            'Content-Disposition' => 'attachment; filename="'.$fileName.'"',
        ]);
    }
    
    public function downloadQrPdf($id){
        $doctor = User::findOrFail($id);
        $qrBgImage = 'qr-bg-1.jpeg';
        //return view('dashboard.pdf.doctor_qr', ['doctor' => $doctor, 'qrBgImage' => $qrBgImage]);
        $pdf = Pdf::loadView('dashboard.pdf.doctor_qr', compact('doctor', 'qrBgImage'));
        //return $pdf->stream('QRcode.pdf');
        return $pdf->download('doctor-' . $doctor->id . '-qrcode.pdf');
    }
    
    public function toggleBooking(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required',
            'booking_enabled' => 'required'
        ]);
    
        $doctor = User::findOrFail($request->doctor_id);
        $doctor->booking_enabled = $request->booking_enabled;
        $doctor->save();
    
        return response()->json(['success' => true]);
    }
    
    public function showImportForm(){
        return view('dashboard.doctors.import'); 
    }
        
    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|mimes:csv,txt,xlsx|max:2048',
        ]);
    
        try {
            Excel::import(new DoctorsImport, $request->file('csv_file'));
            return back()->with('success', 'Doctors imported successfully!');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            return back()->with('error', 'Some rows failed validation.')->with('details', $failures);
        }
    }
    
    public function search(Request $request){
        $search = $request->query('q');
        $data = Services::where('service_name', 'LIKE', "%$search%")->where('status', 1)->whereNull('deleted_at')->limit(20)->pluck('service_name');
        return response()->json($data);
    }

}
