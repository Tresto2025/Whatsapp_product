<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ChatSessions;
use App\Models\Appointments;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\Log;

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

        // 🟢 Start Flow
        if ($lowerBody === 'hi') {
            $session->step = 'awaiting_choice';
            $session->mode = 'menu';
            $session->data = null;
            $session->save();
            return $this->sendMenuTemplate($from); // menu_item1
        }

        // 🟡 Menu Selection
        if ($session->step === 'awaiting_choice') {
            if ($body === 'new_booking') {
                $session->mode = 'booking';
                $session->step = 'awaiting_name';
                $session->save();
                return $this->sendAskNameTemplate($from); // ask for name
            } else {
                return $this->sendMessage($from, "❌ Invalid option. Please reply with *1* to book an appointment.");
            }
        }

        // 🔵 Booking Flow
        if ($session->mode === 'booking') {
            switch ($session->step) {
                case 'awaiting_name':
                    $data['name'] = $body;
                    $session->data = json_encode($data);
                    $session->step = 'awaiting_date';
                    $session->save();
                    return $this->sendMessage($from, "📅 Please type your preferred appointment date in *YYYY-MM-DD* format.");

                case 'awaiting_date':
                    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $body)) {
                        return $this->sendMessage($from, "⚠️ Invalid date format. Please type in *YYYY-MM-DD* format.");
                    }

                    $data['date'] = $body;
                    $session->data = json_encode($data);
                    $session->step = 'awaiting_time';
                    $session->save();
                    return $this->sendAskTimeTemplate($from); // time slot picker

                case 'awaiting_time':
                    if ($request->has('interactive.list_reply')) {
                        $selectedTime = $request->input('interactive.list_reply.id');
                    } else {
                        return $this->sendMessage($from, "⚠️ Please select a time slot from the list.");
                    }

                    $data['time'] = $selectedTime;

                    Appointments::create([
                        'phone' => $phone,
                        'name' => $data['name'],
                        'date' => $data['date'],
                        'time' => $data['time'],
                    ]);

                    $session->delete();

                    return $this->sendMessage($from,
                        "✅ *Appointment Confirmed!*\n\n👤 Name: {$data['name']}\n📅 Date: {$data['date']}\n⏰ Time: {$data['time']}\n\nThank you!"
                    );
            }
        }

        return $this->sendMessage($from, "❓ I didn't understand that. Please type *hi* to begin.");
    }

    // 📩 Send Main Menu (menu_item1)
    protected function sendMenuTemplate($to)
    {
        $twilio = new Client(
            config('services.twilio.sid'),
            config('services.twilio.token')
        );

        $twilio->messages->create($to, [
            'from' => config('services.twilio.whatsapp_from'),
            'contentSid' => 'HX6ce0f8d6c324a5d5bd99f6abd3977990', // menu_item1 template SID
        ]);

        return response('Main menu sent', 200);
    }

    // 📩 Ask for Name Template
    protected function sendAskNameTemplate($to)
    {
        $twilio = new Client(
            config('services.twilio.sid'),
            config('services.twilio.token')
        );

        $twilio->messages->create($to, [
            'from' => config('services.twilio.whatsapp_from'),
            'contentSid' => 'HXasknameTemplateSID1234567890', // Replace with real SID
        ]);

        return response('Ask name sent', 200);
    }

    // 📩 Ask for Time (List Template)
    protected function sendAskTimeTemplate($to)
    {
        $twilio = new Client(
            config('services.twilio.sid'),
            config('services.twilio.token')
        );

        $twilio->messages->create($to, [
            'from' => config('services.twilio.whatsapp_from'),
            'contentSid' => 'HX1a741e3e8350451ebc060a087ca98c2b', // Replace with real SID
        ]);

        return response('Ask time sent', 200);
    }

    // 💬 Fallback Plain Message
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

        return response('Message sent', 200);
    }
}
