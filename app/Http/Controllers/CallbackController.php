<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CallbackController extends Controller
{
    public function handle(Request $request)
    {
        try {

            $phone = $request->input('phone');

            if (!$phone) {
                return response()->json([
                    'success' => false,
                    'error' => 'Phone required'
                ]);
            }

            // 🔥 Your n8n webhook
            $webhookUrl = "https://n8n.srv948607.hstgr.cloud/webhook/demo-callback";

            // ✅ Send request WITHOUT breaking flow
            try {
                Http::withOptions([
                    'verify' => false
                ])->timeout(5)->post($webhookUrl, [
                    'phone' => $phone
                ]);
            } catch (\Exception $e) {
                // ❌ Don't break user flow
                Log::error("n8n failed", [
                    'message' => $e->getMessage()
                ]);
            }

            // ✅ ALWAYS return success to frontend
            return response()->json([
                'success' => true,
                'message' => '📞 You will receive a call shortly'
            ]);

        } catch (\Exception $e) {

            Log::error("Controller error", [
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Something went wrong'
            ]);
        }
    }
}