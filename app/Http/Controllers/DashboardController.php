<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Response;
use App\Models\WhatsappAccount;
use Illuminate\View\View;

/**
 * The workspace overview. Super admins run with the tenant scope bypassed, so
 * the same queries give them platform-wide totals.
 */
class DashboardController extends Controller
{
    public function index(): View
    {
        return view('dashboard', [
            'connectedNumbers' => WhatsappAccount::where('connection_status', WhatsappAccount::STATUS_CONNECTED)->count(),
            'contacts' => Contact::count(),
            'openConversations' => Conversation::where('status', Conversation::STATUS_OPEN)->count(),
            'messagesIn' => Message::where('direction', Message::IN)->count(),
            'messagesOut' => Message::where('direction', Message::OUT)->count(),
            'responses' => Response::count(),
            'recentConversations' => Conversation::with('contact')
                ->orderByDesc('last_message_at')
                ->limit(10)
                ->get(),
        ]);
    }
}
