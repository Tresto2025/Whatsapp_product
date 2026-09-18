<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointments;
use App\Models\DoctorService;
use App\Models\DoctorTimings;
use App\Models\User;
use App\Models\SmsLogs;
use App\Models\SmsBalance;
use App\Models\WalletBalance;
use App\Models\MessagePrices;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\Log;

class DoctorAppointmentController extends Controller
{
    public function index(){
        
        $doctorId = Auth::id();
        $today = Carbon::today()->toDateString();
    
        Appointments::where('doctor_id', $doctorId)->where('date', '<', $today)->whereIn('status', [1, 2])->update(['status' => 4]);
    
        $appointments = Appointments::where('doctor_id', $doctorId)
            ->orderByRaw("
                CASE
                    WHEN date = ? AND status = 1 THEN 1
                    WHEN date > ? AND status = 1 THEN 2
                    ELSE 3
                END
            ", [$today, $today])->orderByRaw("CASE WHEN status = 1 THEN date END ASC")->orderByRaw("CASE WHEN status = 1 THEN start_time END ASC")->orderBy('date','desc')->orderBy('start_time','desc')->get();
    
        $dates = [];
        $doctorId = Auth::id();
        $timings = DoctorTimings::where('doctor_id', $doctorId)->get()->keyBy(function($item){
            return strtolower($item->day);
        });
        for ($i = 0; $i < 7; $i++) {
            $date = Carbon::today()->addDays($i);
            $dayName = strtolower($date->format('l'));
            if (isset($timings[$dayName])) {
                $dates[] = [
                    'id' => $date->format('Y-m-d'),
                    'label' => $date->format('D, d M Y')
                ];
            }
        }
    
        $doctor = User::find($doctorId);
        $timeSlots = [];
        if ($doctor) {
            $timing = DoctorTimings::where('doctor_id', $doctorId)->first();
            if ($timing) {
                $timeSlots = $this->generateTimeSlots($timing);
            }
        }
        return view('doctor-dashboard.appointments.index', compact('appointments','dates','timeSlots'));
    }
    
    private function generateTimeSlots($timing){
        $slots = [];
    
        $gap = $timing->slot_time_gap;
    
        if ($timing->slot_type == 'single') {
            $slots = $this->generateSlotsBetween($timing->start_time, $timing->end_time, $gap);
        } else if ($timing->slot_type == 'double') {
            $first = $this->generateSlotsBetween($timing->first_half_start, $timing->first_half_end, $gap);
            $second = $this->generateSlotsBetween($timing->second_half_start, $timing->second_half_end, $gap);
            $slots = array_merge($first, $second);
        }
    
        return $slots;
    }
    
    private function generateSlotsBetween($startTime, $endTime, $gap){
        $slots = [];
        $start = Carbon::createFromFormat('H:i:s', $startTime);
        $end = Carbon::createFromFormat('H:i:s', $endTime);
    
        while($start->lt($end)){
            $slotStart = $start->format('h:i A');
            $slotEnd = $start->copy()->addMinutes($gap)->format('h:i A');
            if (Carbon::createFromFormat('h:i A', $slotEnd)->gt($end)) break;
            $slots[] = "$slotStart - $slotEnd";
            $start->addMinutes($gap);
        }
        return $slots;
    }
    
    public function newgetSlots(Request $request){
        $doctorId = Auth::id();
        $date = $request->date;
        $dayName = strtolower(Carbon::parse($date)->format('l'));
        $timing = DoctorTimings::where('doctor_id', $doctorId)->where('day', $dayName)->first();
        if (!$timing) {
            return response()->json([]);
        }
        $slots = $this->generateTimeSlots($timing);
        return response()->json($slots);
    }

    
    public function filterAppointments(Request $request){
        $doctorId = Auth::id();
        $today = Carbon::today()->toDateString();
        $search = $request->get('search');
        $date = $request->get('date');
    
        $appointments = Appointments::where('doctor_id', $doctorId)
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->when($date, function ($query, $date) {
                $query->whereDate('date', $date); 
            })
            ->orderByRaw("
                CASE
                    WHEN date = ? AND status = 1 THEN 1
                    WHEN date > ? AND status = 1 THEN 2
                    ELSE 3
                END
            ", [$today, $today])
            ->orderByRaw("
                CASE
                    WHEN status = 1 THEN date
                END ASC
            ")
            ->orderByRaw("
                CASE
                    WHEN status = 1 THEN start_time
                END ASC
            ")
            ->orderBy('date', 'desc')
            ->orderBy('start_time', 'desc')
            ->get();
            
        $dates = $this->getNext7Days();
        $doctor = User::find($doctorId);
        $timeSlots = [];
        if ($doctor && $doctor->start_time && $doctor->end_time) {
            $timeSlots = $this->generateDoctorSlots($doctor->start_time, $doctor->end_time);
        }
        return view('doctor-dashboard.appointments.filter', compact('appointments', 'dates', 'timeSlots'));
    }

    public function Patientdetails($id){
        $detail = Appointments::where('id', $id)->first();
        return view('doctor-dashboard.appointments.view', compact('detail'));
    }
    
    /*public function cancelAppointment($id){
        $appointment = Appointments::find($id);
        if (!$appointment) {
            return response()->json(['error' => 'Appointment not found.'], 404);
        }
        $appointment->status = 0;
        $appointment->save();
        
        try {
            $sid    = env('TWILIO_SID');
            $token  = env('TWILIO_AUTH_TOKEN');
            $from   = env('TWILIO_WHATSAPP_FROM');
            $to     = "whatsapp:" . $appointment->phone;
    
            $client = new Client($sid, $token);
            
            $appointment = Appointments::where('id', $appointment->id)->with('doctor_detail')->first();
        $doctorName = $appointment->doctor_detail->first_name . ' ' . $appointment->doctor_detail->last_name;
        $address = $appointment->doctor_detail->address ?? 'N/A';
        $formattedDate = date('d-M-Y', strtotime($appointment->date));
        $doctorProfession = $appointment->profession_type ?? 'N/A';
               
        $message = "Hello {$appointment->name},\n\n".
               "Your appointment has been *cancelled* by the doctor.\n\n".
               "*Here are your appointment details:*\n".
               "*Doctor* : {$doctorName} ({$doctorProfession})\n".
               "*Service* : {$appointment->service_type}\n".
               "*Purpose* : {$appointment->purpose}\n".
               "*Date* : {$formattedDate}\n".
               "*Time* : {$appointment->time}\n\n".
               "*Address* : {$address}";
    
            $client->messages->create($to, [
                'from' => $from,
                'body' => $message
            ]);
        } catch (\Exception $e) {
            // Log error if sending fails
            \Log::error('WhatsApp message send failed: ' . $e->getMessage());
        }

        return response()->json(['success' => 'Appointment cancelled successfully.']);
    }*/
    
    public function cancelAppointment($id)
    {
        $appointment = Appointments::where('id', $id)->with('doctor_detail')->first();
        if (!$appointment) {
            return response()->json(['error' => 'Appointment not found.'], 404);
        }
        
        $isSameDay = Carbon::parse($appointment->updated_at)->isToday();
        $price  = optional(MessagePrices::first())->price_per_message ?? 0;
        $wallet = WalletBalance::where('doctor_id', $appointment->doctor_id)->first();
        $isPaidMessage = false;
        if (!$isSameDay) {
            if (!$wallet || $wallet->wallet_balance < $price) {
                return response()->json(['error' => 'Your wallet balance is low. Please recharge.'], 400);
            }
            $wallet->decrement('wallet_balance', $price);
            $wallet->increment('total_spent', $price);
            $isPaidMessage = true;
            \Log::info("Wallet deducted for cancel. Remaining balance: " . $wallet->wallet_balance);
        } else {
            \Log::info("Same day cancel - message sent free.");
        }
    
        $appointment->status = 0;
        $appointment->save();
    
        try {
    
            $doctorName = $appointment->doctor_detail->first_name . ' ' . $appointment->doctor_detail->last_name;
            $doctorProfession = $appointment->profession_type ?? 'N/A';
            $address = $appointment->doctor_detail->address ?? 'N/A';
            $formattedDate = date('d M Y', strtotime($appointment->date));
    
            $message =
                "Hello {$appointment->name},\n\n" .
                "*Your appointment has been cancelled by the doctor.*\n\n" .
                "*Doctor:* Dr. {$doctorName} ({$doctorProfession})\n" .
                "*Service:* {$appointment->service_type}\n" .
                "*Purpose:* {$appointment->purpose}\n" .
                "*Date:* {$formattedDate}\n" .
                "*Time:* {$appointment->time}\n" .
                "*Location:* {$address}\n\n" .
                "If needed, you can book a new appointment anytime.";
    
            $cleanNumber = $appointment->phone;
            $this->sendWhatsAppMessage($cleanNumber, $message);
            
            if ($isPaidMessage) {
                SmsLogs::create([
                    'doctor_id'   => $appointment->doctor_id,
                    'sms_to'      => $cleanNumber,
                    'sms_from'    => 'Meta WhatsApp',
                    'sid'         => null,
                    'body'        => $message,
                    'status'      => 'sent',
                    'broadcast_id'=> 9000000001,
                    'title'       => 'Appointment Cancelled',
                    'description' => $message,
                ]);
            }
    
        } catch (\Exception $e) {
            \Log::error('WhatsApp message failed: ' . $e->getMessage());
        }
    
        return response()->json([
            'success' => 'Appointment cancelled successfully.'
        ]);
    }
    
    private function sendWhatsAppMessage($to, $message)
    {
        $token = env('WHATSAPP_TOKEN');
        $phone_number_id = env('PHONE_NUMBER_ID');
    
        $url = "https://graph.facebook.com/v22.0/{$phone_number_id}/messages";
    
        $payload = [
            "messaging_product" => "whatsapp",
            "to" => $to,
            "type" => "text",
            "text" => [
                "body" => $message
            ]
        ];
    
        $ch = curl_init($url);
    
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer {$token}",
            "Content-Type: application/json"
        ]);
    
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
        $response = curl_exec($ch);
        curl_close($ch);
    
        return $response;
    }
    
    public function rescheduleAppointment(Request $request)
    {
        $request->validate([
            'appointment_id' => 'required|exists:appointments,id',
            'date'           => 'required|date_format:Y-m-d',
            'time'           => 'required|string',
        ]);
    
        $appointment = Appointments::where('id', $request->appointment_id)->with('doctor_detail')->first();
        if (!$appointment) {
            return response()->json(['error' => 'Appointment not found.'], 404);
        }
    
        $isSameDay = Carbon::parse($appointment->updated_at)->isToday();
        $price = optional(MessagePrices::first())->price_per_message ?? 0;
        $wallet = WalletBalance::where('doctor_id', $appointment->doctor_id)->first();
        $isPaidMessage = false;
        if (!$isSameDay) {
            if (!$wallet || $wallet->wallet_balance < $price) {
                return response()->json(['error' => 'Your wallet balance is low. Please recharge.'], 400);
            }
            $wallet->decrement('wallet_balance', $price);
            $wallet->increment('total_spent', $price);
            $isPaidMessage = true;
            \Log::info("Wallet deducted for reschedule. Remaining balance: " . $wallet->wallet_balance);
    
        } else {
            \Log::info("Same day reschedule - message sent free.");
        }
    
        $raw = $request->time;
        $parts = explode('-', $raw);
        $startTimeString = trim($parts[0]);
    
        try {
            $startTime = Carbon::parse($startTimeString)->format('H:i:s');
        } catch (\Throwable $e) {
            $startTime = '00:00:00';
        }
        $appointment->date       = $request->date;
        $appointment->time       = $request->time;
        $appointment->start_time = $startTime;
        $appointment->status     = 2;
        $appointment->save();
    
        $doctorName = $appointment->doctor_detail->first_name . ' ' . $appointment->doctor_detail->last_name;
        $doctorProfession = $appointment->profession_type ?? 'N/A';
        $address = $appointment->doctor_detail->address ?? 'N/A';
        $formattedDate = date('d M Y', strtotime($appointment->date));
    
        $message =
            "Hello {$appointment->name},\n\n".
            "*Your appointment has been rescheduled.*\n\n".
            "*Doctor:* Dr. {$doctorName} ({$doctorProfession})\n".
            "*Service:* {$appointment->service_type}\n".
            "*Purpose:* {$appointment->purpose}\n".
            "*Date:* {$formattedDate}\n".
            "*Time:* {$appointment->time}\n".
            "*Location:* {$address}\n\n".
            "Please confirm your appointment below.";
    
        $cleanNumber = $appointment->phone;
        $this->sendWhatsAppText($cleanNumber, $message);
        $this->sendConfirmRescheduleOptions($cleanNumber, $appointment->id);
        if ($isPaidMessage) {
            SmsLogs::create([
                'doctor_id'   => $appointment->doctor_id,
                'sms_to'      => $cleanNumber,
                'sms_from'    => 'Meta WhatsApp',
                'sid'         => null,
                'body'        => $message,
                'status'      => 'sent',
                'broadcast_id'=> 900000000,
                'title'       => 'Appointment Rescheduled',
                'description' => $message,
            ]);
        }
        return response()->json(['status'  => true,'message' => 'Appointment rescheduled successfully.']);
    }
    
    private function sendWhatsAppText($to, $message)
    {
        $token = env('WHATSAPP_TOKEN');
        $phone_number_id = env('PHONE_NUMBER_ID');
        $url = "https://graph.facebook.com/v22.0/{$phone_number_id}/messages";
        $payload = [
            "messaging_product" => "whatsapp",
            "to" => $to,
            "type" => "text",
            "text" => [
                "body" => $message
            ]
        ];
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer {$token}",
            "Content-Type: application/json"
        ]);
    
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }
    
