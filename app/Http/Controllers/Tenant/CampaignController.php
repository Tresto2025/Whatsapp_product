<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Jobs\SendCampaign;
use App\Models\Campaign;
use App\Models\CampaignRecipient;
use App\Models\Contact;
use App\Models\Tag;
use App\Models\WhatsappTemplate;
use App\Tenancy\TenantManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Broadcasts: pick an approved template and a segment, and send.
 *
 * Recipients are materialised into campaign_recipients at creation so the send
 * (which runs on a queue) has a fixed list and per-person status to write back.
 */
class CampaignController extends Controller
{
    public function __construct(private TenantManager $tenants)
    {
    }

    public function index(): View
    {
        return view('tenant.campaigns.index', [
            'campaigns' => Campaign::with('template')
                ->withCount('recipients')
                ->orderByDesc('id')
                ->get(),
        ]);
    }

    public function create(): View
    {
        return view('tenant.campaigns.create', [
            'templates' => WhatsappTemplate::where('status', WhatsappTemplate::STATUS_APPROVED)
                ->orderBy('name')
                ->get(),
            'tags' => Tag::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'template_id' => ['required', 'integer'],
            'segment_type' => ['required', 'in:all,tags'],
            'tags' => ['array'],
            'tags.*' => ['integer'],
            'variable_map' => ['array'],
        ]);

        $template = WhatsappTemplate::where('id', $data['template_id'])
            ->where('status', WhatsappTemplate::STATUS_APPROVED)
            ->first();

        if (!$template) {
            return back()->withInput()->withErrors(['template_id' => 'Choose an approved template.']);
        }

        $contactIds = $this->resolveContactIds($data['segment_type'], $data['tags'] ?? []);

        if ($contactIds->isEmpty()) {
            return back()->withInput()->withErrors(['segment_type' => 'That segment has no contacts.']);
        }

        $campaign = new Campaign();
        $campaign->fill([
            'name' => $data['name'],
            'template_id' => $template->id,
            'whatsapp_account_id' => $template->whatsapp_account_id,
            'segment' => ['type' => $data['segment_type'], 'tags' => $data['tags'] ?? []],
            'variable_map' => $this->cleanVariableMap($data['variable_map'] ?? []),
            'status' => Campaign::STATUS_DRAFT,
            'created_by' => $request->user()->id,
        ]);
        $campaign->save();

        $this->materialiseRecipients($campaign, $contactIds);

        SendCampaign::dispatch($campaign->id);

        return redirect()
            ->route('tenant.campaigns.show', $campaign)
            ->with('success', 'Campaign queued to '.$contactIds->count().' contact(s).');
    }

    public function show(Campaign $campaign): View
    {
        $campaign->load('template', 'account');

        return view('tenant.campaigns.show', [
            'campaign' => $campaign,
            'recipients' => $campaign->recipients()
                ->with('contact', 'message')
                ->orderBy('id')
                ->paginate(50),
        ]);
    }

    /** @return \Illuminate\Support\Collection<int,int> */
    private function resolveContactIds(string $segmentType, array $tagIds)
    {
        $query = Contact::query();

        if ($segmentType === 'tags') {
            $query->whereHas('tags', fn ($q) => $q->whereIn('tags.id', $tagIds));
        }

        return $query->pluck('id');
    }

    private function materialiseRecipients(Campaign $campaign, $contactIds): void
    {
        $tenantId = $this->tenants->id();
        $now = now();

        $rows = $contactIds->map(fn ($contactId) => [
            'tenant_id' => $tenantId,
            'campaign_id' => $campaign->id,
            'contact_id' => $contactId,
            'status' => CampaignRecipient::STATUS_PENDING,
            'created_at' => $now,
            'updated_at' => $now,
        ])->all();

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('campaign_recipients')->insert($chunk);
        }
    }

    /**
     * The form posts one `mode` per placeholder — a contact field name or
     * "static". Normalise it to the {source, field|value} shape the dispatcher
     * resolves, dropping anything blank or unrecognised.
     */
    private function cleanVariableMap(array $raw): array
    {
        $fields = ['name', 'profile_name', 'wa_id', 'email'];
        $clean = [];

        foreach ($raw as $position => $map) {
            $mode = $map['mode'] ?? null;

            if (in_array($mode, $fields, true)) {
                $clean[$position] = ['source' => 'field', 'field' => $mode];
            } elseif ($mode === 'static' && ($map['value'] ?? '') !== '') {
                $clean[$position] = ['source' => 'static', 'value' => $map['value']];
            }
        }

        return $clean;
    }
}
