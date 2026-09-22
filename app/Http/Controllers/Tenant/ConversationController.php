<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Services\WhatsApp\OutboundMessageSender;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

/**
 * The inbox: the tenant's conversations and their transcripts.
 *
 * Route-model binding on Conversation inherits the tenant global scope, so one
 * tenant can never open another tenant's thread — an out-of-scope id 404s.
 * Available to agents as well as admins; the inbox is the day-to-day workspace.
 */
class ConversationController extends Controller
{
    public function index(): View
    {
        $conversations = Conversation::with('contact')
            ->orderByDesc('last_message_at')
            ->paginate(25);

        return view('tenant.conversations.index', [
            'conversations' => $conversations,
        ]);
    }

    public function show(Conversation $conversation): View
    {
        $conversation->load('contact', 'account');

        $messages = $conversation->messages()
            ->orderBy('sent_at')
            ->orderBy('id')
            ->get();

        // Opening the thread clears its unread badge.
        if ($conversation->unread_count > 0) {
            $conversation->forceFill(['unread_count' => 0])->save();
        }

        // WhatsApp only allows a free-text reply within 24h of the contact's
        // last inbound message; outside that window an approved template is
        // required. Surface it so a rejected send is not a surprise.
        $lastInboundAt = $messages->where('direction', Message::IN)->max('sent_at');
        $withinWindow = $lastInboundAt !== null && $lastInboundAt->gt(now()->subDay());

        return view('tenant.conversations.show', [
            'conversation' => $conversation,
            'messages' => $messages,
            'canSend' => $conversation->account?->isUsable() ?? false,
            'withinWindow' => $withinWindow,
        ]);
    }

    public function reply(Request $request, Conversation $conversation, OutboundMessageSender $sender): RedirectResponse
    {
        $data = $request->validate([
            'body' => ['required', 'string', 'max:4096'],
        ]);

        try {
            $message = $sender->sendText($conversation, $data['body']);
        } catch (RuntimeException $e) {
            return back()->withInput()->withErrors(['body' => $e->getMessage()]);
        }

        if ($message->status === Message::STATUS_FAILED) {
            return back()->withInput()->withErrors([
                'body' => 'WhatsApp rejected the message: '.($message->error ?? 'unknown error'),
            ]);
        }

        return redirect()
            ->route('conversations.show', $conversation)
            ->with('success', 'Reply sent.');
    }
}
