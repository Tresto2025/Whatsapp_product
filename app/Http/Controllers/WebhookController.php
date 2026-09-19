<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\User;
use App\Models\DoctorService;
use App\Models\DoctorTimings;
use App\Models\Appointments;
use App\Models\SmsBalance;
use App\Models\SmsLogs;

class WebhookController extends Controller
{
    private $token;
    private $phone_number_id;
    
    public function __construct()
    {
        $this->token = config('services.whatsapp.token');
        $this->phone_number_id = config('services.whatsapp.phone_number_id');
    }

    /* ===============================================
       VERIFY
    =============================================== */

    public function verify(Request $request)
    {
        $verifyToken = config('services.whatsapp.verify_token');

        if (
            $verifyToken &&
            $request->hub_mode === 'subscribe' &&
            hash_equals($verifyToken, (string) $request->hub_verify_token)
        ) {
            return response($request->hub_challenge, 200);
        }

        return response('Verification failed', 403);
    }

    /**
     * Verify Meta's X-Hub-Signature-256 HMAC header against the raw request
     * body using the app secret. Returns true when no app secret is configured
     * (so local/dev without a secret still works), false only on an explicit
     * mismatch.
     */
    private function signatureIsValid(Request $request): bool
    {
        $appSecret = config('services.whatsapp.app_secret');
        if (empty($appSecret)) {
            return true; // no secret configured — skip verification
        }

        $signature = $request->header('X-Hub-Signature-256');
        if (empty($signature)) {
            return false;
        }

        $expected = 'sha256=' . hash_hmac('sha256', $request->getContent(), $appSecret);

        return hash_equals($expected, $signature);
    }

    /* ===============================================
       RECEIVE
    =============================================== */

