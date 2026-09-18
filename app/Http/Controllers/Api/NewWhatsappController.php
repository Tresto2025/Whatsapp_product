<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ChatSessions;
use App\Models\Appointments;
use App\Models\Cities;
use App\Models\User;
use App\Models\SmsBalance;
use App\Models\SmsLog;
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
        
            // fetch or create session
            $session = ChatSessions::firstOrCreate(['phone' => $phone]);
        
            $session->mode = 'rescheduling';
            $session->step = 'awaiting_new_date';
            $session->data = json_encode(['appointment_id' => $appointmentId]);
            $session->save();
            $data = $session->data ? json_decode($session->data, true) : [];
            $doctorId = $data['doctor_id'] ?? null;
        
            return $this->sendDateListNew($from, $session,$doctorId);
        }
        
        

        // fetch or create session
        $session = ChatSessions::firstOrCreate(['phone' => $phone]);

        // session expiry (15 minutes)
        if ($session->updated_at && Carbon::parse($session->updated_at)->lt(now()->subMinutes(5))) {
            $session->delete();
            $session = ChatSessions::create(['phone' => $phone]);
            $data = $session->data ? json_decode($session->data, true) : [];
            $doctorId = $data['doctor_id'] ?? null;
            return $this->sendMessage($from, "Please enter your doctor code to start a new chat.", $doctorId);
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
            $data = $session->data ? json_decode($session->data, true) : [];
            $doctorId = $data['doctor_id'] ?? null;

            $appointments = Appointments::where('phone', $phone)
                ->where('status', 1)
                ->whereDate('date', '>=', now())
                ->with('doctor_detail')
                ->orderBy('date', 'asc')
                ->orderBy('start_time', 'asc')
                ->get();

            if ($appointments->isEmpty()) {
                return $this->sendMessage($from, "❌ You have no upcoming confirmed appointments.",$doctorId);
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

            $session->data = json_encode(['appointments' => $appointments->pluck('id')->toArray()]);
            $session->save();
            $data = $session->data ? json_decode($session->data, true) : [];
            $doctorId = $data['doctor_id'] ?? null;

            return $this->sendMessage($from, $list, $doctorId);
        }

        // ------------- DOCTOR CODE DETECTION -----------------------
          $cleanBody = trim($bodyRaw);
        
        // extract the longest digit sequence (code is always numeric)
        if (preg_match_all('/\d+/', $cleanBody, $matches)) {
            // take the last sequence of digits (covers "send this code: 260814")
            $bodyCode = end($matches[0]);
        
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
        
                // success: store session
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
                $data = $session->data ? json_decode($session->data, true) : [];
                $doctorId = $data['doctor_id'] ?? null;
                // handled by global trigger earlier; ignore here
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
                $data = $session->data ? json_decode($session->data, true) : [];
                $doctorId = $data['doctor_id'] ?? null;
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
                    // basic name validation: at least 2 letters, no numbers
                    if (mb_strlen($body) < 2 || preg_match('/\d/', $body)) {
                        $this->sendMessage($from, "Please enter your full name (letters only).");
                        return $this->sendAskNameTemplate($from);
                    }

                    $data['name'] = $body;
                    $session->data = json_encode($data);
                    $session->step = 'awaiting_service_type';
                    $session->save();
                    $data = $session->data ? json_decode($session->data, true) : [];
                    $doctorId = $data['doctor_id'] ?? null;

                    return $this->sendServiceTypeTemplate($from, $doctorId);

                // ------- SERVICE TYPE -------
                case 'awaiting_service_type':
                    $selectedService = null;
                
                    if ($this->isInteractiveListReply($request)) {
                        $selectedService = $this->interactiveValue($request);
                    }
                
                    elseif (!empty($body)) {
                        $validServices = ['General (OPD)', 'Obstetrics & Gynecology', 'Gastroenterology', 'Orthopedics', 'Traumatology', 'Neuropsychiatry']; // change to your service names
                        if (in_array($body, $validServices, true)) {
                            $selectedService = $body;
                        }
                    }
                
                    if (!$selectedService) {
                        $data = $session->data ? json_decode($session->data, true) : [];
                        $doctorId = $data['doctor_id'] ?? null;
                        $this->sendMessage($from, "❌ Please select a valid service from the list.", $doctorId);
                        return $this->sendServiceTypeTemplate($from);
                    }
                    $data['service_type'] = $selectedService;
                    $session->data = json_encode($data);
                    $session->step = 'awaiting_date';
                    $session->save();
                    $data = $session->data ? json_decode($session->data, true) : [];
                    $doctorId = $data['doctor_id'] ?? null;
                
                    return $this->sendDateListNew($from, $session, $doctorId);

                // ------- DATE -------
                case 'awaiting_date':
                    $data = $session->data ? json_decode($session->data, true) : [];
                    $allowed = $data['allowed_dates'] ?? [];

                    $selectedDate = null;

                    // If interactive list reply, take that
                    if ($this->isInteractiveListReply($request)) {
                        $candidate = $this->interactiveValue($request);
                        if (in_array($candidate, $allowed, true)) {
                            $selectedDate = $candidate;
                        }
                    } elseif (ctype_digit($body)) {
                        // user typed 1..7
                        $idx = (int)$body - 1;
                        if (isset($allowed[$idx])) {
                            $selectedDate = $allowed[$idx];
                        }
                    } elseif (preg_match('/^\d{4}-\d{2}-\d{2}$/', $body) && in_array($body, $allowed, true)) {
                        $selectedDate = $body;
                    }

                    if (!$selectedDate) {
                        $data = $session->data ? json_decode($session->data, true) : [];
                        $doctorId = $data['doctor_id'] ?? null;
                        $this->sendMessage($from, "Please pick a date from the list.", $doctorId);
                        return $this->sendDateListNew($from, $session, $doctorId);
                    }

                    $data['date'] = $selectedDate;
                    $session->data = json_encode($data);
                    $session->step = 'awaiting_time';
                    $session->save();
                    $data = $session->data ? json_decode($session->data, true) : [];
                    $doctorId = $data['doctor_id'] ?? null;

                    // send time template and store allowed_times
                    return $this->sendTimeTemplate($from, $session, $doctorId);

                // ------- TIME -------
                case 'awaiting_time':
                    $data = $session->data ? json_decode($session->data, true) : [];
                    $doctorId = $data['doctor_id'] ?? null;
                    // Define hardcoded valid slots
                    $validSlots = [
                        "09:00 - 10:00 AM",
                        "10:00 - 11:00 AM",
                        "11:00 - 12:00 PM",
                        "12:00 - 01:00 PM",
                        "01:00 - 02:00 PM",
                        "02:00 - 03:00 PM",
                        "03:00 - 04:00 PM",
                        "04:00 - 05:00 PM",
                        "05:00 - 06:00 PM",
                        "06:00 - 07:00 PM",
                    ];
                
                    // Use either stored allowed times or fallback to valid slots
                    $allowedTimes = $data['allowed_times'] ?? $validSlots;
                
                    $selectedTime = null;
                
                    // Case 1: Interactive reply
                    if ($this->isInteractiveListReply($request)) {
                        $selectedTime = $this->interactiveValue($request);
                    }
                    // Case 2: Manual text input (must match one of the valid slots)
                    elseif (!empty($body) && in_array($body, $validSlots, true)) {
                        $selectedTime = $body;
                    }
                
                    if (!$selectedTime || !in_array($selectedTime, $validSlots, true)) {
                        $this->sendMessage($from, "Please select a valid time slot from the list below.", $doctorId);
                        return $this->sendTimeTemplate($from, $session, $doctorId);
                    }
                
                    // Validate not past for today
                    $tz = 'Asia/Kolkata';
                    $now = Carbon::now($tz);
                    $selectedDate = $data['date'] ?? $now->format('Y-m-d');
                    $isToday = Carbon::parse($selectedDate, $tz)->isSameDay($now);
                
                    $times = explode('-', $selectedTime);
                    if (count($times) < 2) {
                        $this->sendMessage($from, "Invalid slot format. Please select a valid option.", $doctorId);
                        return $this->sendTimeTemplate($from, $session, $doctorId);
                    }
                
                    $startTimeStr = trim($times[0]);
                    $endTimeStr   = trim($times[1]);
                
                    // If start missing AM/PM, inherit from end
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
                
                    if ($isToday && $slotEndDateTime->lessThanOrEqualTo($now)) {
                        $this->sendMessage($from, "You selected a past slot. Please pick a future time.", $doctorId);
                        return $this->sendTimeTemplate($from, $session, $doctorId);
                    }
                
                    $doctor = User::find($data['doctor_id']);
                    if ($doctor && $doctor->start_time && $doctor->end_time) {
                        $doctorStart = Carbon::createFromFormat('H:i:s', $doctor->start_time, $tz)
                            ->setDateFrom(Carbon::parse($selectedDate, $tz));
                        $doctorEnd   = Carbon::createFromFormat('H:i:s', $doctor->end_time, $tz)
                            ->setDateFrom(Carbon::parse($selectedDate, $tz));
                
                        if ($slotStartDateTime->lt($doctorStart) || $slotEndDateTime->gt($doctorEnd)) {
                            $this->sendMessage($from,"You can book appointments only between " .$doctorStart->format('h:i A') . " and " . $doctorEnd->format('h:i A'), $doctorId);
                            return $this->sendTimeTemplate($from, $session, $doctorId);

                        }
                    }
                
                    if ($doctor && $doctor->appointment_mode == 2) {
                        $doctorStart = $doctor->start_time
                            ? Carbon::createFromFormat('Y-m-d H:i:s', $selectedDate . ' ' . $doctor->start_time, $tz)
                            : null;
                    
                        $doctorEnd = $doctor->end_time
                            ? Carbon::createFromFormat('Y-m-d H:i:s', $selectedDate . ' ' . $doctor->end_time, $tz)
                            : null;
                    
                        $filteredSlots = array_filter($validSlots, function ($slot) use ($selectedDate, $tz, $doctorStart, $doctorEnd) {
                            [$start, $end] = array_map('trim', explode('-', $slot));
                    
                            if (!str_contains($start, 'AM') && !str_contains($start, 'PM')) {
                                $start .= ' ' . substr($end, -2);
                            }
                    
                            $slotStart = Carbon::createFromFormat('Y-m-d h:i A', $selectedDate . ' ' . $start, $tz);
                            $slotEnd   = Carbon::createFromFormat('Y-m-d h:i A', $selectedDate . ' ' . $end, $tz);
                    
                            return (!$doctorStart || !$doctorEnd) || (
                                $slotStart->gte($doctorStart) && $slotEnd->lte($doctorEnd)
                            );
                        });
                    
                        $bookedSlots = Appointments::where('doctor_id', $doctor->id)->where('date', $selectedDate)->pluck('time')->toArray();
                    
                        $availableSlots = array_diff($filteredSlots, $bookedSlots);
                    
                        if (in_array($selectedTime, $bookedSlots, true)) {
                            if (empty($availableSlots)) {
                                $this->sendMessage($from, "All appointments for {$selectedDate} are booked. Please select another date.", $doctorId);
                                return $this->sendDateTemplate($from, $session, $doctorId);
                            } else {
                                $slotsStr = implode("\n", $availableSlots);
                                $this->sendMessage($from, "This slot is not available. Available slots for {$selectedDate}:\n{$slotsStr}", $doctorId);
                                return $this->sendTimeTemplate($from, $session, $doctorId);
                            }
                        }
                    }
                
                    $data['time'] = $selectedTime;
                    $session->data = json_encode($data);
                    $session->step = 'awaiting_purpose';
                    $session->save();
                    $data = $session->data ? json_decode($session->data, true) : [];
                    $doctorId = $data['doctor_id'] ?? null;
                
                    return $this->sendPurposeTemplate($from, $doctorId);


                // ------- PURPOSE & SAVE -------
                case 'awaiting_purpose':
                    $data = $session->data ? json_decode($session->data, true) : [];
                    $doctorId = $data['doctor_id'] ?? null;

                    $data['purpose'] = $body ?: 'N/A';

                    // store appointment: use left part of slot as start_time
                    $rawTime = $data['time'];
                    $startTimeString = trim(explode('-', $rawTime)[0] ?? $rawTime);
                    $startTime = Carbon::parse($startTimeString)->format('H:i:s');

                    $appointment = Appointments::create([
                        'phone'        => $phone,
                        'name'         => $data['name'],
                        'service_type' => $data['service_type'] ?? null,
                        'date'         => $data['date'],
                        'time'         => $data['time'],
                        'purpose'      => $data['purpose'],
                        'doctor_id'    => $data['doctor_id'] ?? null,
                        'start_time'   => $startTime,
                    ]);

                    $session->delete();

                    $get_doctor = User::where('id', $data['doctor_id'])->first();
                    $doctorFirstName  = $get_doctor->first_name ?? '';
                    $doctorLastName   = $get_doctor->last_name ?? '';
                    $doctorName       = trim($doctorFirstName . ' ' . $doctorLastName) ?: 'N/A';
                    $doctorProfession = $get_doctor->profession_type ?? 'N/A';
                    $address          = $get_doctor->address ?? '';
                    $formattedDate    = Carbon::parse($appointment->date)->format('D, d M Y');

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

                    // try send reschedule button template (optional)
                    try {
                        $twilio = new Client(config('services.twilio.sid'), config('services.twilio.token'));
                        $twilio->messages->create($from, [
                            'from' => config('services.twilio.whatsapp_from'),
                            'contentSid' => 'HXe8a98c6233096de9b35c01f753fe618f',
                            'contentVariables' => json_encode(new \stdClass()),
                        ]);
                        \Log::info("✅ Reschedule button template sent");
                    } catch (\Exception $e) {
                        \Log::error("❌ Error sending reschedule button template: " . $e->getMessage());
                    }

                    return response('', 200);
            } // end switch booking
        } // end booking mode

        // ---------------- RESCHEDULING FLOW -----------------------
        if ($session->mode === 'rescheduling') {
            switch ($session->step) {

                case 'awaiting_reschedule_selection':
                    $data = $session->data ? json_decode($session->data, true) : [];
                    $doctorId = $data['doctor_id'] ?? null;
                    $appointments = $data['appointments'] ?? [];

                    $choiceStr = preg_replace('/\D+/', '', trim((string)$body));
                    $choice = $choiceStr === '' ? 0 : intval($choiceStr);

                    if ($choice < 1 || $choice > count($appointments)) {
                        return $this->sendMessage($from, "❌ Invalid choice. Please reply with a valid number from the list.", $doctorId);
                    }

                    $selectedAppointmentId = $appointments[$choice - 1];
                    $data['appointment_id'] = $selectedAppointmentId;

                    $session->data = json_encode($data);
                    $session->step = 'awaiting_new_date';
                    $session->save();
                    $data = $session->data ? json_decode($session->data, true) : [];
                    $doctorId = $data['doctor_id'] ?? null;

                    return $this->sendDateListNew($from, $session, $doctorId);

                case 'awaiting_new_date':
                    $data = $session->data ? json_decode($session->data, true) : [];
                    $doctorId = $data['doctor_id'] ?? null;
                    $allowed = $data['allowed_dates'] ?? [];
                
                    $selectedDate = null;
                
                    if ($this->isInteractiveListReply($request)) {
                        $candidate = $this->interactiveValue($request);
                        if (in_array($candidate, $allowed, true)) {
                            $selectedDate = $candidate;
                        }
                    }
                    elseif (preg_match('/^\d{4}-\d{2}-\d{2}$/', $body) && in_array($body, $allowed, true)) {
                        $selectedDate = $body;
                    }
                
                    if (!$selectedDate) {
                        $this->sendMessage($from, "Please pick a valid date from the list.", $doctorId);
                        return $this->sendDateListNew($from, $session, $doctorId);
                    }
                
                    $data['new_date'] = $selectedDate;
                
                    if (!empty($data['appointment_id'])) {
                        $appointment = Appointments::find($data['appointment_id']);
                        if ($appointment) {
                            $data['doctor_id'] = $appointment->doctor_id;
                        }
                    }
                
                    $session->data = json_encode($data);
                    $session->step = 'awaiting_new_time';
                    $session->save();
                    $data = $session->data ? json_decode($session->data, true) : [];
                    $doctorId = $data['doctor_id'] ?? null;
                
                    return $this->sendTimeTemplate($from, $session, $doctorId);

                case 'awaiting_new_time':
                    $data = $session->data ? json_decode($session->data, true) : [];
                    $doctorId = $data['doctor_id'] ?? null;

                    $validSlots = [
                        "09:00 - 10:00 AM",
                        "10:00 - 11:00 AM",
                        "11:00 - 12:00 PM",
                        "12:00 - 01:00 PM",
                        "01:00 - 02:00 PM",
                        "02:00 - 03:00 PM",
                        "03:00 - 04:00 PM",
                        "04:00 - 05:00 PM",
                        "05:00 - 06:00 PM",
                        "06:00 - 07:00 PM",
                    ];
                
                    $allowedTimes = $data['allowed_times'] ?? $validSlots;
                    $selectedTime = null;
                
                    if ($this->isInteractiveListReply($request)) {
                        $selectedTime = $this->interactiveValue($request);
                    } elseif (!empty($body) && in_array($body, $validSlots, true)) {
                        $selectedTime = $body;
                    }
                
                    if (!$selectedTime || !in_array($selectedTime, $validSlots, true)) {
                        $this->sendMessage($from, "Please select a valid time slot from the list below.", $doctorId);
                        return $this->sendTimeTemplate($from, $session, $doctorId);
                    }
                
                    $tz = 'Asia/Kolkata';
                    $now = Carbon::now($tz);
                    $selectedDate = $data['new_date'] ?? $now->format('Y-m-d');
                    $isToday = Carbon::parse($selectedDate, $tz)->isSameDay($now);
                
                    $times = explode('-', $selectedTime);
                    if (count($times) < 2) {
                        $this->sendMessage($from, "Invalid slot format. Please select again.", $doctorId);
                        return $this->sendTimeTemplate($from, $session, $doctorId);
                    }
                
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
                
                    if ($isToday && $slotEndDateTime->lessThanOrEqualTo($now)) {
                        $this->sendMessage($from, "You selected a past slot. Please pick a future time.", $doctorId);
                        return $this->sendTimeTemplate($from, $session, $doctorId);
                    }
                
                    $appointment = Appointments::find($data['appointment_id']);
                    if ($appointment) {
                        $doctor = User::find($appointment->doctor_id);
                
                        if ($doctor) {
                            $doctorStart = $doctor->start_time
                                ? Carbon::createFromFormat('Y-m-d H:i:s', $selectedDate . ' ' . $doctor->start_time, $tz)
                                : null;
                
                            $doctorEnd = $doctor->end_time
                                ? Carbon::createFromFormat('Y-m-d H:i:s', $selectedDate . ' ' . $doctor->end_time, $tz)
                                : null;
                
                            if ($doctorStart && $doctorEnd &&
                                ($slotStartDateTime->lt($doctorStart) || $slotEndDateTime->gt($doctorEnd))) {
                                $this->sendMessage($from, "You can reschedule only between " .
                                    $doctorStart->format('h:i A') . " and " . $doctorEnd->format('h:i A'), $doctorId);
                                return $this->sendTimeTemplate($from, $session, $doctorId);
                            }
                
                            $existing = Appointments::where('doctor_id', $doctor->id)->where('date', $selectedDate)->where('time', $selectedTime)->where('id', '!=', $appointment->id)->first();
                
                            if ($existing) {
                                $filteredSlots = array_filter($validSlots, function ($slot) use ($selectedDate, $tz, $doctorStart, $doctorEnd) {
                                    [$s, $e] = array_map('trim', explode('-', $slot));
                                    if (!str_contains($s, 'AM') && !str_contains($s, 'PM')) {
                                        $s .= ' ' . substr($e, -2);
                                    }
                                    $slotS = Carbon::createFromFormat('Y-m-d h:i A', $selectedDate . ' ' . $s, $tz);
                                    $slotE = Carbon::createFromFormat('Y-m-d h:i A', $selectedDate . ' ' . $e, $tz);
                
                                    return (!$doctorStart || !$doctorEnd) || (
                                        $slotS->gte($doctorStart) && $slotE->lte($doctorEnd)
                                    );
                                });
                
                                $bookedSlots = Appointments::where('doctor_id', $doctor->id)->where('date', $selectedDate)->pluck('time')->toArray();
                                $availableSlots = array_diff($filteredSlots, $bookedSlots);
                                if (empty($availableSlots)) {
                                    $this->sendMessage($from, "All appointments for {$selectedDate} are booked. Please select another date.", $doctorId);
                                    return $this->sendDateListNew($from, $session, $doctorId);
                                } else {
                                    $slotsStr = implode("\n", $availableSlots);
                                    $this->sendMessage($from, "This slot is not available. Available slots for {$selectedDate}:\n{$slotsStr}", $doctorId);
                                    return $this->sendTimeTemplate($from, $session, $doctorId);
                                }
                            }
                        }
                
                        $appointment->update([
                            'date'       => $selectedDate,
                            'time'       => $selectedTime,
                            'status'       => 1,
                            'start_time' => $slotStartDateTime->format('H:i:s'),
                        ]);
                
                        $session->delete();
                
                        $formattedDate = Carbon::parse($selectedDate)->format('D, d M Y');
                        $get_doctor = $doctor;
                        $doctorFirstName = $get_doctor->first_name ?? '';
                        $doctorLastName  = $get_doctor->last_name ?? '';
                        $doctorName      = trim($doctorFirstName . ' ' . $doctorLastName) ?: 'N/A';
                        $doctorProfession = $get_doctor->profession_type ?? 'N/A';
                        $address = $get_doctor->address ?? '';
                
                        return $this->sendMessage($from, "✅ Your appointment has been *rescheduled*:\n\n".
                            "*Doctor* : {$doctorName} ({$doctorProfession})\n".
                            "*Service*: {$appointment->service_type}\n".
                            "*Purpose*: {$appointment->purpose}\n".
                            "*Date* : {$formattedDate}\n".
                            "*Time* : {$selectedTime}\n\n".
                            "*Address*: {$address}"
                        , $doctor->id);
                    } else {
                        $session->delete();
                        return $this->sendMessage($from, "Appointment not found. Please type *hi* to try again.");
                    }

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
            SmsLog::create([
                'doctor_id' => $doctorId,
                'to'        => $to,
                'message'   => $message,
                'sid'       => $sent->sid ?? null,
                'status'    => $sent->status ?? 'queued',
                'direction' => 'outgoing',
            ]);
        }
    
        return $sent;
        //return response('List menu sent', 200);
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
            SmsLog::create([
                'doctor_id' => $doctorId,
                'to'        => $to,
                'message'   => $message,
                'sid'       => $sent->sid ?? null,
                'status'    => $sent->status ?? 'queued',
                'direction' => 'outgoing',
            ]);
        }
    
        return $sent;
        //return response('Ask name template sent', 200);
    }

    // send time template; optionally pass session to persist allowed_times
    protected function sendTimeTemplate($to, ChatSessions $session = null, $doctorId = null)
    {
        // Save allowed times into session data for later validation
        $infoMessage = "Please choose from the available slots below."; // default
    
        if ($session) {
            $data = $session->data ? json_decode($session->data, true) : [];
            $data['allowed_times'] = $this->defaultTimeSlots();
            $session->data = json_encode($data);
            $session->save();
    
            // Check if doctor exists and has working hours
            if (!empty($data['doctor_id'])) {
                $doctor = User::find($data['doctor_id']);
                if ($doctor && $doctor->start_time && $doctor->end_time) {
                    $tz = 'Asia/Kolkata';
                    $doctorStart = Carbon::createFromFormat('H:i:s', $doctor->start_time, $tz)->format('h:i A');
                    $doctorEnd   = Carbon::createFromFormat('H:i:s', $doctor->end_time, $tz)->format('h:i A');
    
                    $infoMessage = "You can book appointments only between {$doctorStart} and {$doctorEnd}.";
                }
            }
        }
    
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
            'contentSid' => 'HXea19f9dffe6fed7da195c6094c6a9aee',
            'contentVariables' => json_encode([
                "Info" => $infoMessage
            ])
        ]);
        if ($doctorId && $sent) {
            $balance->spent_sms += 1;
            $balance->pending_sms -= 1;
            $balance->save();
        }
    
        if ($doctorId) {
            SmsLog::create([
                'doctor_id' => $doctorId,
                'to'        => $to,
                'message'   => $message,
                'sid'       => $sent->sid ?? null,
                'status'    => $sent->status ?? 'queued',
                'direction' => 'outgoing',
            ]);
        }
    
        return $sent;
        //return response('Time template sent', 200);
    }

    // send dynamic date list (and store allowed_dates in session if provided)
    protected function sendDateListNew($to, ChatSessions $session = null, $doctorId = null)
    {
        $twilio = new Client(config('services.twilio.sid'), config('services.twilio.token'));

        $tz = 'Asia/Kolkata';
        $now = Carbon::now($tz);

        // Cutoff time 16:00 (skip today if after cutoff)
        $cutoffTime = Carbon::createFromFormat('H:i:s', '16:00:00', $tz);

        $datesRaw = [];
        $datesPretty = [];

        $daysAdded = 0;
        $i = 0;

        while ($daysAdded < 7) {
            $d = $now->copy()->addDays($i);

            // Today cutoff check
            if ($i === 0 && $now->greaterThanOrEqualTo($cutoffTime)) {
                $i++;
                continue; // skip today
            }

            $datesRaw[] = $d->format('Y-m-d');
            $datesPretty[] = $d->format('D, d M Y');

            $daysAdded++;
            $i++;
        }

        // Prepare template variables (DateId1..DateId7 and Date1..Date7)
        $vars = [];
        for ($j = 0; $j < 7; $j++) {
            $vars["DateId" . ($j + 1)] = $datesRaw[$j] ?? 'NA';
            $vars["Date" . ($j + 1)] = $datesPretty[$j] ?? 'NA';
        }

        // Save allowed_dates in session for validation
        if ($session) {
            $data = $session->data ? json_decode($session->data, true) : [];
            $data['allowed_dates'] = $datesRaw;
            $session->data = json_encode($data);
            $session->save();
        }

        if ($doctorId) {
            $balance = SmsBalance::where('doctor_id', $doctorId)->first();
            if (!$balance || $balance->pending_sms <= 0) {
                Log::warning("Doctor {$doctorId} has no SMS balance left.");
                return false; // stop sending if no balance
            }
        }
        $sent = $twilio->messages->create($to, [
            'from' => config('services.twilio.whatsapp_from'),
            'contentSid' => 'HX8c8e03fa36b0019278327cdc0786da30',
            'contentVariables' => json_encode($vars),
        ]);
        if ($doctorId && $sent) {
            $balance->spent_sms += 1;
            $balance->pending_sms -= 1;
            $balance->save();
        }
    
        if ($doctorId) {
            SmsLog::create([
                'doctor_id' => $doctorId,
                'to'        => $to,
                'message'   => $message,
                'sid'       => $sent->sid ?? null,
                'status'    => $sent->status ?? 'queued',
                'direction' => 'outgoing',
            ]);
        }
    
        return $sent;
        //return response('Dynamic date list sent', 200);
    }

    protected function sendServiceTypeTemplate($to, $doctorId = null)
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
            'contentSid' => 'HX32adec5b7e72ee686b10452b21e6edbb'
        ]);
        if ($doctorId && $sent) {
            $balance->spent_sms += 1;
            $balance->pending_sms -= 1;
            $balance->save();
        }
    
        if ($doctorId) {
            SmsLog::create([
                'doctor_id' => $doctorId,
                'to'        => $to,
                'message'   => $message,
                'sid'       => $sent->sid ?? null,
                'status'    => $sent->status ?? 'queued',
                'direction' => 'outgoing',
            ]);
        }
    
        return $sent;
        //return response('Service type list sent', 200);
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
            SmsLog::create([
                'doctor_id' => $doctorId,
                'to'        => $to,
                'message'   => $message,
                'sid'       => $sent->sid ?? null,
                'status'    => $sent->status ?? 'queued',
                'direction' => 'outgoing',
            ]);
        }
    
        return $sent;
        //return response('Purpose template sent', 200);
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
            SmsLog::create([
                'doctor_id' => $doctorId,
                'to'        => $to,
                'message'   => $message,
                'sid'       => $sent->sid ?? null,
                'status'    => $sent->status ?? 'queued',
                'direction' => 'outgoing',
            ]);
        }
    
        return $sent;
        //return response('OK', 200);
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
}