    private function sendConfirmRescheduleOptions($to, $appointmentId)
    {
        $rows = [
            [
                "id" => "confirm_appoint_" . $appointmentId,
                "title" => "Confirm Appointment"
            ],
            [
                "id" => "reschedule_appoint_" . $appointmentId,
                "title" => "Reschedule Again"
            ]
        ];
    
        $token = env('WHATSAPP_TOKEN');
        $phone_number_id = env('PHONE_NUMBER_ID');
        $url = "https://graph.facebook.com/v22.0/{$phone_number_id}/messages";
        $payload = [
            "messaging_product" => "whatsapp",
            "to" => $to,
            "type" => "interactive",
            "interactive" => [
                "type" => "list",
                "body" => [
                    "text" => "Please confirm your appointment:"
                ],
                "action" => [
                    "button" => "Select Option",
                    "sections" => [
                        [
                            "title" => "Appointment Confirmation",
                            "rows" => $rows
                        ]
                    ]
                ]
            ]
        ];
    
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer {$token}",
            "Content-Type: application/json"
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }
    
    public function missAppointment($id){
        $appointment = Appointments::find($id);
        if (!$appointment) {
            return response()->json(['error' => 'Appointment not found.'], 404);
        }
        $appointment->status = 4;
        $appointment->save();
        return response()->json(['success' => 'Appointment status update successfully.']);
    }
    