    public function receive(Request $request)
    {
        if (!$this->signatureIsValid($request)) {
            Log::warning('WhatsApp webhook rejected: invalid signature');
            return response('Invalid signature', 403);
        }

        Log::info("Webhook Hit", $request->all());
        $data = $request->all();
        if (!isset($data['entry'][0]['changes'][0]['value']['messages'][0])) {
            return response()->json(['status' => 'no message']);
        }

        $message = $data['entry'][0]['changes'][0]['value']['messages'][0];
        $from = $message['from'];
        $sessions = cache()->get('wa_sessions', []);
        $step = $sessions[$from]['step'] ?? null;

        /* ================= TEXT ================= */
        if (isset($message['text']['body'])) {
            $text      = trim($message['text']['body']);
            $lowerText = strtolower($text);
        
            if ($lowerText === '*') {
                unset($sessions[$from]);
                cache()->put('wa_sessions', $sessions);
                return $this->sendText($from, "You have exited. Scan the QR code to start again.");
            }

            $isReschedule = str_contains($lowerText, 'reschedule');
            $isCancel     = str_contains($lowerText, 'cancel');
        
            if ($isReschedule || $isCancel) {
                $cleanNumber  = ltrim($from, '+');
                $appointments = Appointments::where('phone', $cleanNumber)->where('status', 1)->whereDate('date', '>=', Carbon::today())->with('doctor_detail')->orderBy('date', 'asc')->get();
                if ($appointments->isEmpty()) {
                    return $this->sendText($from, "You have no upcoming confirmed appointments.");
                }
        
                $msgText  = "*YOUR UPCOMING APPOINTMENTS*\n";
                $msgText .= "━━━━━━━━━━━━━━━━━━\n\n";
        
                foreach ($appointments as $index => $appt) {
                    $num      = $index + 1;
                    $date     = Carbon::parse($appt->date)->format('D, d M Y');
                    $msgText .= "*Appointment {$num}*\n";
                    $msgText .= "*Patient Name:* {$appt->name}\n";
                    $msgText .= "*Dr. Name:* {$appt->doctor_detail->first_name} {$appt->doctor_detail->last_name}\n";
                    $msgText .= "*Service:* {$appt->service_type}\n";
                    $msgText .= "*Date:* {$date}\n";
                    $msgText .= "*Time:* {$appt->time}\n";
                    $msgText .= "*Contact No.:* {$appt->doctor_detail->phone}\n";
                    $msgText .= "*Address:* {$appt->doctor_detail->address}\n";
                    $msgText .= "━━━━━━━━━━━━━━━━━━\n\n";
                }
        
                if ($isReschedule) {
                    $msgText .= "👉 *Reply with the appointment number (1, 2, 3...) to reschedule.*\n\n";
                    $msgText .= "Type * to exit.";
                    $sessions[$from]['step'] = 'awaiting_reschedule_selection';
                } else {
                    $msgText .= "👉 *Reply with the appointment number (1, 2, 3...) to cancel.*\n\n";
                    $msgText .= "Type * to exit.";
                    $sessions[$from]['step'] = 'awaiting_cancel_selection';
                }
        
                $sessions[$from]['appointments'] = $appointments->pluck('id')->toArray();
                $sessions[$from]['doctor_id']    = $sessions[$from]['doctor_id'] ?? null;
                cache()->put('wa_sessions', $sessions);
        
                return $this->sendText($from, $msgText);
            }

            Log::info("User Text: " . $text);
            $doctorId = $this->extractDoctorId($text);
            Log::info("Doctor ID Extracted: " . $doctorId);
        
            if ($doctorId) {
                $doctor = User::find($doctorId);
                if (!$doctor) {
                    return $this->sendText($from, "Not found.");
                }
                if ($doctor->booking_enabled == 0) {
                    return $this->sendText($from, "Sorry, bookings are closed for today.");
                }
                $sessions[$from] = ['doctor_id' => $doctorId, 'step' => 'welcome_menu'];
                cache()->put('wa_sessions', $sessions);
                return $this->sendWelcomeOptions($from);
            }
            
            $greetings = ['hello', 'hi', 'hey', 'hii', 'helo', 'hlw', 'hlo', 'start', 'begin', 'good morning', 'good evening', 'good afternoon', 'new booking', 'book', 'booking', 'appointment', 'new appointment',
    'book appointment', 'make appointment', 'appoint', 'slot', 'book slot', 'get slot',];
            $isGreeting = in_array($lowerText, $greetings) || str_starts_with($lowerText, 'hello') || str_starts_with($lowerText, 'hi ');
        
            if ($isGreeting && !$step) {
                $cleanNumber = ltrim($from, '+');
                $lastAppointment = Appointments::where('phone', $cleanNumber)->orderBy('created_at', 'desc')->first();
                if ($lastAppointment) {
                    $doctor = User::find($lastAppointment->doctor_id);
                    if ($doctor && $doctor->booking_enabled == 1) {
                        $sessions[$from] = [
                            'doctor_id' => $doctor->id,
                            'step'      => 'welcome_menu'
                        ];
                        cache()->put('wa_sessions', $sessions);
                        return $this->sendWelcomeOptions($from);
                    }
                }
                return $this->sendText($from,
                    "*Welcome 👋*\n\n" .
                    "We couldn't find any previous appointments linked to your number.\n\n" .
                    "Please scan the QR code to book an appointment."
                );
            }

    if ($step == 'awaiting_reschedule_selection') {
        if (!is_numeric($text)) {
            return $this->sendText($from, "⚠ Invalid input.\nPlease enter only the appointment number (1, 2, 3...).\n\nType * to exit.");
        }
        $selectedIndex  = (int)$text - 1;
        $appointmentIds = $sessions[$from]['appointments'] ?? [];
        if (!isset($appointmentIds[$selectedIndex])) {
            return $this->sendText($from, "Invalid selection. Please enter a valid number.");
        }
        $appointmentId = $appointmentIds[$selectedIndex];
        $appointment   = Appointments::find($appointmentId);
        if (!$appointment) {
            return $this->sendText($from, "Appointment not found.");
        }
        $sessions[$from]['reschedule'] = [
            'appointment_id' => $appointmentId,
            'doctor_id'      => $appointment->doctor_id,
            'service'        => $appointment->service_type,
            'name'           => $appointment->name,
            'purpose'        => $appointment->purpose,
            'date'           => null,
            'time'           => null,
            'selected_half'  => null,
        ];
        $sessions[$from]['step'] = 'reschedule_date';
        cache()->put('wa_sessions', $sessions);
        return $this->sendDateListReschedule($from);
    }

    if ($step == 'awaiting_cancel_selection') {
        if (!is_numeric($text)) {
            return $this->sendText($from, "⚠ Invalid input.\nPlease enter only the appointment number (1, 2, 3...).\n\nType * to exit.");
        }
        $selectedIndex  = (int)$text - 1;
        $appointmentIds = $sessions[$from]['appointments'] ?? [];
        if (!isset($appointmentIds[$selectedIndex])) {
            return $this->sendText($from, "Invalid selection. Please enter a valid number.");
        }
        $appointmentId = $appointmentIds[$selectedIndex];
        $appointment   = Appointments::find($appointmentId);
        if (!$appointment) {
            return $this->sendText($from, "Appointment not found.");
        }
        
        //  fetch doctor details
    $doctor = \App\Models\User::select('first_name', 'last_name', 'address')
        ->find($appointment->doctor_id);

    $doctorName = $doctor
        ? trim(($doctor->first_name ?? '') . ' ' . ($doctor->last_name ?? ''))
        : 'N/A';

    $address = $doctor ? ($doctor->address ?? 'N/A') : 'N/A';
    // end here
        
        $appointment->update(['status' => 0]);
        $name    = $appointment->name;
        $service = $appointment->service_type;
        $date    = Carbon::parse($appointment->date)->format('D, d M Y');
        $time    = $appointment->time;
        unset($sessions[$from]);
        cache()->put('wa_sessions', $sessions);
        return $this->sendText($from,
            "*Appointment Cancelled Successfully* ❌\n\n" .
            "Hello {$name},\n\n" .
            "Your appointment has been cancelled as requested.\n\n" .
            "*Doctor:* Dr. {$doctorName}\n" .
            "*Service:* {$service}\n" .
            "*Date:* {$date}\n" .
            "*Time:* {$time}\n" .
            "*Clinic Address:* {$address}\n" .

            "\nReply with *Hi* - To book a new appointment\n\n" .

            "Thank you for choosing us."
        );
    }

    if (isset($sessions[$from]) && in_array($step, [
        'welcome_menu', 'ask_service', 'ask_date',
        'choose_half', 'ask_time', 'reschedule_date',
        'reschedule_choose_half', 'reschedule_time'
    ])) {
        return $this->invalidOption($from);
    }

    if ($step == 'ask_name') {
        if (empty($text) || strlen($text) < 2) {
            return $this->sendText($from, "⚠ Please enter a valid full name.");
        }
        $sessions[$from]['name'] = $text;
        $sessions[$from]['step'] = 'ask_service';
        cache()->put('wa_sessions', $sessions);
        return $this->sendServiceList($from, $sessions[$from]['doctor_id']);
    }

    if ($step == 'ask_purpose') {
    $name    = $sessions[$from]['name'];
    $service = $sessions[$from]['service'];
    $date    = $sessions[$from]['date'];
    $time    = $sessions[$from]['time'];
    $doctorId = $sessions[$from]['doctor_id'];

    // Fetch doctor
    // $doctor = \App\Models\User::find($doctorId);
    $doctor = \App\Models\User::select('first_name', 'last_name', 'address')
    ->find($doctorId);

    $doctorName = $doctor
        ? trim($doctor->first_name . ' ' . $doctor->last_name)
        : 'N/A';
        
    $address = $doctor ? ($doctor->address ?? 'N/A') : 'N/A';

    // Save appointment
    Appointments::create([
        'doctor_id'    => $doctorId,
        'service_type' => $service,
        'phone'        => $from,
        'name'         => $name,
        'date'         => $date,
        'time'         => $time,
        'purpose'      => $text,
    ]);

    // Clear session
    unset($sessions[$from]);
    cache()->put('wa_sessions', $sessions);

    // Send improved message
    return $this->sendText($from,
        "*Appointment Confirmed* ✅ \n\n" .

        "Hello *{$name}*,\n\n" .

        "Your appointment has been successfully booked.\n\n" .

        "*Doctor:* Dr. {$doctorName}\n" .
        "*Service:* {$service}\n" .
        "*Date:* " . date('D, d M Y', strtotime($date)) . "\n" .
        "*Time:* {$time}\n" .
        "*Clinic Address:* {$address}\n" .

        (!empty($text) ? "\n*Notes:* {$text}\n" : "") .

        "\nNeed to make any changes to your appointment?\n\n" .
        "Reply with:\n" .
        "*reschedule* – to change your appointment\n" .
        "*cancel* – to cancel your appointment\n\n" .

        "Thank you for choosing us."
    );
}

    if (!$step) {
        $hasCode = str_contains($lowerText, 'code:');
        if (!$hasCode) {
            return $this->sendText($from,
                "⚠ Invalid input.\n\n" .
                "Please use one of the following:\n\n" .
                "1. Type *reschedule* to reschedule your appointment.\n\n" .
                "2. Type *cancel* to cancel your appointment.\n\n" .
                "3. Scan QR code to book a new appointment."
            );
        }
    }
}

        if (isset($message['interactive']['list_reply'])) {
            $selected = $message['interactive']['list_reply']['id'];
            if (str_starts_with($selected, 'confirm_appoint_')) {
                $appointmentId = str_replace('confirm_appoint_', '', $selected);
                Appointments::where('id', $appointmentId)->update(['status' => 1]);
                return $this->sendText($from, "Your appointment is confirmed. Thank you!");
            }
            
            if (str_starts_with($selected, 'reschedule_appoint_')) {
                Log::info("Reschedule Clicked Selected ID: " . $selected);
                $appointmentId = str_replace('reschedule_appoint_', '', $selected);
                Log::info("Extracted Appointment ID: " . $appointmentId);
                $appointment = Appointments::find($appointmentId);
                if (!$appointment) {
                    Log::info("Appointment NOT FOUND for ID: " . $appointmentId);
                    return $this->sendText($from, "Appointment not found.");
                }
                Log::info("Appointment Found", [
                    'id' => $appointment->id,
                    'doctor_id' => $appointment->doctor_id,
                    'service' => $appointment->service_type,
                    'name' => $appointment->name
                ]);
                $sessions[$from]['reschedule'] = [
                    'appointment_id' => $appointment->id,
                    'doctor_id'      => $appointment->doctor_id,
                    'service'        => $appointment->service_type,
                    'name'           => $appointment->name,
                    'purpose'        => $appointment->purpose,
                    'date'           => null,
                    'time'           => null,
                    'selected_half'  => null,
                ];
                $sessions[$from]['step'] = 'reschedule_date';
                Log::info("Session Data Before Save", [
                    'from' => $from,
                    'session' => $sessions[$from]
                ]);
                cache()->put('wa_sessions', $sessions);
                Log::info("Session Stored Successfully");
                return $this->sendDateListReschedule($from);
            }
            if ($step == 'welcome_menu') {
                $allowed = ['book_appointment', 'reschedule_appointment', 'cancel_appointment'];
                if (!in_array($selected, $allowed)) {
                    return $this->invalidOption($from);
                }
                if ($selected == 'book_appointment') {
                    $sessions[$from]['step'] = 'ask_name';
                    cache()->put('wa_sessions', $sessions);
                    return $this->sendText($from, "Please enter your Full Name:");
                }
                
                if ($selected == 'reschedule_appointment') {
                    $cleanNumber = ltrim($from, '+');
                    $appointments = Appointments::where('phone', $cleanNumber)->where('status', 1)->whereDate('date', '>=', Carbon::today())->with('doctor_detail')->orderBy('date', 'asc')->get();
                    Log::info("Appointments Found: " . $appointments->count());
                    if ($appointments->isEmpty()) {
                        return $this->sendText($from, "You have no upcoming confirmed appointments.");
                    }
                    $message = "*YOUR UPCOMING APPOINTMENTS*\n";
                    $message .= "━━━━━━━━━━━━━━━━━━\n\n";
                
                    foreach ($appointments as $index => $appt) {
                
                        $patientName = $appt->name ?? 'N/A';
                        $doctorProfession = $appt->profession_type ?? 'N/A';
                        $doctorFirstName = $appt->doctor_detail->first_name ?? '';
                        $doctorLastName  = $appt->doctor_detail->last_name ?? '';
                        $doctorName      = trim($doctorFirstName . ' ' . $doctorLastName) ?: 'N/A';
                        $service = $appt->service_type ?? 'N/A';
                        $purpose = $appt->purpose ?? 'N/A';
                        $date = Carbon::parse($appt->date)->format('D, d M Y');
                        $time = $appt->time ?? 'N/A';
                        $address = $appt->doctor_detail->address ?? 'N/A';
                
                        $num = $index + 1;
                
                        $message .= "*Appointment {$num}*\n";
                        $message .= "*Patient Name:* {$patientName}\n";
                        $message .= "*Dr. Name:* {$doctorName}\n";
                        $message .= "*Service:* {$service}\n";
                        $message .= "*Date:* {$date}\n";
                        $message .= "*Time:* {$time}\n";
                        $message .= "*Contact No.:* {$appt->doctor_detail->phone}\n";
                        $message .= "*Address:* {$address}\n";
                        $message .= "━━━━━━━━━━━━━━━━━━\n\n";
                    }
                
                    $message .= "👉 *Reply with the appointment number (1, 2, 3...) to reschedule.*";
                
                    /* =============================
                       STORE SESSION LIKE TWILIO
                    ============================= */
                
                    $sessions[$from]['step'] = 'awaiting_reschedule_selection';
                    $sessions[$from]['appointments'] = $appointments->pluck('id')->toArray();
                
                    cache()->put('wa_sessions', $sessions);
                
                    Log::info("===== META RESCHEDULE END =====");
                
                    return $this->sendText($from, $message);
                }
                
                if ($selected == 'cancel_appointment') {
                    $cleanNumber = ltrim($from, '+');
                    $appointments = Appointments::where('phone', $cleanNumber)->where('status', 1)->whereDate('date', '>=', Carbon::today())->with('doctor_detail')->orderBy('date', 'asc')->get();
                    if ($appointments->isEmpty()) {
                        return $this->sendText($from, "You have no upcoming confirmed appointments.");
                    }
                
                    $message = "*YOUR UPCOMING APPOINTMENTS*\n";
                    $message .= "━━━━━━━━━━━━━━━━━━\n\n";
                
                    foreach ($appointments as $index => $appt) {
                        $num = $index + 1;
                        $date = Carbon::parse($appt->date)->format('D, d M Y');
                        $message .= "*Appointment {$num}*\n";
                        $message .= "*Patient Name:* {$appt->name}\n";
                        $message .= "*Dr. Name:* {$appt->doctor_detail->first_name} {$appt->doctor_detail->last_name}\n";
                        $message .= "*Service:* {$appt->service_type}\n";
                        $message .= "*Date:* {$date}\n";
                        $message .= "*Time:* {$appt->time}\n";
                        $message .= "*Contact No.:* {$appt->doctor_detail->phone}\n";
                        $message .= "*Address:* {$appt->doctor_detail->address}\n";
                        $message .= "━━━━━━━━━━━━━━━━━━\n\n";
                    }
                
                    $message .= "👉 *Reply with the appointment number (1, 2, 3...) to cancel.*";
                    $sessions[$from]['step'] = 'awaiting_cancel_selection';
                    $sessions[$from]['appointments'] = $appointments->pluck('id')->toArray();
                    cache()->put('wa_sessions', $sessions);
                    return $this->sendText($from, $message);
                }
                return $this->sendWelcomeOptions($from);
            }
            
            if ($step == 'reschedule_date') {
                try {
                    $selectedDate = Carbon::createFromFormat('Y-m-d', $selected);
                } catch (\Exception $e) {
                    return $this->invalidOption($from);
                }
                if ($selectedDate->lt(Carbon::today()) || $selectedDate->gt(Carbon::today()->addDays(7)->endOfDay())) {
                    return $this->invalidOption($from);
                }
                $doctorId = $sessions[$from]['reschedule']['doctor_id'];
                $dayName = strtolower($selectedDate->format('l'));
                $timing = DoctorTimings::where('doctor_id', $doctorId)->where('day', $dayName)->first();
                if (!$timing) {
                    return $this->invalidOption($from);
                }
                $sessions[$from]['reschedule']['date'] = $selected;
                $sessions[$from]['step'] = 'reschedule_time';
                cache()->put('wa_sessions', $sessions);
                return $this->sendTimeListReschedule($from, $doctorId, $selected);
            }
            
            if ($step == 'reschedule_choose_half') {

                $allowed = ['first_half', 'second_half'];
            
                if (!in_array($selected, $allowed)) {
                    return $this->invalidOption($from);
                }
            
                $sessions[$from]['reschedule']['selected_half'] = $selected;
                $sessions[$from]['step'] = 'reschedule_time';
            
                cache()->put('wa_sessions', $sessions);
            
                return $this->sendHalfSlotsReschedule(
                    $from,
                    $sessions[$from]['reschedule']['doctor_id'],
                    $sessions[$from]['reschedule']['date'],
                    $selected
                );
            }
            
            if ($step == 'reschedule_time') {

                $selectedSlot = $selected;
                $appointmentId = $sessions[$from]['reschedule']['appointment_id'];
                $doctorId      = $sessions[$from]['reschedule']['doctor_id'];
                $date          = $sessions[$from]['reschedule']['date'];
                $half          = $sessions[$from]['reschedule']['selected_half'] ?? null;
            
                $appointment = Appointments::find($appointmentId);
                if (!$appointment) {
                    return $this->sendText($from, "Appointment not found.");
                }
            
                $doctor = User::find($doctorId);
                
                // fetch doctor details
                $doctor = \App\Models\User::select('first_name', 'last_name', 'address')->find($doctorId);
                $doctorName = $doctor? trim(($doctor->first_name ?? '') . ' ' . ($doctor->last_name ?? '')): 'N/A';
                $address = $doctor ? ($doctor->address ?? 'N/A') : 'N/A';
                
            
                try {
                    $selectedDate = Carbon::createFromFormat('Y-m-d', $date);
                } catch (\Exception $e) {
                    return $this->invalidOption($from);
                }
            
                if ($selectedDate->lt(Carbon::today()) || $selectedDate->gt(Carbon::today()->addDays(7)->endOfDay())) {
                    return $this->invalidOption($from);
                }
            
                $dayName = strtolower($selectedDate->format('l'));
            
                $timing = DoctorTimings::where('doctor_id', $doctorId)->where('day', $dayName)->first();
                if (!$timing) {
                    return $this->invalidOption($from);
                }
                
                if ($timing->slot_type == 'single') {
                    $validSlots = $this->generateSlots(
                        $timing->start_time,
                        $timing->end_time,
                        $timing->slot_time_gap
                    );
                } else {
                    $half = $sessions[$from]['reschedule']['selected_half'] ?? null;
                    
                    if (!$half) {
                        return $this->invalidOption($from);
                    }
                    if ($half == 'first_half') {
                        $start = $timing->first_half_start;
                        $end   = $timing->first_half_end;
                    } else {
                        $start = $timing->second_half_start;
                        $end   = $timing->second_half_end;
                    }
                    $validSlots = $this->generateSlots($start, $end, $timing->slot_time_gap);
                }
                
                if (!in_array($selectedSlot, $validSlots)) {
                    return $this->invalidOption($from);
                }
                
                try {
            
                    $parts = explode('-', $selectedSlot);
                    $startTimeString = trim($parts[0]);
                    $selectedDateTime = Carbon::createFromFormat(
                        'Y-m-d h:i A',
                        $date.' '.$startTimeString
                    );
                } catch (\Exception $e) {
                    return $this->invalidOption($from);
                }
                
                if ($selectedDateTime->lessThan(Carbon::now())) {
                    return $this->sendText($from, "Selected time has already passed. Please choose another slot.");
                }
                
                $bookingCount = Appointments::where('doctor_id',$doctorId)->where('date',$date)->where('time',$selectedSlot)->where('status',1)->where('id','!=',$appointmentId)->count();
            
                if($doctor && $doctor->appointment_mode == 2 && $bookingCount >= 1){
                    $this->sendText($from,"⚠ This slot is already booked.\nPlease select another time.");
                    if($half){
                        return $this->sendHalfSlotsReschedule($from,$doctorId,$date,$half);
                    }
                    return $this->sendTimeListReschedule($from,$doctorId,$date);
                }
            
                // UPDATE APPOINTMENT
                $appointment->update([
                    'date' => $date,
                    'time' => $selectedSlot,
                    'status' => 1
                ]);
            
                unset($sessions[$from]);
                cache()->put('wa_sessions', $sessions);
            
                return $this->sendText(
                    $from,
                    "*Appointment Rescheduled Successfully* 🔁\n\n" .
                    "Hello {$appointment->name},\n\n" .
                    "Your appointment has been successfully rescheduled.\n\n" .

                    "*Doctor:* Dr. {$doctorName}\n" .
                    "*Service:* {$appointment->service_type}\n" .
                    "*Date:* " . Carbon::parse($date)->format('D, d M Y') . "\n" .
                    "*Time:* {$selectedSlot}\n" .
                    "*Clinic Address:* {$address}\n" .

                    (!empty($appointment->purpose) ? "\n*Notes:* {$appointment->purpose}\n" : "") .

                    "\nReply *Hi* - To book another appointment.\n\n" .
                    "Thank you for choosing us."
                );
            }
        
            if ($step == 'ask_service') {
                $doctorId = $sessions[$from]['doctor_id'];
                $validServices = DoctorService::where('doctor_id', $doctorId)->pluck('service_name')->toArray();
                if (!in_array($selected, $validServices)) {
                    return $this->invalidOption($from);
                }
                
                $sessions[$from]['service'] = $selected;
                $sessions[$from]['step'] = 'ask_date';
                cache()->put('wa_sessions', $sessions);
                return $this->sendDateList($from);
            }
        
            if ($step == 'ask_date') {
                try {
                    $selectedDate = Carbon::createFromFormat('Y-m-d', $selected);
                } catch (\Exception $e) {
                    return $this->invalidOption($from);
                }
            
                if ($selectedDate->lt(Carbon::today()) || $selectedDate->gt(Carbon::today()->addDays(7)->endOfDay())) {
                    return $this->invalidOption($from);
                }
                $doctorId = $sessions[$from]['doctor_id'];
                $dayName = strtolower($selectedDate->format('l'));
                $timing = DoctorTimings::where('doctor_id', $doctorId)->where('day', $dayName)->first();
            
                if (!$timing) {
                    return $this->invalidOption($from);
                }
                $sessions[$from]['date'] = $selected;
                $sessions[$from]['step'] = 'ask_time';
                cache()->put('wa_sessions', $sessions);
                return $this->sendTimeList(
                    $from,
                    $sessions[$from]['doctor_id'],
                    $selected
                );
            }
        
            if ($step == 'choose_half') {
                $allowed = ['first_half', 'second_half'];
                if (!in_array($selected, $allowed)) {
                    return $this->invalidOption($from);
                }
                $sessions[$from]['selected_half'] = $selected;
                // IMPORTANT: Next step remains ask_time
                $sessions[$from]['step'] = 'ask_time';
                cache()->put('wa_sessions', $sessions);
        
                return $this->sendHalfSlots(
                    $from,
                    $sessions[$from]['doctor_id'],
                    $sessions[$from]['date'],
                    $selected
                );
            }
        
            /* ==== FINAL SLOT SELECT ==== */
            if ($step == 'ask_time') {
                $date = $sessions[$from]['date'];
                $doctorId = $sessions[$from]['doctor_id'];
                $doctor = User::find($doctorId);
                $dayName = strtolower(Carbon::parse($date)->format('l'));
                $timing = DoctorTimings::where('doctor_id',$doctorId)->where('day',$dayName)->first();
                if (!$timing) {
                    return $this->invalidOption($from);
                }
                
                if ($timing->slot_type == 'single') {
                    $validSlots = $this->generateSlots(
                        $timing->start_time,
                        $timing->end_time,
                        $timing->slot_time_gap
                    );
                } else {
                    $firstHalf = $this->generateSlots(
                        $timing->first_half_start,
                        $timing->first_half_end,
                        $timing->slot_time_gap
                    );
                    $secondHalf = $this->generateSlots(
                        $timing->second_half_start,
                        $timing->second_half_end,
                        $timing->slot_time_gap
                    );
                    $validSlots = array_merge($firstHalf,$secondHalf);
                }
            
                if (!in_array($selected,$validSlots)) {
                    return $this->invalidOption($from);
                }
            
                $selectedSlot = $selected;
                $bookingCount = Appointments::where('doctor_id',$doctorId)->where('date',$date)->where('time',$selectedSlot)->where('status',1)->count();
                // SINGLE MODE SLOT CHECK
                if($doctor && $doctor->appointment_mode == 2 && $bookingCount >= 1){
                    $this->sendText($from,"This slot is already booked.\nPlease select another time.");
                    if(isset($sessions[$from]['selected_half'])){
                        $half = $sessions[$from]['selected_half'];
                        return $this->sendHalfSlots($from,$doctorId,$date,$half);
                    }
                    return $this->sendTimeList($from,$doctorId,$date);
                }
            
                try {
                    if (!str_contains($selectedSlot,'-')) {
                        throw new \Exception("Invalid format");
                    }
            
                    $parts = explode('-',$selectedSlot);
                    $startTimeString = trim($parts[0]);
                    $selectedDateTime = Carbon::createFromFormat(
                        'Y-m-d h:i A',
                        $date.' '.$startTimeString
                    );
                } catch (\Exception $e) {
                    return $this->sendTimeList($from,$doctorId,$date);
                }
            
                $now = Carbon::now();
                if ($selectedDateTime->lessThan($now)) {
                    if (isset($sessions[$from]['selected_half'])) {
                        $half = $sessions[$from]['selected_half'];
                        $halfLabel = $half == 'first_half' ? 'Morning' : 'Evening';
                        if ($half == 'first_half') {
                            $start = $timing->first_half_start;
                            $end   = $timing->first_half_end;
                        } else {
                            $start = $timing->second_half_start;
                            $end   = $timing->second_half_end;
                        }
            
                        $slots = $this->generateSlots($start,$end,$timing->slot_time_gap);
                        $futureSlots = [];
                        foreach ($slots as $slot) {
                            $parts = explode('-',$slot);
                            $slotStart = trim($parts[0]);
                            $slotDateTime = Carbon::createFromFormat(
                                'Y-m-d h:i A',
                                $date.' '.$slotStart
                            );
                            if ($slotDateTime->greaterThan($now)) {
                                $futureSlots[] = $slot;
                            }
                        }
            
                        if (empty($futureSlots)) {
                            $this->sendText($from,"$halfLabel slots are finished.\nPlease select another slot type.");
                            return $this->sendTimeList($from,$doctorId,$date);
                        }
                        $this->sendText($from,"Selected time has passed.\nPlease select another time.");
                        return $this->sendHalfSlots($from,$doctorId,$date,$half);
                    }
                    $this->sendText($from,"Selected time has passed.\nPlease select another time.");
                    return $this->sendTimeList($from,$doctorId,$date);
                }
                
                $availableSlots = [];
                foreach($validSlots as $slot){
                    $count = Appointments::where('doctor_id',$doctorId)->where('date',$date)->where('time',$slot)->where('status',1)->count();
                    if($doctor->appointment_mode == 1 || $count == 0){
                        $availableSlots[] = $slot;
                    }
                }
            
                if(empty($availableSlots)){
                    $this->sendText($from,"No slots available for this date.\nPlease select another date.");
                    unset($sessions[$from]['date']);
                    $sessions[$from]['step'] = 'ask_date';
                    cache()->put('wa_sessions',$sessions);
                    return $this->sendDateList($from,$doctorId);
                }
                
                $sessions[$from]['time'] = $selectedSlot;
                $sessions[$from]['step'] = 'ask_purpose';
                cache()->put('wa_sessions',$sessions);
                return $this->sendText($from,"Please type any special requests, notes, or the purpose of your visit if any:");
            
            }
        }

        return response()->json(['status' => 'ok']);
    }

