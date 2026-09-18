<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ChatSessions;
use App\Models\Appointments;
use App\Models\Cities;
use App\Models\User;
use App\Models\SmsBalance;
use App\Models\SmsLogs;
use App\Models\DoctorTimings;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class WhatsAppController extends Controller
{
    // --- WEBHOOK -------------------------------------------------
    public function webhook(Request $request)
    {
        Log::info('📥 WhatsApp Webhook Hit', $request->all());

        $from = $request->input('From');
        $bodyRaw = (string) $request->input('Body');
        $body = trim($bodyRaw);
        $lowerBody = strtolower($body);
        $phone = str_replace('whatsapp:', '', $from);
        
        
        
        // --- BUTTON RESPONSE HANDLER ---
        if (str_starts_with($body, 'confirm_appoint_')) {
            $appointmentId = str_replace('confirm_appoint_', '', $body);
            $appointment = Appointments::find($appointmentId);
        
            if ($appointment) {
                $appointment->status = 1; // confirmed
                $appointment->save();
                $doctorId = $appointment->doctor_id;
                return $this->sendMessage($from, "✅ Your appointment has been confirmed for {$appointment->date} at {$appointment->time}.", $doctorId);
            }
        
            return $this->sendMessage($from, "❌ Appointment not found. Please type *hi* to start again.");
        }
        
        if (str_starts_with($body, 'reschedule_appoint_')) {
            $appointmentId = str_replace('reschedule_appoint_', '', $body);
            $appointment = Appointments::find($appointmentId);
            // fetch or create session
            $session = ChatSessions::firstOrCreate(['phone' => $phone]);
        
            $session->mode = 'rescheduling';
            $session->step = 'awaiting_new_date';
            $session->data = json_encode(['appointment_id' => $appointmentId]);
            $session->save();
        
            $data = $session->data ? json_decode($session->data, true) : [];
            $doctorId = $data['doctor_id'] ?? null;
        
            if (!$doctorId) {
                if ($appointment) {
                    $doctorId = $appointment->doctor_id;
                }
            }
        Log::info('Rescheduling appointment data', [
    'appointment_id' => $appointmentId,
    'doctor_id' => $doctorId,
]);
            return $this->sendDateListNew($from, $session, $doctorId);
        }

        
        

        // fetch or create session
        $session = ChatSessions::firstOrCreate(['phone' => $phone]);

        // session expiry (15 minutes)
        if ($session->updated_at && Carbon::parse($session->updated_at)->lt(now()->subMinutes(5))) {
            $session->delete();
            $session = ChatSessions::create(['phone' => $phone]);
           // return $this->sendMessage($from, "Please enter your doctor code to start a new chat. 1");
        }

        $data = $session->data ? json_decode($session->data, true) : [];

        if (!empty($session->completed)) {
            $data = $session->data ? json_decode($session->data, true) : [];
            $doctorId = $data['doctor_id'] ?? null;
            return $this->sendMessage($from, "Please enter your doctor code to start a new chat.", $doctorId);
        }

        // ------------- GLOBAL: Reschedule Trigger -----------------
        if ($body === 'reschedule_booking' || $lowerBody === 'reschedule' || $lowerBody === 'reschedule appointment') {
    $session->mode = 'rescheduling';
    $session->step = 'awaiting_reschedule_selection';
    $session->save();

    $appointments = Appointments::where('phone', $phone)
        ->where('status', 1)
        ->whereDate('date', '>=', now())
        ->with('doctor_detail')
        ->orderBy('date', 'asc')
        ->orderBy('start_time', 'asc')
        ->get();

    if ($appointments->isEmpty()) {
        return $this->sendMessage($from, "❌ You have no upcoming confirmed appointments.");
    }

    $list = "🔄 You have the following bookings:\n\n";
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
        $list .= "{$num}. Patient: {$patientName}\n";
        $list .= "Doctor: {$doctorName} ({$doctorProfession})\n";
        $list .= "Service: {$service}\n";
        $list .= "Purpose: {$purpose}\n";
        $list .= "Date: {$date}\n";
        $list .= "Time: {$time}\n";
        $list .= "Address: {$address}\n\n";
    }

    $list .= "👉 Please reply with the number of the appointment you want to reschedule.";

    // ✅ Save appointment list + doctor_id in session
    $sessionData = [
        'appointments' => $appointments->pluck('id')->toArray(),
    ];
    if ($appointments->count() > 0) {
        $sessionData['doctor_id'] = $appointments->first()->doctor_id;
    }

    $session->data = json_encode($sessionData);
    $session->save();

    $doctorId = $sessionData['doctor_id'] ?? null;
    return $this->sendMessage($from, $list, $doctorId);
}


        // ------------- DOCTOR CODE DETECTION -----------------------
        // ------------- DOCTOR CODE DETECTION -----------------------
        $cleanBody = trim($bodyRaw);
        $bodyCode = null;
        
        // 1. Look for "send this code: 260814"
        if (preg_match('/send this code[: ]+(\d+)/i', $cleanBody, $match)) {
            $bodyCode = $match[1];
        }
        // 2. If not found, fallback: get last sequence of digits in message
        elseif (preg_match_all('/\d+/', $cleanBody, $matches)) {
            $bodyCode = end($matches[0]);
        }
        
        if ($bodyCode) {
            // Validate: must be 4-digit date + doctorId
            if (preg_match('/^(\d{4})(\d+)$/', $bodyCode, $m)) {
                $datePart = $m[1];
                $doctorId = $m[2];
        
                $doctor = User::find($doctorId);
                if (!$doctor) {
                    return $this->sendMessage($from, "❌ Wrong doctor code. Please enter the correct code.");
                }
        
                $created = Carbon::parse($doctor->created_at)->format('dm');
                $expectedCode = $created . $doctor->id;
        
                if ($bodyCode !== $expectedCode) {
                    return $this->sendMessage($from, "❌ Invalid doctor code. Please enter the correct code.");
                }
        
                // ✅ Success: store session
                $data['doctor_id'] = $doctorId;
                $session->data = json_encode($data);
                $session->step = 'awaiting_choice';
                $session->mode = 'menu';
                $session->completed = 0;
                $session->save();
                $data = $session->data ? json_decode($session->data, true) : [];
                $doctorId = $data['doctor_id'] ?? null;
        
                return $this->sendListPickerTemplate($from, $doctorId);
            }
        }

        if (!$session->step) {
            $data = $session->data ? json_decode($session->data, true) : [];
            $doctorId = $data['doctor_id'] ?? null;
            return $this->sendMessage($from, "Please enter your doctor code to start the chat.", $doctorId);
        }

        // ------------- MENU CHOICE HANDLING -----------------------
        if ($session->step === 'awaiting_choice') {
            // Only accept defined menu keywords (coming from your menu template)
            if ($body === 'new_booking') {
                $data = $session->data ? json_decode($session->data, true) : [];
                $doctorId = $data['doctor_id'] ?? null;
                $doctor = User::find($data['doctor_id']);
                if ($doctor && $doctor->booking_enabled == 0) {
                    return $this->sendMessage($from, "Sorry, bookings are closed for today. Please try again tomorrow.", $doctorId);
                }

                // weekly cap check
                $startDate = Carbon::now()->startOfDay();
                $endDate = Carbon::now()->addDays(6)->endOfDay();

                $appointmentCount = Appointments::where('phone', $phone)
                    ->where('status', 1)
                    ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                    ->count();

                if ($appointmentCount >= 3) {
                    return $this->sendMessage($from, "Sorry, you have already reached the maximum of 3 appointments for this week. You cannot book a new appointment right now.", $doctorId);
                }

                $session->mode = 'booking';
                $session->step = 'awaiting_name';
                $session->save();
                $data = $session->data ? json_decode($session->data, true) : [];
                $doctorId = $data['doctor_id'] ?? null;

                return $this->sendAskNameTemplate($from, $doctorId);
            } elseif ($body === 'reschedule_booking') {
                // handled by global trigger earlier; ignore here
                $data = $session->data ? json_decode($session->data, true) : [];
                $doctorId = $data['doctor_id'] ?? null;
                return $this->sendMessage($from, "Please follow the reschedule instructions shown earlier.", $doctorId);
            } elseif ($body === 'my_appointments') {
                $data = $session->data ? json_decode($session->data, true) : [];
                $doctorId = $data['doctor_id'] ?? null;
                $appointments = Appointments::where('phone', $phone)->where('doctor_id', $data['doctor_id'])->where('status', 1)->whereDate('date', '>=', now())->with('doctor_detail')->orderBy('date', 'asc')->orderBy('start_time', 'asc')->get();

                if ($appointments->isEmpty()) {
                    $session->update([
                        'completed' => 0,
                        'step' => null,
                        'mode' => null,
                        'data' => null,
                    ]);
                    return $this->sendMessage($from, "You have no upcoming confirmed appointments with this doctor. Please enter your doctor code again to start a new chat.", $doctorId);
                }
    
                $list = "📋 Here are your upcoming appointments:\n\n";
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
                    $list .= "{$num}. Patient: {$patientName}\n";
                    $list .= "Doctor: {$doctorName} ({$doctorProfession})\n";
                    $list .= "Service: {$service}\n";
                    $list .= "Purpose: {$purpose}\n";
                    $list .= "Date: {$date}\n";
                    $list .= "Time: {$time}\n";
                    $list .= "Address: {$address}\n\n";
                }
    
                $session->update([
                    'completed' => 0,
                    'step' => null,
                    'mode' => null,
                    'data' => null,
                ]);

                return $this->sendMessage($from, $list, $doctorId);
            } else {
                // invalid menu choice — re-send the menu
                $data = $session->data ? json_decode($session->data, true) : [];
                $doctorId = $data['doctor_id'] ?? null;
                $this->sendMessage($from, "Please choose an option from the menu below.", $doctorId);
                return $this->sendListPickerTemplate($from, $doctorId);
            }
        }

        // ---------------- BOOKING FLOW ----------------------------
        if ($session->mode === 'booking') {
            switch ($session->step) {
        
                // ------- NAME -------
                case 'awaiting_name':
                    if (mb_strlen($body) < 2 || preg_match('/\d/', $body)) {
                        $this->sendMessage($from, "Please enter your full name (letters only).");
                        return $this->sendAskNameTemplate($from);
                    }
        
                    $data['name'] = $body;
                    $session->data = json_encode($data);
                    $session->step = 'awaiting_service_type';
                    $session->save();
        
                    $doctorId = $data['doctor_id'] ?? null;
                    return $this->sendServiceTypeTemplate($from, $doctorId);
        
                // ------- SERVICE TYPE -------
                case 'awaiting_service_type':
                    $selectedService = null;
                    if (($request->input('MessageType') ?? '') === 'interactive') {
                        $selectedService = $request->input('ListTitle') ?? $request->input('ListId');
                    }
        
                    if (!$selectedService) {
                        $data = $session->data ? json_decode($session->data, true) : [];
                        $doctorId = $data['doctor_id'] ?? null;
                        $this->sendMessage($from, "❌ Please select a valid service from the list.", $doctorId);
                        return $this->sendServiceTypeTemplate($from, $doctorId);
                    }
        
                    $data['service_type'] = $selectedService;
                    $session->data = json_encode($data);
                    $session->step = 'awaiting_date';
                    $session->save();
        
                    $doctorId = $data['doctor_id'] ?? null;
                    return $this->sendDateListNew($from, $session, $doctorId);
        
                // ------- DATE -------
                case 'awaiting_date':
                    $data = json_decode($session->data ?? '{}', true);
                    $allowed = $data['allowed_dates'] ?? [];
                    $selectedDate = null;
        
                    // Determine the candidate date from user input
                    if ($this->isInteractiveListReply($request)) {
                        $candidate = $this->interactiveValue($request);
                    } else {
                        $candidate = trim($body);
                    }
        
                    // Normalize both formats before comparison
                    foreach ($allowed as $dateOption) {
                        $formatted = \Carbon\Carbon::parse($dateOption)->format('D, d M Y');
                        if (strcasecmp($candidate, $dateOption) === 0 || strcasecmp($candidate, $formatted) === 0) {
                            $selectedDate = $dateOption;
                            break;
                        }
                    }
        
                    if (!$selectedDate) {
                        $doctorId = $data['doctor_id'] ?? null;
                        $this->sendMessage($from, "Please pick a date from the list.", $doctorId);
                        return $this->sendDateListNew($from, $session, $doctorId);
                    }
        
                    $data['date'] = $selectedDate;
                    $doctorId = $data['doctor_id'] ?? null;
        
                    $timing = DoctorTimings::where('doctor_id', $doctorId)->first();
        
                    if ($timing && $timing->slot_type === 'double') {
                        // Ask for Morning / Evening slot
                        $session->data = json_encode($data);
                        $session->step = 'awaiting_preferred_slot';
                        $session->save();
        
                        $this->sendPreferredSlotTemplate($from, $doctorId);
                        return response('Sent preferred slot selection', 200);
                    }
        
                    // Single slot - send morning (template 1)
                    $session->data = json_encode($data);
                    $session->step = 'awaiting_time';
                    $session->save();
                    return $this->sendTimeTemplate($from, $session, $doctorId, 'timing_template_id_1');
        
                // ------- PREFERRED SLOT -------
                case 'awaiting_preferred_slot':
                    $data = json_decode($session->data ?? '{}', true);
                    $doctorId = $data['doctor_id'] ?? null;
                
                    // Get the selected slot from ListId if interactive, else fallback to user-typed Body
                    if ($this->isInteractiveListReply($request)) {
                        $selectedSlot = $request->input('ListId'); // Use ListId for validation
                    } else {
                        $selectedSlot = trim($body);
                    }
                
                    // Valid slot IDs
                    $validSlots = ['Morning Slot', 'Evening Slot'];
                
                    if (!in_array($selectedSlot, $validSlots)) {
                        $this->sendMessage($from, "Please select *Morning Slot* or *Evening Slot*.");
                        $this->sendPreferredSlotTemplate($from, $doctorId);
                        return response('', 200);
                    }
                
                    // Save selected slot
                    $data['preferred_slot'] = $selectedSlot;
                    $session->data = json_encode($data);
                    $session->step = 'awaiting_time';
                    $session->save();
                
                    // Choose template based on slot
                    $templateId = ($selectedSlot === 'Evening Slot') 
                                  ? 'timing_template_id_2' 
                                  : 'timing_template_id_1';
                
                    return $this->sendTimeTemplate($from, $session, $doctorId, $templateId);
                        // ------- TIME -------
                    // ------- TIME -------
                case 'awaiting_time':
                    $data = json_decode($session->data ?? '{}', true);
                    $doctorId = $data['doctor_id'] ?? null;
                
                    // Capture selected time (interactive or manual)
                    if ($this->isInteractiveListReply($request)) {
                        $selectedTime = $this->interactiveValue($request);
                    } else {
                        $selectedTime = trim($body);
                    }
                
                    if (empty($selectedTime)) {
                        $this->sendMessage($from, "Please select a time slot from the list.", $doctorId);
                        $template = ($data['preferred_slot'] ?? '') === 'Evening Slot'
                            ? 'timing_template_id_2'
                            : 'timing_template_id_1';
                        return $this->sendTimeTemplate($from, $session, $doctorId, $template);
                    }
                
                    $tz = 'Asia/Kolkata';
                    $now = \Carbon\Carbon::now($tz);
                    $selectedDate = $data['date'] ?? $now->format('Y-m-d');
                    $isToday = \Carbon\Carbon::parse($selectedDate, $tz)->isSameDay($now);
                
                    $times = explode('-', $selectedTime);
                    if (count($times) < 2) {
                        $this->sendMessage($from, "Invalid slot format. Please select a valid option.", $doctorId);
                        $template = ($data['preferred_slot'] ?? '') === 'Evening Slot'
                            ? 'timing_template_id_2'
                            : 'timing_template_id_1';
                        return $this->sendTimeTemplate($from, $session, $doctorId, $template);
                    }
                
                    $startTimeStr = trim($times[0]);
                    $endTimeStr   = trim($times[1]);
                
                    // If AM/PM missing in start, inherit from end
                    if (!str_contains($startTimeStr, 'AM') && !str_contains($startTimeStr, 'PM')) {
                        if (str_contains($endTimeStr, 'AM') || str_contains($endTimeStr, 'PM')) {
                            $startTimeStr .= ' ' . substr($endTimeStr, -2);
                        }
                    }
                
                    try {
                        $slotStartDateTime = \Carbon\Carbon::createFromFormat('Y-m-d h:i A', $selectedDate . ' ' . $startTimeStr, $tz);
                        $slotEndDateTime   = \Carbon\Carbon::createFromFormat('Y-m-d h:i A', $selectedDate . ' ' . $endTimeStr, $tz);
                    } catch (\Exception $e) {
                        $this->sendMessage($from, "Invalid time format. Please choose a valid slot.", $doctorId);
                        $template = ($data['preferred_slot'] ?? '') === 'Evening Slot'
                            ? 'timing_template_id_2'
                            : 'timing_template_id_1';
                        return $this->sendTimeTemplate($from, $session, $doctorId, $template);
                    }
                
                    // Fetch doctor timings
                    $timing = \App\Models\DoctorTimings::where('doctor_id', $doctorId)->first();
                    $isDouble = $timing && $timing->slot_type === 'double';
                
                    // If today and the selected slot is in the past
                    if ($isToday && $slotEndDateTime->lte($now)) {
                
                        if ($isDouble) {
                            // Build half ranges
                            try {
                                $firstStart = $timing->first_half_start ?? $timing->start_time;
                                $firstEnd   = $timing->first_half_end   ?? $timing->first_half_start ?? $timing->start_time;
                                $secondStart = $timing->second_half_start ?? $timing->end_time;
                                $secondEnd   = $timing->second_half_end   ?? $timing->second_half_start ?? $timing->end_time;
                
                                $firstHalfStartDT  = \Carbon\Carbon::parse($selectedDate . ' ' . $firstStart, $tz);
                                $firstHalfEndDT    = \Carbon\Carbon::parse($selectedDate . ' ' . $firstEnd, $tz);
                                $secondHalfStartDT = \Carbon\Carbon::parse($selectedDate . ' ' . $secondStart, $tz);
                                $secondHalfEndDT   = \Carbon\Carbon::parse($selectedDate . ' ' . $secondEnd, $tz);
                            } catch (\Exception $e) {
                                $this->sendMessage($from, "The selected slot is in the past. Please pick another available slot.", $doctorId);
                                $template = ($data['preferred_slot'] ?? '') === 'Evening Slot'
                                    ? 'timing_template_id_2'
                                    : 'timing_template_id_1';
                                return $this->sendTimeTemplate($from, $session, $doctorId, $template);
                            }
                
                            // Identify which half the selected slot belongs to
                            $selectedHalf = null;
                            if ($slotStartDateTime->gte($firstHalfStartDT) && $slotEndDateTime->lte($firstHalfEndDT)) {
                                $selectedHalf = 'first';
                            } elseif ($slotStartDateTime->gte($secondHalfStartDT) && $slotEndDateTime->lte($secondHalfEndDT)) {
                                $selectedHalf = 'second';
                            }
                
                            // --- First half past logic ---
                            if ($selectedHalf === 'first') {
                                if ($firstHalfEndDT->gt($now)) {
                                    $data['preferred_slot'] = 'Morning Slot';
                                    $session->data = json_encode($data);
                                    $session->step = 'awaiting_time';
                                    $session->save();
                
                                    $this->sendMessage($from, "You selected a past time. Please choose another available slot from the list below.");
                                    return $this->sendTimeTemplate($from, $session, $doctorId, 'timing_template_id_1');
                                }
                
                                if ($secondHalfEndDT->gt($now)) {
                                    unset($data['preferred_slot']);
                                    $session->data = json_encode($data);
                                    $session->step = 'awaiting_preferred_slot';
                                    $session->save();
                
                                    $this->sendMessage($from, "The selected slot has already passed. Please choose another available slot.");
                                    return $this->sendPreferredSlotTemplate($from, $doctorId);
                                }
                
                                $this->sendMessage($from, "All slots for {$selectedDate} have already passed. Please select another date.");
                                return $this->sendDateListNew($from, $session, $doctorId);
                            }
                
                            // --- Second half past logic ---
                            if ($selectedHalf === 'second') {
                                if ($secondHalfEndDT->gt($now)) {
                                    $data['preferred_slot'] = 'Evening Slot';
                                    $session->data = json_encode($data);
                                    $session->step = 'awaiting_time';
                                    $session->save();
                
                                    $this->sendMessage($from, "You selected a past time. Please choose another available slot from the list below.");
                                    return $this->sendTimeTemplate($from, $session, $doctorId, 'timing_template_id_2');
                                }
                
                                if ($firstHalfEndDT->gt($now)) {
                                    unset($data['preferred_slot']);
                                    $session->data = json_encode($data);
                                    $session->step = 'awaiting_preferred_slot';
                                    $session->save();
                
                                    $this->sendMessage($from, "The selected slot has already passed. Please choose another available slot.");
                                    return $this->sendPreferredSlotTemplate($from, $doctorId);
                                }
                
                                $this->sendMessage($from, "All slots for {$selectedDate} have already passed. Please select another date.");
                                return $this->sendDateListNew($from, $session, $doctorId);
                            }
                
                            // Fallback if half not determined
                            unset($data['preferred_slot']);
                            $session->data = json_encode($data);
                            $session->step = 'awaiting_preferred_slot';
                            $session->save();
                            $this->sendMessage($from, "The selected slot has already passed. Please choose another available slot.");
                            return $this->sendPreferredSlotTemplate($from, $doctorId);
                        }
                
                        // ---- Single-slot fallback ----
                        $this->sendMessage($from, "You selected a past time. Please pick another available slot.", $doctorId);
                        $template = ($data['preferred_slot'] ?? '') === 'Evening Slot'
                            ? 'timing_template_id_2'
                            : 'timing_template_id_1';
                        return $this->sendTimeTemplate($from, $session, $doctorId, $template);
                    }
                
                    // ✅ Passed all validations -> Save and continue
                    $data['time'] = $selectedTime;
                    $session->data = json_encode($data);
                    $session->step = 'awaiting_purpose';
                    $session->save();
                
                    return $this->sendPurposeTemplate($from, $doctorId);

                // ------- PURPOSE -------
                case 'awaiting_purpose':
                    $data = json_decode($session->data ?? '{}', true);
                    $doctorId = $data['doctor_id'] ?? null;
        
                    $data['purpose'] = $body ?: 'N/A';
                    $rawTime = $data['time'];
                    $startTimeString = trim(explode('-', $rawTime)[0] ?? $rawTime);
                    $startTime = \Carbon\Carbon::parse($startTimeString)->format('H:i:s');
        
                    $appointment = \App\Models\Appointments::create([
                        'phone'        => $phone,
                        'name'         => $data['name'],
                        'service_type' => $data['service_type'] ?? null,
                        'date'         => $data['date'],
                        'time'         => $data['time'],
                        'purpose'      => $data['purpose'],
                        'doctor_id'    => $doctorId,
                        'start_time'   => $startTime,
                    ]);
        
                    $session->delete();
        
                    $get_doctor = \App\Models\User::find($doctorId);
                    $doctorName = trim(($get_doctor->first_name ?? '') . ' ' . ($get_doctor->last_name ?? ''));
                    $doctorProfession = $get_doctor->profession_type ?? 'N/A';
                    $address = $get_doctor->address ?? '';
                    $formattedDate = \Carbon\Carbon::parse($appointment->date)->format('D, d M Y');
                    $bizDigits = preg_replace('/\D+/', '', config('services.twilio.whatsapp_from'));
                    $rescheduleLink = "https://wa.me/{$bizDigits}?text=reschedule_booking";
        
                    $msg = "Thanks {$appointment->name} for your booking.\n".
                        "*Here are your appointment details:*\n\n".
                        "*Doctor*: {$doctorName} ({$doctorProfession})\n".
                        "*Service*: {$appointment->service_type}\n".
                        "*Purpose*: {$appointment->purpose}\n".
                        "*Date*: {$formattedDate}\n".
                        "*Time*: {$appointment->time}\n\n".
                        "*Address*: {$address}\n\n".
                        "If you need to make changes later, reply with *RESCHEDULE* anytime.\n".
                        "Or tap here: {$rescheduleLink}";
        
                    $this->sendMessage($from, $msg, $doctorId);
                    return response('', 200);
                }
            }

            // ---------------- RESCHEDULING FLOW -----------------------
            if ($session->mode === 'rescheduling') {
                switch ($session->step) {
            
                    // ------- SELECT APPOINTMENT -------
                    case 'awaiting_reschedule_selection':
                        $data = $session->data ? json_decode($session->data, true) : [];
                        $doctorId = $data['doctor_id'] ?? null;
                        $appointments = $data['appointments'] ?? [];
            
                        $choiceStr = preg_replace('/\D+/', '', trim((string)$body));
                        $choice = $choiceStr === '' ? 0 : intval($choiceStr);
            
                        if ($choice < 1 || $choice > count($appointments)) {
                            return $this->sendMessage($from, "❌ Invalid choice. Please reply with a valid number from the list.", $doctorId);
                        }
            
                        $data['appointment_id'] = $appointments[$choice - 1];
                        $session->data = json_encode($data);
                        $session->step = 'awaiting_new_date';
                        $session->save();
            
                        return $this->sendDateListNew($from, $session, $doctorId);
            
                    // ------- NEW DATE -------
                    case 'awaiting_new_date':
                        $appointment = Appointments::find($data['appointment_id']);
                        if ($appointment) {
                            $data['doctor_id'] = $appointment->doctor_id;  // <-- Important!
                            $session->data = json_encode($data);
                            $session->save();
                        }
                        $data = $session->data ? json_decode($session->data, true) : [];
                        $doctorId = $data['doctor_id'] ?? null;
                        $allowedDates = $data['allowed_dates'] ?? [];
                        $selectedDate = null;
            
                        $candidate = $this->isInteractiveListReply($request) ? $this->interactiveValue($request) : trim($body);
            
                        foreach ($allowedDates as $dateOption) {
                            $formatted = \Carbon\Carbon::parse($dateOption)->format('D, d M Y');
                            if (strcasecmp($candidate, $dateOption) === 0 || strcasecmp($candidate, $formatted) === 0) {
                                $selectedDate = $dateOption;
                                break;
                            }
                        }
            
                        if (!$selectedDate) {
                            $this->sendMessage($from, "Please pick a valid date from the list.", $doctorId);
                            return $this->sendDateListNew($from, $session, $doctorId);
                        }
            
                        $data['new_date'] = $selectedDate;
            
                        $appointment = Appointments::find($data['appointment_id']);
                        if (!$appointment) {
                            $session->delete();
                            return $this->sendMessage($from, "Appointment not found. Please type *hi* to try again.");
                        }
            
                        $doctor = User::find($appointment->doctor_id);
                        $timing = DoctorTimings::where('doctor_id', $doctor->id)->first();
            
                        $session->data = json_encode($data);
            
                        if ($timing && $timing->slot_type === 'double') {
                            // Ask user to select Morning/Evening slot first
                            $session->step = 'awaiting_new_preferred_slot';
                            $session->save();
                            return $this->sendPreferredSlotTemplate($from, $doctorId);
                        }
            
                        // Single-slot doctor → send time template directly
                        $session->step = 'awaiting_new_time';
                        $session->save();
                        return $this->sendTimeTemplate($from, $session, $doctor->id, $doctor->timing_template_id_1 ?? 'HXea19f9dffe6fed7da195c6094c6a9aee');
            
                    // ------- NEW PREFERRED SLOT (for double-slot doctors) -------
                    case 'awaiting_new_preferred_slot':
                        $data = $session->data ? json_decode($session->data, true) : [];
                        $doctorId = $data['doctor_id'] ?? null;
                        $doctor = User::find($doctorId);
            
                        $selectedSlot = $this->isInteractiveListReply($request) ? $request->input('ListId') : trim($body);
            
                        $validSlots = ['Morning Slot', 'Evening Slot'];
                        if (!in_array($selectedSlot, $validSlots)) {
                            $this->sendMessage($from, "Please select *Morning Slot* or *Evening Slot*.");
                            $this->sendPreferredSlotTemplate($from, $doctorId);
                            return response('', 200);
                        }
            
                        // Save preferred slot
                        $data['preferred_slot'] = $selectedSlot;
                        $session->data = json_encode($data);
                        $session->step = 'awaiting_new_time';
                        $session->save();
            
                        // Choose template according to slot
                        $templateId = ($selectedSlot === 'Evening Slot') 
                                      ? ($doctor->timing_template_id_2 ?? 'HXea19f9dffe6fed7da195c6094c6a9aee') 
                                      : ($doctor->timing_template_id_1 ?? 'HXea19f9dffe6fed7da195c6094c6a9aee');
            
                        return $this->sendTimeTemplate($from, $session, $doctorId, $templateId);
            
                    // ------- NEW TIME -------
                    case 'awaiting_new_time':
                        $data = json_decode($session->data ?? '{}', true);
                        $doctorId = $data['doctor_id'] ?? null;
                        $appointment = Appointments::find($data['appointment_id']);
                    
                        if (!$appointment) {
                            $session->delete();
                            return $this->sendMessage($from, "Appointment not found. Please type *hi* to try again.");
                        }
                    
                        $selectedTime = $this->isInteractiveListReply($request) ? $this->interactiveValue($request) : trim($body);
                    
                        if (empty($selectedTime)) {
                            $this->sendMessage($from, "Please select a time slot from the list.", $doctorId);
                            $templateId = 'HXea19f9dffe6fed7da195c6094c6a9aee';
                            if (!empty($data['preferred_slot'])) {
                                $templateId = ($data['preferred_slot'] === 'Evening Slot')
                                    ? ($doctor->timing_template_id_2 ?? $templateId)
                                    : ($doctor->timing_template_id_1 ?? $templateId);
                            }
                            return $this->sendTimeTemplate($from, $session, $doctorId, $templateId);
                        }
                    
                        $tz = 'Asia/Kolkata';
                        $selectedDate = $data['new_date'] ?? now($tz)->format('Y-m-d');
                        $times = explode('-', $selectedTime);
                        $startTimeStr = trim($times[0]);
                        $endTimeStr   = trim($times[1]);
                    
                        if (!str_contains($startTimeStr, 'AM') && !str_contains($startTimeStr, 'PM')) {
                            if (str_contains($endTimeStr, 'AM') || str_contains($endTimeStr, 'PM')) {
                                $startTimeStr .= ' ' . substr($endTimeStr, -2);
                            }
                        }
                    
                        try {
                            $slotStartDateTime = Carbon::createFromFormat('Y-m-d h:i A', $selectedDate . ' ' . $startTimeStr, $tz);
                            $slotEndDateTime   = Carbon::createFromFormat('Y-m-d h:i A', $selectedDate . ' ' . $endTimeStr, $tz);
                        } catch (\Exception $e) {
                            $this->sendMessage($from, "Invalid time format. Please choose a valid slot.", $doctorId);
                            return $this->sendTimeTemplate($from, $session, $doctorId);
                        }
                    
                        // Doctor timings from DB
                        $timing = DoctorTimings::where('doctor_id', $doctorId)->first();
                        $isDouble = $timing && $timing->slot_type === 'double';
                        $now = Carbon::now($tz);
                        $isToday = Carbon::parse($selectedDate, $tz)->isSameDay($now);
                    
                        if ($isToday && $slotEndDateTime->lte($now)) {
                            if ($isDouble) {
                                // Double slot logic
                                $firstStart = $timing->first_half_start ?? $timing->start_time;
                                $firstEnd   = $timing->first_half_end   ?? $timing->first_half_start ?? $timing->start_time;
                                $secondStart = $timing->second_half_start ?? $timing->second_half_end ?? $timing->end_time;
                                $secondEnd   = $timing->second_half_end   ?? $timing->end_time;
                    
                                $firstHalfStartDT  = Carbon::parse($selectedDate . ' ' . $firstStart, $tz);
                                $firstHalfEndDT    = Carbon::parse($selectedDate . ' ' . $firstEnd, $tz);
                                $secondHalfStartDT = Carbon::parse($selectedDate . ' ' . $secondStart, $tz);
                                $secondHalfEndDT   = Carbon::parse($selectedDate . ' ' . $secondEnd, $tz);
                    
                                $selectedHalf = null;
                                if ($slotStartDateTime->gte($firstHalfStartDT) && $slotEndDateTime->lte($firstHalfEndDT)) {
                                    $selectedHalf = 'first';
                                } elseif ($slotStartDateTime->gte($secondHalfStartDT) && $slotEndDateTime->lte($secondHalfEndDT)) {
                                    $selectedHalf = 'second';
                                }
                    
                                if ($selectedHalf === 'first') {
                                    if ($firstHalfEndDT->gt($now)) {
                                        $session->step = 'awaiting_new_time';
                                        $session->data = json_encode($data);
                                        $session->save();
                                        $this->sendMessage($from, "You selected a past time. Please choose another available slot from the list.");
                                        return $this->sendTimeTemplate($from, $session, $doctorId, $doctor->timing_template_id_1 ?? 'HXea19f9dffe6fed7da195c6094c6a9aee');
                                    }
                                    if ($secondHalfEndDT->gt($now)) {
                                        $session->step = 'awaiting_new_preferred_slot';
                                        $session->data = json_encode($data);
                                        $session->save();
                                        $this->sendMessage($from, "The selected slot has already passed. Please choose another available slot.");
                                        return $this->sendPreferredSlotTemplate($from, $doctorId);
                                    }
                                    $this->sendMessage($from, "All slots for {$selectedDate} have already passed. Please select another date.");
                                    return $this->sendDateListNew($from, $session, $doctorId);
                                }
                    
                                if ($selectedHalf === 'second') {
                                    if ($secondHalfEndDT->gt($now)) {
                                        $session->step = 'awaiting_new_time';
                                        $session->data = json_encode($data);
                                        $session->save();
                                        $this->sendMessage($from, "You selected a past time. Please choose another available slot from the list.");
                                        return $this->sendTimeTemplate($from, $session, $doctorId, $doctor->timing_template_id_2 ?? 'HXea19f9dffe6fed7da195c6094c6a9aee');
                                    }
                                    if ($firstHalfEndDT->gt($now)) {
                                        $session->step = 'awaiting_new_preferred_slot';
                                        $session->data = json_encode($data);
                                        $session->save();
                                        $this->sendMessage($from, "The selected slot has already passed. Please choose another available slot.");
                                        return $this->sendPreferredSlotTemplate($from, $doctorId);
                                    }
                                    $this->sendMessage($from, "All slots for {$selectedDate} have already passed. Please select another date.");
                                    return $this->sendDateListNew($from, $session, $doctorId);
                                }
                    
                                // Fallback
                                $session->step = 'awaiting_new_preferred_slot';
                                $session->data = json_encode($data);
                                $session->save();
                                $this->sendMessage($from, "The selected slot has already passed. Please choose another available slot.");
                                return $this->sendPreferredSlotTemplate($from, $doctorId);
                            }
                    
                            // Single-slot doctor
                            $this->sendMessage($from, "You selected a past time. Please pick another available slot.", $doctorId);
                            $templateId = $doctor->timing_template_id_1 ?? 'HXea19f9dffe6fed7da195c6094c6a9aee';
                            return $this->sendTimeTemplate($from, $session, $doctorId, $templateId);
                        }
                    
                        // ✅ Passed validation → save and continue
                        $appointment->update([
                            'date'       => $selectedDate,
                            'time'       => $selectedTime,
                            'status'     => 1,
                            'start_time' => $slotStartDateTime->format('H:i:s'),
                        ]);
                    
                        $session->delete();
                    
                        $get_doctor = User::find($appointment->doctor_id);
                        $doctorName = trim(($get_doctor->first_name ?? '') . ' ' . ($get_doctor->last_name ?? 'N/A'));
                        $doctorProfession = $get_doctor->profession_type ?? 'N/A';
                        $address = $get_doctor->address ?? '';
                        $formattedDate = Carbon::parse($selectedDate)->format('D, d M Y');
                    
                        return $this->sendMessage($from,
                            "✅ Your appointment has been *rescheduled*:\n\n".
                            "*Doctor*: {$doctorName} ({$doctorProfession})\n".
                            "*Service*: {$appointment->service_type}\n".
                            "*Purpose*: {$appointment->purpose}\n".
                            "*Date*: {$formattedDate}\n".
                            "*Time*: {$selectedTime}\n\n".
                            "*Address*: {$address}",
                            $doctorId
                        );
            
                    default:
                        return $this->sendMessage($from, "Invalid step. Please type *hi* to start again.");
                }
            }

        return $this->sendMessage($from, "Please enter your doctor code to start the chat.");
    }

    // ---------------- HELPERS & TEMPLATES ------------------------

    // detect interactive list reply from Twilio payload
    protected function isInteractiveListReply(Request $request): bool
    {
        return $request->input('interactive.type') === 'list_reply'
            && ($request->input('interactive.list_reply.id') || $request->input('interactive.list_reply.title'));
    }

    protected function interactiveValue(Request $request): ?string
    {
        return $request->input('interactive.list_reply.id') 
            ?? $request->input('interactive.list_reply.title') 
            ?? null;
    }

    // canonical set of time slots (must match your Twilio template's slot labels)
    protected function defaultTimeSlots(): array
    {
        return [
            '09:00 AM - 10:00 AM',
            '10:00 AM - 11:00 AM',
            '11:00 AM - 12:00 PM',
            '12:00 PM - 01:00 PM',
            '01:00 PM - 02:00 PM',
            '02:00 PM - 03:00 PM',
            '03:00 PM - 04:00 PM',
            '04:00 PM - 05:00 PM',
            '05:00 PM - 06:00 PM',
            '06:00 PM - 07:00 PM',
        ];
    }

    protected function sendListPickerTemplate($to, $doctorId = null)
    {
        $twilio = new Client(config('services.twilio.sid'), config('services.twilio.token'));
        if ($doctorId) {
            $balance = SmsBalance::where('doctor_id', $doctorId)->first();
            if (!$balance || $balance->pending_sms <= 0) {
                Log::warning("Doctor {$doctorId} has no SMS balance left.");
                return false; // stop sending if no balance
            }
        }
        $sent = $twilio->messages->create($to, [
            'from' => config('services.twilio.whatsapp_from'),
            'contentSid' => 'HXb068c82796f8e73904204fbcefa0cbae',
        ]);
        if ($doctorId && $sent) {
            $balance->spent_sms += 1;
            $balance->pending_sms -= 1;
            $balance->save();
        }
    
        if ($doctorId) {
            SmsLogs::create([
                'doctor_id' => $doctorId,
                'to'        => $to,
                'message'   => "start",
                'sid'       => $sent->sid ?? null,
                'status'    => $sent->status ?? 'queued',
                'direction' => 'outgoing',
            ]);
        }
    
        return $sent;
    }

    protected function sendAskNameTemplate($to, $doctorId = null)
    {
        $twilio = new Client(config('services.twilio.sid'), config('services.twilio.token'));
        if ($doctorId) {
            $balance = SmsBalance::where('doctor_id', $doctorId)->first();
            if (!$balance || $balance->pending_sms <= 0) {
                Log::warning("Doctor {$doctorId} has no SMS balance left.");
                return false; // stop sending if no balance
            }
        }
        $sent = $twilio->messages->create($to, [
            'from' => config('services.twilio.whatsapp_from'),
            'contentSid' => 'HX491c3cbb0907b4c300261a490299c9de',
        ]);
        if ($doctorId && $sent) {
            $balance->spent_sms += 1;
            $balance->pending_sms -= 1;
            $balance->save();
        }
    
        if ($doctorId) {
            SmsLogs::create([
                'doctor_id' => $doctorId,
                'to'        => $to,
                'message'   => "Ask fo name",
                'sid'       => $sent->sid ?? null,
                'status'    => $sent->status ?? 'queued',
                'direction' => 'outgoing',
            ]);
        }
    
        return $sent;
        //return response('Ask name template sent', 200);
    }

    // Updated method to send service type template based on doctor's service_template_id
    protected function sendServiceTypeTemplate($to, $doctorId = null)
    {
        $twilio = new Client(config('services.twilio.sid'), config('services.twilio.token'));
    
        // Default service template ID
        $defaultServiceTemplateId = 'HX32adec5b7e72ee686b10452b21e6edbb';
        $serviceTemplateId = $defaultServiceTemplateId;
    
        // Get doctor's custom service template if available
        if ($doctorId) {
            $doctor = User::find($doctorId);
            if ($doctor && !empty($doctor->service_template_id)) {
                $serviceTemplateId = $doctor->service_template_id;
            }
    
            // Check balance
            $balance = SmsBalance::where('doctor_id', $doctorId)->first();
            if (!$balance || $balance->pending_sms <= 0) {
                Log::warning("Doctor {$doctorId} has no SMS balance left.");
                return false;
            }
        }
    
        $sent = $twilio->messages->create($to, [
            'from' => config('services.twilio.whatsapp_from'),
            'contentSid' => $serviceTemplateId,
        ]);
    
        if ($doctorId && $sent) {
            $balance->spent_sms += 1;
            $balance->pending_sms -= 1;
            $balance->save();
    
            SmsLogs::create([
                'doctor_id' => $doctorId,
                'to'        => $to,
                'message'   => "Sent Service Type Template ({$serviceTemplateId})",
                'sid'       => $sent->sid ?? null,
                'status'    => $sent->status ?? 'queued',
                'direction' => 'outgoing',
            ]);
        }
    
        return $sent;
    }

    // Updated method to send time template based on doctor's timing_template_id
    protected function sendTimeTemplate($to, ChatSessions $session = null, $doctorId = null)
{
    $twilio = new Client(config('services.twilio.sid'), config('services.twilio.token'));

    $defaultTemplateId = 'HXea19f9dffe6fed7da195c6094c6a9aee';
    $selectedTemplateId = $defaultTemplateId;
    $infoMessage = "Please choose from the available slots below.";

    $doctor = null;
    $allowedTimes = [];

    if ($session) {
        $data = $session->data ? json_decode($session->data, true) : [];

        // Load doctor
        if (!empty($data['doctor_id'])) {
            $doctor = User::find($data['doctor_id']);

            if ($doctor) {
                $timing = DoctorTimings::where('doctor_id', $doctor->id)->first();
                if ($timing) {
                    $slotType = $timing->slot_type ?? 'single';
                    $preferredSlot = strtolower($data['preferred_slot'] ?? 'morning slot');

                    // Single slot doctor
                    if ($slotType === 'single') {
                        $selectedTemplateId = $doctor->timing_template_id_1 ?? $defaultTemplateId;
                        $infoMessage = "Select your appointment slot.";

                        $start = strtotime($timing->start_time);
                        $end   = strtotime($timing->end_time);
                        $gap   = $timing->slot_time_gap * 60;

                        while ($start + $gap <= $end) {
                            $slotStart = date('h:i', $start);
                            $slotEnd   = date('h:i A', $start + $gap);
                            $allowedTimes[] = "$slotStart - $slotEnd";
                            $start += $gap;
                        }
                    } 
                    // Double slot doctor
                    else {
                        if ($preferredSlot === 'morning slot') {
                            $selectedTemplateId = $doctor->timing_template_id_1 ?? $defaultTemplateId;
                            $infoMessage = "Select your morning appointment slot.";

                            $start = strtotime($timing->first_half_start);
                            $end   = strtotime($timing->first_half_end);
                        } else {
                            $selectedTemplateId = $doctor->timing_template_id_2 ?? $defaultTemplateId;
                            $infoMessage = "Select your evening appointment slot.";

                            $start = strtotime($timing->second_half_start);
                            $end   = strtotime($timing->second_half_end);
                        }

                        $gap = $timing->slot_time_gap * 60;
                        while ($start + $gap <= $end) {
                            $slotStart = date('h:i', $start);
                            $slotEnd   = date('h:i A', $start + $gap);
                            $allowedTimes[] = "$slotStart - $slotEnd";
                            $start += $gap;
                        }
                    }
                }
            }
        }

        // Store allowed times in session for validation
        $data['allowed_times'] = $allowedTimes;
        $session->data = json_encode($data);
        $session->save();
    }

    // Check doctor SMS balance
    if ($doctorId) {
        $balance = SmsBalance::where('doctor_id', $doctorId)->first();
        if (!$balance || $balance->pending_sms <= 0) {
            Log::warning("Doctor {$doctorId} has no SMS balance left.");
            return false;
        }
    }

    try {
        // Send WhatsApp Template
        $sent = $twilio->messages->create($to, [
            'from' => config('services.twilio.whatsapp_from'),
            'contentSid' => $selectedTemplateId,
            'contentVariables' => json_encode([
                "Info" => $infoMessage
            ]),
        ]);

        // Deduct SMS & log
        if ($doctorId && $sent) {
            $balance->spent_sms += 1;
            $balance->pending_sms -= 1;
            $balance->save();

            SmsLogs::create([
                'doctor_id' => $doctorId,
                'to'        => $to,
                'message'   => "Sent Time Template ({$selectedTemplateId})",
                'sid'       => $sent->sid ?? null,
                'status'    => $sent->status ?? 'queued',
                'direction' => 'outgoing',
            ]);
        }

        return $sent;
    } catch (\Exception $e) {
        Log::error("❌ Failed to send time template: " . $e->getMessage());
        return false;
    }
}



    protected function sendDateListNew($to, ChatSessions $session = null, $doctorId = null)
{
    $twilio = new Client(config('services.twilio.sid'), config('services.twilio.token'));
    $tz = 'Asia/Kolkata';
    $now = Carbon::now($tz);

    $data = $session && $session->data ? json_decode($session->data, true) : [];

    // ✅ Safely check for doctor_id
    $doctorFromSession = $data['doctor_id'] ?? null;
    $activeDoctorId = $doctorFromSession ?: $doctorId;

    // if still no doctorId, return early with error
    if (!$activeDoctorId) {
        Log::error("sendDateListNew called without valid doctor_id", ['to' => $to]);
        return false;
    }

    $doctor = User::find($activeDoctorId);
    $get_time = DoctorTimings::where('doctor_id', $activeDoctorId)->first();

    $end_time = '21:00:00';
    if ($get_time) {
        $end_time = ($get_time->slot_type === "single")
            ? $get_time->end_time
            : $get_time->second_half_end;
    }

    $cutoffTime = Carbon::createFromFormat('H:i:s', $end_time, $tz);

    // Generate next 7 valid dates
    $datesRaw = [];
    $datesPretty = [];
    $daysAdded = 0;
    $i = 0;

    while ($daysAdded < 7) {
        $d = $now->copy()->addDays($i);
        if ($i === 0 && $now->greaterThanOrEqualTo($cutoffTime)) {
            $i++;
            continue;
        }
        $datesRaw[] = $d->format('Y-m-d');
        $datesPretty[] = $d->format('D, d M Y');
        $daysAdded++;
        $i++;
    }

    // Prepare Twilio template vars
    $vars = [];
    for ($j = 0; $j < 7; $j++) {
        $vars["DateId" . ($j + 1)] = $datesRaw[$j] ?? 'NA';
        $vars["Date" . ($j + 1)] = $datesPretty[$j] ?? 'NA';
    }

    // Store allowed dates back into session
    if ($session) {
        $data['allowed_dates'] = $datesRaw;
        $data['doctor_id'] = $activeDoctorId; // ✅ ensure doctor_id is stored
        $session->data = json_encode($data);
        $session->save();
    }

    // Check SMS balance
    $balance = SmsBalance::where('doctor_id', $activeDoctorId)->first();
    if (!$balance || $balance->pending_sms <= 0) {
        Log::warning("Doctor {$activeDoctorId} has no SMS balance left.");
        return false;
    }

    // Send message
    $sent = $twilio->messages->create($to, [
        'from' => config('services.twilio.whatsapp_from'),
        'contentSid' => 'HX8c8e03fa36b0019278327cdc0786da30',
        'contentVariables' => json_encode($vars),
    ]);

    if ($sent) {
        $balance->increment('spent_sms');
        $balance->decrement('pending_sms');

        SmsLogs::create([
            'doctor_id' => $activeDoctorId,
            'to'        => $to,
            'message'   => "Sent Date List Template",
            'sid'       => $sent->sid ?? null,
            'status'    => $sent->status ?? 'queued',
            'direction' => 'outgoing',
        ]);
    }

    return $sent;
}


    protected function sendPurposeTemplate($to, $doctorId = null)
    {
        $twilio = new Client(config('services.twilio.sid'), config('services.twilio.token'));
        if ($doctorId) {
            $balance = SmsBalance::where('doctor_id', $doctorId)->first();
            if (!$balance || $balance->pending_sms <= 0) {
                Log::warning("Doctor {$doctorId} has no SMS balance left.");
                return false; // stop sending if no balance
            }
        }
        $sent = $twilio->messages->create($to, [
            'from' => config('services.twilio.whatsapp_from'),
            'contentSid' => 'HX5b430d3cda9fbf6ce0a7965c48d49d87'
        ]);
        if ($doctorId && $sent) {
            $balance->spent_sms += 1;
            $balance->pending_sms -= 1;
            $balance->save();
        }
    
        if ($doctorId) {
            SmsLogs::create([
                'doctor_id' => $doctorId,
                'to'        => $to,
                'message'   => "Apointment Purpose",
                'sid'       => $sent->sid ?? null,
                'status'    => $sent->status ?? 'queued',
                'direction' => 'outgoing',
            ]);
        }
    
        return $sent;
    }

    // simple message wrapper
    protected function sendMessage($to, $message, $doctorId = null)
    {
        $twilio = new Client(config('services.twilio.sid'), config('services.twilio.token'));
        if ($doctorId) {
            $balance = SmsBalance::where('doctor_id', $doctorId)->first();
            if (!$balance || $balance->pending_sms <= 0) {
                Log::warning("Doctor {$doctorId} has no SMS balance left.");
                return false; // stop sending if no balance
            }
        }
        /*$twilio->messages->create($to, [
            'from' => config('services.twilio.whatsapp_from'),
            'body' => $message
        ]);*/
        $sent = $twilio->messages->create($to, [
            'from' => config('services.twilio.whatsapp_from'),
            'body' => $message,
        ]);
        if ($doctorId && $sent) {
            $balance->spent_sms += 1;
            $balance->pending_sms -= 1;
            $balance->save();
        }
    
        if ($doctorId) {
            SmsLogs::create([
                'doctor_id' => $doctorId,
                'to'        => $to,
                'message'   => $message,
                'sid'       => $sent->sid ?? null,
                'status'    => $sent->status ?? 'queued',
                'direction' => 'outgoing',
            ]);
        }
    
        return $sent;
    }

    // Optional: city list
    protected function sendCityList($to)
    {
        $cities = Cities::where('status', 1)->pluck('city_name')->toArray();
        if (empty($cities)) {
            return $this->sendMessage($to, "⚠️ No cities available at the moment.");
        }
        $text = "🏙️ Please reply with your city name from the following list:\n\n";
        foreach ($cities as $index => $city) {
            $text .= ($index + 1) . ". " . $city . "\n";
        }
        return $this->sendMessage($to, $text);
    }
    
    /*private function sendPreferredSlotTemplate($to)
    {
        $twilio = new \Twilio\Rest\Client(config('services.twilio.sid'), config('services.twilio.token'));
        $twilio->messages->create($to, [
            'from' => config('services.twilio.whatsapp_from'),
            'contentSid' => 'HXae7bc0de1b1b8b86a2559cf8719adbc9', // your preferred-slot template
            'contentVariables' => json_encode(new \stdClass()),
        ]);
    }*/
   private function sendPreferredSlotTemplate($to, $doctorId)
{
    \Log::info('sendPreferredSlotTemplate called', ['doctorId' => $doctorId]);

    // Fetch timings
    $timing = DoctorTimings::where('doctor_id', $doctorId)->first();
    \Log::info('DoctorTimings fetched', ['timing' => $timing]);

    if (!$timing) {
        $firstSlot  = "N/A";
        $secondSlot = "N/A";
    } else {
        $firstStart  = \Carbon\Carbon::parse($timing->first_half_start)->format('h:i');
        $firstEnd    = \Carbon\Carbon::parse($timing->first_half_end)->format('h:i A');
        $secondStart = \Carbon\Carbon::parse($timing->second_half_start)->format('h:i');
        $secondEnd   = \Carbon\Carbon::parse($timing->second_half_end)->format('h:i A');

        $firstSlot  = "$firstStart to $firstEnd";
        $secondSlot = "$secondStart to $secondEnd";
    }

    // Send message
    $twilio = new Client(config('services.twilio.sid'), config('services.twilio.token'));
    try {
        $contentVariables = [
            "one" => $firstSlot,
            "two" => $secondSlot
        ];
        \Log::info('ContentVariables before send', $contentVariables);
        
        $twilio->messages->create($to, [
            'from'             => config('services.twilio.whatsapp_from'),
            'contentSid'       => 'HXc51460c4a69bc482d734216f76abc321',
            'contentVariables' => json_encode($contentVariables, JSON_UNESCAPED_UNICODE),
        ]);

        \Log::info('Preferred slot template sent', ['to' => $to]);
    } catch (\Exception $e) {
        \Log::error('Failed to send Preferred slot template', ['error' => $e->getMessage()]);
        throw $e;
    }
}





}