    public function checkinAppointment($id){
        $appointment = Appointments::find($id);
        if (!$appointment) {
            return response()->json(['error' => 'Appointment not found.'], 404);
        }
        $appointment->status = 3;
        $appointment->save();
        return response()->json(['success' => 'Appointment status update successfully.']);
    }
    
    public function create()
    {
        $doctorId = auth()->id();
        $services = DoctorService::where('doctor_id', $doctorId)->pluck('service_name', 'id');
        $timing   = DoctorTimings::where('doctor_id', $doctorId)->first();
        return view('doctor-dashboard.appointments.create', compact('services', 'timing'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:255',
            'phone'        => 'required|string|max:15',
            'service_type' => 'required|string|max:255',
            'date'         => 'required|date',
            'time_slot'    => 'required|string',
            'purpose'      => 'required|string',
        ]);
    
        $doctorId = auth()->id();
        $doctor   = \App\Models\User::find($doctorId);
    
        $phone = preg_replace('/\D/', '', $request->phone);
        if (strlen($phone) == 10) {
            $phone = '+91' . $phone;
        } elseif (substr($phone, 0, 1) !== '+') {
            $phone = '+91' . $phone;
        }
    
        $startTime = null;
        if (strpos($request->time_slot, ' - ') !== false) {
            $parts = explode(' - ', $request->time_slot);
            try {
                $startTime = \Carbon\Carbon::createFromFormat('h:i A', trim($parts[0]))->format('H:i:s');
            } catch (\Exception $e) {
                $startTime = null;
            }
        }
    
        $appointment = \App\Models\Appointments::create([
            'doctor_id'    => $doctorId,
            'service_type' => $request->service_type,
            'phone'        => $phone,
            'name'         => $request->name,
            'date'         => $request->date,
            'time'         => $request->time_slot,
            'start_time'   => $startTime,
            'purpose'      => $request->purpose,
        ]);
    
        /*if ($request->has('send_whatsapp')) {
    
            $doctorName = trim(($doctor->first_name ?? '') . ' ' . ($doctor->last_name ?? ''));
            $doctorProfession = $doctor->profession ?? 'Doctor';
            $formattedDate = \Carbon\Carbon::parse($appointment->date)->format('d M Y');
            $address = $doctor->address ?? 'Clinic Address not available';
            
            $bizDigits = preg_replace('/\D+/', '', config('services.twilio.whatsapp_from'));
            $rescheduleLink = "https://wa.me/{$bizDigits}?text=reschedule_booking";
    
            $msg = "Thanks {$appointment->name} for your booking.\n"
                . "*Here are your appointment details:*\n\n"
                . "*Doctor*: {$doctorName} ({$doctorProfession})\n"
                . "*Service*: {$appointment->service_type}\n"
                . "*Purpose*: {$appointment->purpose}\n"
                . "*Date*: {$formattedDate}\n"
                . "*Time*: {$appointment->time}\n\n"
                . "*Address*: {$address}\n\n"
                . "If you need to make changes later, reply with *RESCHEDULE* anytime.\n"
                . "Or tap here: {$rescheduleLink}";
    
            try {
                $twilio = new \Twilio\Rest\Client(
                    config('services.twilio.sid'),
                    config('services.twilio.token')
                );
    
                $twilio->messages->create(
                    'whatsapp:' . $phone,
                    [
                        'from' => config('services.twilio.whatsapp_from'),
                        'body' => $msg,
                    ]
                );
    
            } catch (\Exception $e) {
                \Log::error('WhatsApp confirmation failed for ' . $phone . ': ' . $e->getMessage());
            }
        }*/
    
        return redirect()->route('doctor.appointments')->with('success', 'Appointment booked successfully.');
    }

    
    public function getSlots(Request $request)
    {
        $doctorId = auth()->id();
        $timing = DoctorTimings::where('doctor_id', $doctorId)->first();
        if (!$timing) {
            return response()->json(['slots' => [], 'slot_type' => null]);
        }
        $slots = [];
        $gap = (int)$timing->slot_time_gap ?: 30;
        if ($timing->slot_type === 'single') {
            $slots = $this->generateSlots($timing->start_time, $timing->end_time, $gap);
        } else {
            if ($request->slot_half === 'first') {
                $slots = $this->generateSlots($timing->first_half_start, $timing->first_half_end, $gap);
            } elseif ($request->slot_half === 'second') {
                $slots = $this->generateSlots($timing->second_half_start, $timing->second_half_end, $gap);
            }
        }
        return response()->json(['slots' => $slots, 'slot_type' => $timing->slot_type]);
    }
    private function generateSlots($start, $end, $gap){
        $slots = [];
        $startTime = Carbon::parse($start);
        $endTime   = Carbon::parse($end);

        while ($startTime->lt($endTime)) {
            $slotStart = $startTime->format('h:i');
            $slotEnd   = $startTime->copy()->addMinutes($gap)->format('h:i A');
            if ($startTime->copy()->addMinutes($gap)->gt($endTime)) break;
            $slots[] = "$slotStart - $slotEnd";
            $startTime->addMinutes($gap);
        }
        return $slots;
    }

    
    private function getNext7Days()
    {
        $tz = 'Asia/Kolkata';
        $dates = [];

        for ($i = 0; $i < 7; $i++) {
            $d = Carbon::now($tz)->addDays($i);
            $dates[] = [
                'id' => $d->format('Y-m-d'),     // raw
                'label' => $d->format('D, d M Y') // pretty
            ];
        }

        return $dates;
    }
    
    protected function generateDoctorSlots($startTime, $endTime, $intervalMinutes = 60)
    {
        $slots = [];
        $tz = 'Asia/Kolkata';
    
        $start = \Carbon\Carbon::createFromFormat('H:i:s', $startTime, $tz);
        $end   = \Carbon\Carbon::createFromFormat('H:i:s', $endTime, $tz);
    
        while ($start->lt($end)) {
            $slotStart = $start->copy();
            $slotEnd   = $start->copy()->addMinutes($intervalMinutes);
    
            if ($slotEnd->gt($end)) {
                break;
            }
    
            $slots[] = $slotStart->format('h:i A') . ' - ' . $slotEnd->format('h:i A');
            $start->addMinutes($intervalMinutes);
        }
    
        return $slots;
    }
    
    
}