    /* ===============================================
       WELCOME OPTIONS
    =============================================== */

    private function sendWelcomeOptions($to)
    {
        $rows = [
        ["id" => "book_appointment", "title" => "Book Appointment"],
        ["id" => "reschedule_appointment", "title" => "Reschedule Appointment"],
        ["id" => "cancel_appointment", "title" => "Cancel Appointment"]
    ];

    $sessions = cache()->get('wa_sessions', []);
    $doctorId = $sessions[$to]['doctor_id'] ?? null;

    $doctorName = 'our specialist'; // fallback

    if ($doctorId) {
        $doctor = \App\Models\User::find($doctorId);
        if ($doctor) {
            $doctorName = trim($doctor->first_name . ' ' . $doctor->last_name);
        }
    }

    $message =
        "*Welcome 👋*\n\n" .
        "You're now connected with *Dr. {$doctorName}*.\n\n" .
        "Book, reschedule, or manage your appointment in just a few steps.\n\n" .
        "Please select an option below:";

    return $this->sendList($to, $message, "Select Option", "Appointment Options", $rows);

    }

    /* ===============================================
       SERVICES
    =============================================== */

    private function sendServiceList($to, $doctorId)
    {
        $services = DoctorService::where('doctor_id', $doctorId)->get();

        $rows = [];

        foreach ($services as $service) {
            $rows[] = [
                "id" => $service->service_name,
                "title" => $service->service_name
            ];
        }
        $sessions = cache()->get('wa_sessions', []);
        $name = $sessions[$to]['name'] ?? '';
        $message = "Thank you, {$name}\n\n" .
"Please select the service you would like to book:";
        
        return $this->sendList($to, $message, "Select", "Services", $rows);
    }


