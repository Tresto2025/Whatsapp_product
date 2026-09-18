<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ChatSessions;
use App\Models\Appointments;
use App\Models\Cities;
use App\Models\User;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Illuminate\Support\Str;

class WhatsAppController extends Controller
{
    public function webhook(Request $request)
    {
        Log::info('📥 WhatsApp Webhook Hit', $request->all());

        $from = $request->input('From');
        $body = trim($request->input('Body'));
        $lowerBody = strtolower($body);
        $phone = str_replace('whatsapp:', '', $from);

        $session = ChatSessions::firstOrCreate(['phone' => $phone]);
        $data = $session->data ? json_decode($session->data, true) : [];

        if (preg_match('/doctor code wab-(\d+)/i', $body, $matches)) {
            $doctorId = $matches[1];
            $data['doctor_id'] = $doctorId;
            $session->data = json_encode($data);
            $session->step = 'awaiting_choice';
            $session->mode = 'menu';
            $session->save();
    
            return $this->sendListPickerTemplate($from);
        }
        
        if (!$session->step) {
            return $this->sendMessage($from, "❌ Please scan your doctor’s QR code to start the chat.");
        }

        // Main menu
        /*if ($lowerBody === 'hi') {
            $session->step = 'awaiting_choice';
            $session->mode = 'menu';
            $session->data = json_encode($data);
            $session->save();

            return $this->sendListPickerTemplate($from);
        }*/

        // Menu choice handling
        if ($session->step === 'awaiting_choice') {
            if ($body === 'new_booking') {
                $startDate = Carbon::now()->startOfDay();
                $endDate = Carbon::now()->addDays(6)->endOfDay();
        
                $appointmentCount = Appointments::where('phone', $phone)->where('status', 1)->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])->count();
        
                if ($appointmentCount >= 4) {
                    return $this->sendMessage($from, "Sorry, you have already reached the maximum of 3 appointments for this week. You cannot book a new appointment right now.");
                }
                $session->mode = 'booking';
                $session->step = 'awaiting_name';
                $session->save();
        
                return $this->sendAskNameTemplate($from);
            } elseif ($body === 'reschedule_booking') {
                $session->mode = 'rescheduling';
                $session->step = 'awaiting_reschedule_selection';
                $session->save();
        
                // Fetch all future confirmed appointments for this patient
                $dbPhone = str_replace("whatsapp:", "", $from);
                
                $appointments = Appointments::where('phone', $dbPhone)->where('status', 1)->whereDate('date', '>=', now())->with('doctor_detail')->orderBy('date', 'asc')->orderBy('start_time', 'asc')->get();
        
                if ($appointments->isEmpty()) {
                    return $this->sendMessage($from, "❌ You have no upcoming confirmed appointments.");
                }
        
                // Build the numbered list
                $list = "🔄 You have the following bookings:\n\n";
                foreach ($appointments as $index => $appt) {
                    
                    $patientName = $appt->name ?? 'N/A';
                    $doctorName = $appt->doctor_detail->name ?? 'N/A';
                    $service = $appt->service_type ?? 'N/A';
                    $purpose = $appt->purpose ?? 'N/A';
                    $date = Carbon::parse($appt->date)->format('D, d M Y');
                    $time = $appt->time ?? 'N/A';
                    $address = $appt->doctor_detail->address ?? 'N/A';
                
                    $num = $index + 1;
                    $list .= "{$num}. Patient: {$patientName}\n";
                    $list .= "Doctor: {$doctorName}\n";
                    $list .= "Service: {$service}\n";
                    $list .= "Purpose: {$purpose}\n";
                    $list .= "Date: {$date}\n";
                    $list .= "Time: {$time}\n";
                    $list .= "Address: {$address}\n\n";
                }
                $list .= "👉 Please reply with the number of the appointment you want to reschedule.";
        
                // Store appointment IDs in session
                $session->data = [
                    'appointments' => $appointments->pluck('id')->toArray()
                ];
                $session->save();
        
                return $this->sendMessage($from, $list);
            } else {
                return $this->sendMessage($from, "❌ Please scan your doctor’s QR code to start the chat.");
            }
        }

        // Booking flow
        if ($session->mode === 'booking') {
            switch ($session->step) {
        
                case 'awaiting_name':
                    $data['name'] = $body;
                    $session->data = json_encode($data);
                    $session->step = 'awaiting_service_type';
                    $session->save();
                    return $this->sendServiceTypeTemplate($from);
        
                case 'awaiting_service_type':
                    $title = $request->input('interactive.list_reply.title');
                    $id    = $request->input('interactive.list_reply.id');
                    $selectedService = $title ?: $id ?: $body;  // always prefer title
                
                    $data['service_type'] = $selectedService;
                
                    $session->data = json_encode($data);
                    $session->step = 'awaiting_date';
                    $session->save();
                
                    return $this->sendDateListNew($from);
        
                case 'awaiting_date':
                    $selectedDate = $request->input('interactive.list_reply.id') 
                        ?? $request->input('interactive.list_reply.title') 
                        ?? $body;
        
                    // fallback for numeric 1..7
                    if (is_numeric($selectedDate)) {
                        $index = (int)$selectedDate - 1;
                        if ($index >= 0 && $index < 7) {
                            $selectedDate = Carbon::now()->addDays($index)->format('Y-m-d');
                        }
                    }
        
                    $data['date'] = $selectedDate;
                    $session->data = json_encode($data);
                    $session->step = 'awaiting_time';
                    $session->save();
        
                    return $this->sendTimeTemplate($from);
        
                case 'awaiting_time':
                    $title = $request->input('interactive.list_reply.title');
                    $id    = $request->input('interactive.list_reply.id');
                    $selectedTime = $title ?: $id ?: $body;  // always prefer title
                
                    $data['time'] = $selectedTime;
                    $session->data = json_encode($data);
                    $session->step = 'awaiting_purpose';
                    $session->save();
                
                    return $this->sendPurposeTemplate($from);
        
                case 'awaiting_purpose':
                    $data['purpose'] = $body;
                    
                    $rawTime = $data['time'];
                    $startTimeString = trim(explode('-', $rawTime)[1]);
                    $startTime = Carbon::parse($startTimeString)->format('H:i:s');
        
                    // save appointment
                    $appointment = Appointments::create([
                        'phone'      => $phone,
                        'name'       => $data['name'],
                        'service_type' => $data['service_type'] ?? null,
                        'date'       => $data['date'],
                        'time'       => $data['time'],
                        'purpose'    => $data['purpose'],
                        'doctor_id'  => $data['doctor_id'] ?? null,
                        'start_time' => $startTime,
                    ]);
        
                    $session->delete();
        
                    // Confirmation message
                    $get_doctor = User::where('id', $data['doctor_id'])->first();
                    $doctorName = $get_doctor->name;
                    $address = $get_doctor->address;
                    $formattedDate = \Carbon\Carbon::parse($appointment->date)->format('D, d M Y');
                    $msg = "Thanks for your booking service with us.\n".
                           "*Here are your appointment details!*\n\n".
                           "*Date* : {$formattedDate}\n".
                           "*Time* : {$appointment->time}\n".
                           "*Service* : {$appointment->service_type}\n".
                           "*Doctor* : {$doctorName}\n\n".
                           "*Address* : {$address}";
        
                    return $this->sendMessage($from, $msg);
            }
        }

        if ($session->mode === 'rescheduling') {
            switch ($session->step) {
        
                // Step 1: Patient chooses which appointment
                case 'awaiting_reschedule_selection':
                    
                    $data = $session->data ? json_decode($session->data, true) : [];
                    $appointments = $data['appointments'] ?? [];

                    $choiceStr = preg_replace('/\D+/', '', trim((string)$body));
                    $choice = $choiceStr === '' ? 0 : intval($choiceStr);

                    if ($choice < 1 || $choice > count($appointments)) {
                        return $this->sendMessage($from, "❌ Invalid choice. Please reply with a valid number from the list.");
                    }

                    $selectedAppointmentId = $appointments[$choice - 1];
                    $data['appointment_id'] = $selectedAppointmentId;

                    $session->data = json_encode($data);
                    $session->step = 'awaiting_new_date';
                    $session->save();

                    return $this->sendDateListNew($from);

                // Step 2: Pick new date
                case 'awaiting_new_date':
                    
                    $data = $session->data ? json_decode($session->data, true) : [];

                    $selectedDate = $request->input('interactive.list_reply.id') 
                                    ?? $request->input('interactive.list_reply.title') 
                                    ?? $body;

                    $data['new_date'] = $selectedDate;

                    $session->data = json_encode($data);
                    $session->step = 'awaiting_new_time';
                    $session->save();

                    return $this->sendTimeTemplate($from);

                // Step 3: Pick new time
                case 'awaiting_new_time':
                    $data = $session->data ? json_decode($session->data, true) : [];

                    $title = $request->input('interactive.list_reply.title');
                    $id = $request->input('interactive.list_reply.id');
                    $selectedTime = $title ?: $id ?: $body;

                    $data['new_time'] = $selectedTime;

                    $rawTime = $data['new_time'];
                    $startTimeParts = explode('-', $rawTime);
                    $startTimeString = trim($startTimeParts[1] ?? $rawTime);
                    $startTime = Carbon::parse($startTimeString)->format('H:i:s');

                    $appointment = Appointments::find($data['appointment_id']);
                    if ($appointment) {
                        $appointment->update([
                            'date' => $data['new_date'],
                            'time' => $data['new_time'],
                            'start_time' => $startTime,
                        ]);
                        $session->delete();

                        $formattedDate = Carbon::parse($data['new_date'])->format('D, d M Y');
                        $get_doctor = User::find($appointment->doctor_id);
                        $doctorName = $get_doctor->name ?? '';
                        $address = $get_doctor->address ?? '';

                        return $this->sendMessage($from, "✅ Your appointment has been *rescheduled*:\n\n".
                            "*Doctor* : {$doctorName}\n".
                            "*Date* : {$formattedDate}\n".
                            "*Time* : {$data['new_time']}\n".
                            "*Service*: {$appointment->service_type}\n".
                            "*Address*: {$address}"
                        );
                    } else {
                        $session->delete();
                        return $this->sendMessage($from, "❌ Appointment not found. Please type *hi* to try again.");
                    }

                default:
                    return $this->sendMessage($from, "❌ Invalid step. Please type *hi* to start again.");
            }
        }

        return $this->sendMessage($from, "❌ Please scan your doctor’s QR code to start the chat.");
    }

    // Sends main menu (content template)
    protected function sendListPickerTemplate($to)
    {
        $twilio = new Client(
            config('services.twilio.sid'),
            config('services.twilio.token')
        );

        $twilio->messages->create($to, [
            'from' => config('services.twilio.whatsapp_from'),
            'contentSid' => 'HX68c3caf4dfaf5807a525dc852082abdd',
            //'contentSid' => 'HX6ce0f8d6c324a5d5bd99f6abd3977990', // menu_item1 new : HX68c3caf4dfaf5807a525dc852082abdd
        ]);

        return response('List menu sent', 200);
    }

    // Sends name asking template
    protected function sendAskNameTemplate($to)
    {
        $twilio = new Client(
            config('services.twilio.sid'),
            config('services.twilio.token')
        );

        $twilio->messages->create($to, [
            'from' => config('services.twilio.whatsapp_from'),
            'contentSid' => 'HX491c3cbb0907b4c300261a490299c9de',
            //'contentSid' => 'HX3d495a5593f8edf7b97607b97d0d37dc', // ask_for_name new : HX491c3cbb0907b4c300261a490299c9de
        ]);

        return response('Ask name template sent', 200);
    }

    // Sends static time slot list template
    protected function sendTimeTemplate($to)
    {
        $twilio = new Client(
            config('services.twilio.sid'),
            config('services.twilio.token')
        );

        $twilio->messages->create($to, [
            'from' => config('services.twilio.whatsapp_from'),
            'contentSid' => 'HX6f6ff36368a72a244a0f37b930bae694',
            //'contentSid' => 'HX1a741e3e8350451ebc060a087ca98c2b', // time slot template new : HX0ce927e72d9700a42d3cd55ddfd020f1
        ]);

        return response('Time template sent', 200);
    }

    // Sends city list dynamically
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

    // Sends simple message
    protected function sendMessage($to, $message)
    {
        $twilio = new Client(
            config('services.twilio.sid'),
            config('services.twilio.token')
        );

        $twilio->messages->create($to, [
            'from' => config('services.twilio.whatsapp_from'),
            'body' => $message
        ]);

        return response('OK', 200);
    }

    // Optional stubs
    protected function sendDateList($to)
    {
        return $this->sendMessage($to, "📅 Please reply with your new appointment date (YYYY-MM-DD).");
    }

    protected function sendTimeList($to)
    {
        return $this->sendMessage($to, "⏰ Please reply with your new time slot.");
    }
    
    protected function sendDateListNew($to){
        $twilio = new Client(
            config('services.twilio.sid'),
            config('services.twilio.token')
        );
        $raw = [];
        $pretty = [];
        $tz = 'Asia/Kolkata';
        for ($i = 0; $i < 7; $i++) {
            $d = Carbon::now($tz)->addDays($i);
            $raw[] = $d->format('Y-m-d');
            $pretty[] = $d->format('D, d M Y');
        }
        $vars = [
            'DateId1' => $raw[0], 'Date1' => $pretty[0],
            'DateId2' => $raw[1], 'Date2' => $pretty[1],
            'DateId3' => $raw[2], 'Date3' => $pretty[2],
            'DateId4' => $raw[3], 'Date4' => $pretty[3],
            'DateId5' => $raw[4], 'Date5' => $pretty[4],
            'DateId6' => $raw[5], 'Date6' => $pretty[5],
            'DateId7' => $raw[6], 'Date7' => $pretty[6],
        ];
        $twilio->messages->create($to, [
            'from'             => config('services.twilio.whatsapp_from'),
            'contentSid' => 'HX8c8e03fa36b0019278327cdc0786da30',
            'contentVariables' => json_encode($vars),
        ]);
        return response('Dynamic date list sent', 200);
    }
    
    // Send service type template
    protected function sendServiceTypeTemplate($to){
        $twilio = new Client(
            config('services.twilio.sid'),
            config('services.twilio.token')
        );
    
        $twilio->messages->create($to, [
            'from' => config('services.twilio.whatsapp_from'),
            'contentSid' => 'HX32adec5b7e72ee686b10452b21e6edbb'
        ]);
        return response('Service type list sent', 200);
    }
    
    // Send purpose of consultation template
    protected function sendPurposeTemplate($to){
        $twilio = new Client(
            config('services.twilio.sid'),
            config('services.twilio.token')
        );
    
        $twilio->messages->create($to, [
            'from' => config('services.twilio.whatsapp_from'),
            'contentSid' => 'HX5b430d3cda9fbf6ce0a7965c48d49d87'
        ]);
        return response('Purpose template sent', 200);
    }

}
