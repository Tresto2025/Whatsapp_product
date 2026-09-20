<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\WhatsappAccount;
use App\Services\WhatsApp\WhatsAppClient;
use App\Tenancy\TenantManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Manual WhatsApp onboarding (MVP).
 *
 * A tenant admin pastes the credentials from their own Meta app. Before we
 * store anything as "connected" we call GET /{phone_number_id} with the token,
 * which proves the token actually controls that number — otherwise a typo
 * would sit there looking connected until the first message silently failed.
 *
 * Embedded Signup (Phase 5) will populate the same row; everything downstream
 * of this controller is unchanged by which path created the account.
 */
class WhatsAppConnectionController extends Controller
{
    public function __construct(private TenantManager $tenants)
    {
    }

    public function index(): View
    {
        $accounts = WhatsappAccount::orderByDesc('is_default')->orderBy('id')->get();

        return view('tenant.whatsapp.index', [
            'accounts' => $accounts,
            'webhookUrl' => url('/api/webhook'),
        ]);
    }

    public function create(): View
    {
        return view('tenant.whatsapp.create', [
            'webhookUrl' => url('/api/webhook'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'label' => ['nullable', 'string', 'max:255'],
            'phone_number_id' => ['required', 'string', 'max:255', 'unique:whatsapp_accounts,phone_number_id'],
            'waba_id' => ['nullable', 'string', 'max:255'],
            'meta_business_id' => ['nullable', 'string', 'max:255'],
            'access_token' => ['required', 'string'],
            'app_secret' => ['nullable', 'string', 'max:255'],
        ]);

        $tenantId = $this->tenants->id();

        if (!$tenantId) {
            return back()->withInput()->withErrors([
                'phone_number_id' => 'No workspace is active for this account.',
            ]);
        }

        // Prove the credentials work before storing them as connected.
        $probe = new WhatsAppClient(
            $data['phone_number_id'],
            $data['access_token'],
            config('services.whatsapp.api_version', 'v22.0'),
        );

        $result = $probe->verifyCredentials();

        if (!$result['ok']) {
            return back()->withInput()->withErrors([
                'access_token' => 'Meta rejected these credentials: '.($result['error'] ?? 'unknown error'),
            ]);
        }

        $account = new WhatsappAccount();
        $account->fill($data);
        $account->tenant_id = $tenantId;
        $account->provider = 'manual';
        $account->verify_token = WhatsappAccount::generateVerifyToken();
        $account->connection_status = WhatsappAccount::STATUS_CONNECTED;
        $account->display_phone_number = $result['json']['display_phone_number'] ?? null;
        $account->last_verified_at = now();
        $account->is_default = !WhatsappAccount::where('tenant_id', $tenantId)->exists();
        $account->save();

        return redirect()
            ->route('tenant.whatsapp.index')
            ->with('success', 'WhatsApp number connected. Paste the webhook URL and verify token into your Meta app to finish.');
    }

    /**
     * Re-check stored credentials against Meta, e.g. after a token rotation.
     */
    public function recheck(WhatsappAccount $account): RedirectResponse
    {
        $result = WhatsAppClient::forAccount($account)->verifyCredentials();

        if ($result['ok']) {
            $account->markConnected($result['json']['display_phone_number'] ?? null);

            return back()->with('success', 'Credentials are still valid.');
        }

        $account->markFailed((string) $result['error']);

        return back()->withErrors(['account' => 'Meta rejected these credentials: '.$result['error']]);
    }

    public function makeDefault(WhatsappAccount $account): RedirectResponse
    {
        WhatsappAccount::where('tenant_id', $account->tenant_id)->update(['is_default' => false]);
        $account->forceFill(['is_default' => true])->save();

        return back()->with('success', 'Default sending number updated.');
    }

    public function destroy(WhatsappAccount $account): RedirectResponse
    {
        $account->delete();

        return back()->with('success', 'WhatsApp number disconnected.');
    }
}