    /* ===============================================
   DATES (UPDATED - DAYS OFF REMOVED)
=============================================== */

private function sendDateList($to)
{
    $sessions = cache()->get('wa_sessions', []);
    $doctorId = $sessions[$to]['doctor_id'] ?? null;

    if (!$doctorId) {
        return $this->sendText($to, "Doctor not found.");
    }

    $rows = [];

    // Next 14 days check karenge
    for ($i = 0; $i < 8; $i++) {

        $date = Carbon::today()->addDays($i);
        $dayName = strtolower($date->format('l'));

        // Check Doctor timing for that weekday
        $timing = DoctorTimings::where('doctor_id', $doctorId)
                    ->where('day', $dayName)
                    ->first();

        // ❌ Agar timing record hi nahi mila → doctor off
        if (!$timing) {
            continue;
        }

        $slotsAvailable = false;

        // ===== SINGLE SLOT TYPE =====
        if ($timing->slot_type == 'single') {

            $slots = $this->generateSlots(
                $timing->start_time,
                $timing->end_time,
                $timing->slot_time_gap
            );

            if (!empty($slots)) {
                $slotsAvailable = true;
            }
        }

        // ===== DOUBLE SLOT TYPE =====
        else {

            $firstHalf = $this->generateSlots(
                $timing->first_half_start,
                $timing->first_half_end,
                $timing->slot_time_gap
            );

            $secondHalf = $this->generateSlots(
                $timing->second_half_start,
                $timing->second_half_end,
                $timing->slot_time_gap
            );

            if (!empty($firstHalf) || !empty($secondHalf)) {
                $slotsAvailable = true;
            }
        }

        if ($slotsAvailable) {
            $rows[] = [
                "id" => $date->format('Y-m-d'),
                "title" => $date->format('D, d M Y')
            ];
        }
    }

    if (empty($rows)) {
        return $this->sendText($to, "No available dates found.");
    }
    
    $sessions = cache()->get('wa_sessions', []);
    $name = $sessions[$to]['name'] ?? '';
    $message = "Please select a preferred date for your appointment:";

    return $this->sendList($to, $message, "Select Date", "Available Dates", $rows);
}

