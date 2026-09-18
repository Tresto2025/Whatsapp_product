<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ChatSessions;

class RedirectController extends Controller
{
    public function redirectToWhatsapp(User $doctor, Request $request)
    {
        // Extract phone from query param (Twilio will add when chat starts)
        $phone = $request->input('phone'); // might be null now

        // Save doctor_id in ChatSessions (we don’t know phone yet, so just log doctor)
        session(['doctor_id' => $doctor->id]);

        // WhatsApp number
        $whatsappNumber = '14155238886'; // your Twilio WhatsApp number
        $message = urlencode("Hi");
//echo session('doctor_id'); die;
        // Redirect to WhatsApp with prefilled Hi
        $whatsappUrl = "https://wa.me/{$whatsappNumber}?text={$message}";

        return redirect()->away($whatsappUrl);
    }
}
