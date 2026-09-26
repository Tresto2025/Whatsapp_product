<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\ChatbotFlow;
use App\Models\FlowStep;
use App\Models\FlowTrigger;
use App\Models\WhatsappTemplate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * MVP no-code chatbot: one keyword trigger -> one immediate reply per flow.
 *
 * `flow_steps`/`flow_triggers` support a richer branching engine later
 * (ask a question, collect input, hand off to a human); this controller only
 * ever creates a single trigger and a single top-level step per flow.
 */
class ChatbotFlowController extends Controller
{
    public function index(): View
    {
        return view('tenant.chatbot.index', [
            'flows' => ChatbotFlow::with('triggers', 'steps')
                ->orderBy('priority')
                ->orderBy('id')
                ->get(),
        ]);
    }

    public function create(): View
    {
        return view('tenant.chatbot.create', [
            'templates' => $this->replyableTemplates(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'keyword' => ['required', 'string', 'max:255'],
            'exact_match' => ['nullable', 'boolean'],
            'reply_type' => ['required', 'in:text,template'],
            'reply_body' => ['required_if:reply_type,text', 'nullable', 'string', 'max:4096'],
            'template_id' => ['required_if:reply_type,template', 'nullable', 'integer'],
        ]);

        if ($data['reply_type'] === 'template') {
            $template = $this->replyableTemplates()->firstWhere('id', (int) $data['template_id']);

            if (!$template) {
                return back()->withInput()->withErrors([
                    'template_id' => 'Choose an approved template with no placeholders — auto-replies can\'t fill variables yet.',
                ]);
            }
        }

        DB::transaction(function () use ($data, $request) {
            $flow = ChatbotFlow::create([
                'name' => $data['name'],
                'is_active' => true,
            ]);

            FlowTrigger::create([
                'flow_id' => $flow->id,
                'match_type' => FlowTrigger::TYPE_KEYWORD,
                'value' => $data['keyword'],
                'exact_match' => $request->boolean('exact_match'),
            ]);

            FlowStep::create([
                'flow_id' => $flow->id,
                'order' => 0,
                'action_type' => $data['reply_type'] === 'text' ? FlowStep::ACTION_SEND_TEXT : FlowStep::ACTION_SEND_TEMPLATE,
                'payload' => $data['reply_type'] === 'text'
                    ? ['body' => $data['reply_body']]
                    : ['template_id' => (int) $data['template_id']],
            ]);
        });

        return redirect()->route('tenant.chatbot.index')->with('success', 'Auto-reply created.');
    }

    public function toggle(ChatbotFlow $flow): RedirectResponse
    {
        $flow->forceFill(['is_active' => !$flow->is_active])->save();

        return back()->with('success', $flow->is_active ? 'Auto-reply activated.' : 'Auto-reply paused.');
    }

    public function destroy(ChatbotFlow $flow): RedirectResponse
    {
        $flow->delete();

        return back()->with('success', 'Auto-reply deleted.');
    }

    /**
     * Approved templates with no placeholders — the only ones an auto-reply
     * can send, since this MVP has no contact to resolve {{n}} values from
     * until the message that triggered it.
     */
    private function replyableTemplates()
    {
        return WhatsappTemplate::where('status', WhatsappTemplate::STATUS_APPROVED)
            ->orderBy('name')
            ->get()
            ->filter(fn (WhatsappTemplate $template) => $template->variableCount() === 0)
            ->values();
    }
}