    /* ===============================================
       TIME LOGIC
    =============================================== */

    private function sendTimeList($to, $doctorId, $date)
    {
        $dayName = strtolower(Carbon::parse($date)->format('l'));
        $timing = DoctorTimings::where('doctor_id', $doctorId)->where('day', $dayName)->first();
        if (!$timing) {
            return $this->sendText($to, "Doctor not available on this date.");
        }
        if ($timing->slot_type == 'single') {
            $slots = $this->generateSlots(
                $timing->start_time,
                $timing->end_time,
                $timing->slot_time_gap
            );
    
            if (empty($slots)) {
                return $this->sendText($to, "No slots available.");
            }
            $rows = [];
            foreach ($slots as $slot) {
                $rows[] = [
                    "id" => $slot,
                    "title" => $slot
                ];
            }
            $sessions = cache()->get('wa_sessions', []);
            $name = $sessions[$to]['name'] ?? '';
            $message = "Please select a time slot for your appointment:";
            return $this->sendList($to, $message, "Select Time", "Available Slots", $rows);
        }
    
        $sessions = cache()->get('wa_sessions', []);
        $sessions[$to]['step'] = 'choose_half';
        cache()->put('wa_sessions', $sessions);
        $rows = [
            ["id" => "first_half", "title" => "Morning"],
            ["id" => "second_half", "title" => "Evening"]
        ];
        $sessions = cache()->get('wa_sessions', []);
        $name = $sessions[$to]['name'] ?? '';
        $message = "Please select a time of day (Morning or Evening):";
        return $this->sendList($to, $message, "Select", "Morning / Evening", $rows);
    }

