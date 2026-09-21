<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessInboundWhatsAppMessage;
use App\Models\Scopes\TenantScope;
use App\Models\WhatsappAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * The single webhook every tenant points their Meta app at.
 *
 * Nothing here knows about the tenant's business. It works out which account a
 * payload belongs to, proves the payload came from Meta, and hands off.
 */
class WebhookController extends Controller
{
    /**
     * Meta's handshake. Tenants share this URL, so the token they send is what
     * identifies which account is being verified.
     */
    public function verify(Request $request)
    {
        $provided = (string) $request->query('hub_verify_token', $request->input('hub_verify_token'));

        if ($request->query('hub_mode', $request->input('hub_mode')) !== 'subscribe' || $provided === '') {
            return response('Verification failed', 403);
        }

        $account = WhatsappAccount::withoutGlobalScope(TenantScope::class)
            ->where('verify_token', $provided)
            ->first();

        if (!$account) {
            Log::warning('WhatsApp webhook verification failed: unknown verify token');

            return response('Verification failed', 403);
        }

        $account->forceFill(['webhook_status' => 'verified'])->save();

        return response($request->query('hub_challenge', $request->input('hub_challenge')), 200);
    }

    /**
     * Inbound messages and delivery receipts.
     *
     * Routing key is metadata.phone_number_id — it is the only thing in the
     * payload that says which tenant this belongs to.
     */
    public function receive(Request $request)
    {
        $data = $request->all();

        $phoneNumberId = data_get($data, 'entry.0.changes.0.value.metadata.phone_number_id');
        $account = WhatsappAccount::findByPhoneNumberId($phoneNumberId);

        if (!$account) {
            Log::warning('WhatsApp webhook rejected: unknown phone_number_id', [
                'phone_number_id' => $phoneNumberId,
            ]);

            return response('Unknown number', 404);
        }

        if (!$this->signatureIsValid($request, $account->app_secret)) {
            Log::warning('WhatsApp webhook rejected: invalid signature', [
                'whatsapp_account_id' => $account->id,
            ]);

            return response('Invalid signature', 403);
        }

        ProcessInboundWhatsAppMessage::dispatch($account->id, $data);

        // Meta retries anything that is not a prompt 200, so acknowledge now
        // and do the work on the queue.
        return response()->json(['status' => 'queued']);
    }

    /**
     * HMAC of the raw body with the account's app secret. Returns true when the
     * account has no secret configured, so a tenant mid-setup is not locked
     * out — the tenant UI pushes them to add one.
     */
    private function signatureIsValid(Request $request, ?string $appSecret): bool
    {
        if (empty($appSecret)) {
            return true;
        }

        $signature = $request->header('X-Hub-Signature-256');

        if (empty($signature)) {
            return false;
        }

        $expected = 'sha256='.hash_hmac('sha256', $request->getContent(), $appSecret);

        return hash_equals($expected, $signature);
    }
}
