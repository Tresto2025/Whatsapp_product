<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\WhatsappAccount;
use App\Models\WhatsappTemplate;
use App\Services\WhatsApp\TemplateSyncService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use RuntimeException;

/**
 * The tenant's mirrored Meta templates.
 *
 * Templates are created and approved on Meta; here they are only listed and
 * re-synced, so campaigns and flows have a known-approved list to build on.
 */
class TemplateController extends Controller
{
    public function index(): View
    {
        return view('tenant.templates.index', [
            'templates' => WhatsappTemplate::with('account')
                ->orderBy('name')
                ->orderBy('language')
                ->get(),
            'canSync' => WhatsappAccount::where('connection_status', WhatsappAccount::STATUS_CONNECTED)->exists(),
        ]);
    }

    public function sync(TemplateSyncService $sync): RedirectResponse
    {
        $accounts = WhatsappAccount::where('connection_status', WhatsappAccount::STATUS_CONNECTED)->get();

        if ($accounts->isEmpty()) {
            return back()->withErrors(['sync' => 'Connect a WhatsApp number before syncing templates.']);
        }

        $total = 0;

        foreach ($accounts as $account) {
            try {
                $total += $sync->sync($account)['synced'];
            } catch (RuntimeException $e) {
                return back()->withErrors(['sync' => $e->getMessage()]);
            }
        }

        return back()->with('success', "Synced {$total} template(s) from Meta.");
    }
}