    private function sendHalfSlots($to, $doctorId, $date, $half)
    {
        $dayName = strtolower(Carbon::parse($date)->format('l'));
        $timing = DoctorTimings::where('doctor_id', $doctorId)->where('day', $dayName)->first();
        if (!$timing) {
            return $this->sendText($to, "Doctor not available.");
        }
        if ($half == 'first_half') {
            $start = $timing->first_half_start;
            $end   = $timing->first_half_end;
        } else {
            $start = $timing->second_half_start;
            $end   = $timing->second_half_end;
        }
        $slots = $this->generateSlots($start, $end, $timing->slot_time_gap);
        if (empty($slots)) {
            return $this->sendText($to, "No slots available.");
        }
        $rows = [];
        foreach ($slots as $slot) {
            $rows[] = [
                "id" => $slot,
                "title" => $slot
            ];
        }
        
        $sessions = cache()->get('wa_sessions', []);
        $name = $sessions[$to]['name'] ?? '';
        $message = "Please select a time slot:";
        
        return $this->sendList($to, $message, "Select Time", "Available Slots", $rows);
    }
    
    private function generateSlots($start, $end, $gap)
    {
        if (!$start || !$end || !$gap) {
            return [];
        }
    
        $slots = [];
        $startTime = Carbon::createFromFormat('H:i:s', $start);
        $endTime = Carbon::createFromFormat('H:i:s', $end);
        while ($startTime < $endTime) {
            $slotStart = $startTime->format('h:i A');
            $startTime->addMinutes($gap);
            if ($startTime > $endTime) {
                break;
            }
            $slotEnd = $startTime->format('h:i A');
            $slots[] = $slotStart . " - " . $slotEnd;
        }
        return $slots;
    }

    /* ===============================================
       COMMON SENDERS
    =============================================== */

    private function sendList($to, $bodyText, $buttonText, $sectionTitle, $rows)
    {
        $payload = [
            "messaging_product" => "whatsapp",
            "to" => $to,
            "type" => "interactive",
            "interactive" => [
                "type" => "list",
                "body" => ["text" => $bodyText],
                "action" => [
                    "button" => $buttonText,
                    "sections" => [
                        [
                            "title" => $sectionTitle,
                            "rows" => $rows
                        ]
                    ]
                ]
            ]
        ];

        return $this->send($payload);
    }

    private function sendText($to, $message)
    {
        $payload = [
            "messaging_product" => "whatsapp",
            "to" => $to,
            "type" => "text",
            "text" => ["body" => $message]
        ];

        return $this->send($payload);
    }

    private function send($payload)
    {
        $to = $payload['to'] ?? null;
    
        // Get session
        $sessions = cache()->get('wa_sessions', []);
        $doctorId = $sessions[$to]['doctor_id']
            ?? $sessions[$to]['reschedule']['doctor_id']
            ?? null;
    
        // ===== BALANCE CHECK =====
        /*if ($doctorId) {
            $balance = SmsBalance::where('doctor_id', $doctorId)->first();
            if (!$balance || $balance->pending_sms <= 0) {
                Log::warning("Doctor {$doctorId} has no SMS balance left.");
                return response()->json(['error' => 'No SMS balance'], 403);
            }
        }*/
        Log::info("PHONE NUMBER ID VALUE: ".$this->phone_number_id);
        Log::info("TOKEN VALUE: ".$this->token);
        // ===== SEND MESSAGE =====
        $url = "https://graph.facebook.com/v22.0/{$this->phone_number_id}/messages";
    
        $ch = curl_init($url);
    
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer {$this->token}",
            "Content-Type: application/json"
        ]);
    
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
        $response = curl_exec($ch);
        
        curl_close($ch);
    
        // ===== MINUS BALANCE AFTER SUCCESS =====
        
        if ($doctorId) {
           // $balance->spent_sms += 1;
           // $balance->pending_sms -= 1;
           // $balance->save();
    
            SmsLogs::create([
                'doctor_id' => $doctorId,
                'phone'     => $to,
                'message'   => json_encode($payload),
                'status'    => 'sent'
            ]);
        }
    
        return response()->json(['sent' => true]);
    }
    
    private function invalidOption($to)
    {
        return $this->sendText($to, "⚠ Invalid selection.\nPlease choose an option from the list only.");
    }

private function extractDoctorId($text)
{
    Log::info("Incoming Text: ".$text);

    if (preg_match('/code:\s*(\d+)/i', $text, $matches)) {

        Log::info("Regex Match Found: ".json_encode($matches));

        $doctorId = substr($matches[1], 4);

        Log::info("Extracted Doctor ID: ".$doctorId);

        return $doctorId;
    }

    Log::info("Doctor Code NOT Found");

    return null;
}
    
    private function sendHalfSlotsReschedule($to, $doctorId, $date, $half)
    {
        $dayName = strtolower(Carbon::parse($date)->format('l'));
        $timing = DoctorTimings::where('doctor_id', $doctorId)->where('day', $dayName)->first();
        if (!$timing) {
            return $this->sendText($to, "Doctor not available.");
        }
    
        if ($half == 'first_half') {
            $start = $timing->first_half_start;
            $end   = $timing->first_half_end;
        } else {
            $start = $timing->second_half_start;
            $end   = $timing->second_half_end;
        }
    
        $slots = $this->generateSlots($start, $end, $timing->slot_time_gap);
        if (empty($slots)) {
            return $this->sendText($to, "No slots available.");
        }
    
        $rows = [];
        foreach ($slots as $slot) {
            $rows[] = [
                "id" => $slot,
                "title" => $slot
            ];
        }
        $sessions = cache()->get('wa_sessions', []);
        $name = $sessions[$to]['name'] ?? '';
        $message = "Please select a time slot:";
        return $this->sendList($to, $message, "Select Time", "Available Slots", $rows);
    }
    
    private function sendDateListReschedule($to)
    {
        $sessions = cache()->get('wa_sessions', []);
        $doctorId = $sessions[$to]['reschedule']['doctor_id'] ?? null;
        if (!$doctorId) {
            return $this->sendText($to, "Doctor not found.");
        }
    
        $rows = [];
        for ($i = 0; $i < 8; $i++) {
            $date = Carbon::today()->addDays($i);
            $dayName = strtolower($date->format('l'));
            $timing = DoctorTimings::where('doctor_id', $doctorId)->where('day', $dayName)->first();
            if (!$timing) {
                continue;
            }
    
            $slotsAvailable = false;
            if ($timing->slot_type == 'single') {
                $slots = $this->generateSlots(
                    $timing->start_time,
                    $timing->end_time,
                    $timing->slot_time_gap
                );
                if (!empty($slots)) {
                    $slotsAvailable = true;
                }
            } else {
                $firstHalf = $this->generateSlots(
                    $timing->first_half_start,
                    $timing->first_half_end,
                    $timing->slot_time_gap
                );
                $secondHalf = $this->generateSlots(
                    $timing->second_half_start,
                    $timing->second_half_end,
                    $timing->slot_time_gap
                );
                if (!empty($firstHalf) || !empty($secondHalf)) {
                    $slotsAvailable = true;
                }
            }
            if ($slotsAvailable) {
                $rows[] = [
                    "id" => $date->format('Y-m-d'),
                    "title" => $date->format('D, d M Y')
                ];
            }
        }
    
        if (empty($rows)) {
            return $this->sendText($to, "No available dates found.");
        }
        
        $sessions = cache()->get('wa_sessions', []);
        $name = $sessions[$to]['reschedule']['name'] ?? '';
        $service = $sessions[$to]['reschedule']['service'] ?? '';
        $message = "Please select a new date for your appointment:";
        return $this->sendList($to, $message, "Select Date", "Available Dates", $rows);
    }
    
    private function sendTimeListReschedule($to, $doctorId, $date)
    {
        $dayName = strtolower(Carbon::parse($date)->format('l'));
    
        $timing = DoctorTimings::where('doctor_id', $doctorId)->where('day', $dayName)->first();
        if (!$timing) {
            return $this->sendText($to, "Doctor not available.");
        }
    
        if ($timing->slot_type == 'single') {
    
            $slots = $this->generateSlots(
                $timing->start_time,
                $timing->end_time,
                $timing->slot_time_gap
            );
    
            $rows = [];
            foreach ($slots as $slot) {
                $rows[] = [
                    "id" => $slot,
                    "title" => $slot
                ];
            }
            
            $sessions = cache()->get('wa_sessions', []);
            $name = $sessions[$to]['reschedule']['name'] ?? '';
            $service = $sessions[$to]['reschedule']['service'] ?? '';
        
            $message = "Please select a new time for {$service}.";
    
            return $this->sendList( $to, $message, "Select Time", "Available Slots", $rows);
        }
    
        // DOUBLE SLOT
        $sessions = cache()->get('wa_sessions', []);
        $sessions[$to]['step'] = 'reschedule_choose_half';
        cache()->put('wa_sessions', $sessions);
    
        $rows = [
            ["id" => "first_half", "title" => "Morning"],
            ["id" => "second_half", "title" => "Evening"]
        ];
        
        $sessions = cache()->get('wa_sessions', []);
        $name = $sessions[$to]['reschedule']['name'] ?? '';
        $service = $sessions[$to]['reschedule']['service'] ?? '';
        
        $message = "Please select a slot type for {$service}.";
    
        return $this->sendList($to, $message, "Select", "Morning / Evening", $rows);
    }
}