<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        // Validate input
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'nullable',
            'message' => 'required'
        ]);

        try {
            //  Send to n8n webhook
            Http::post('https://n8n.srv948607.hstgr.cloud/webhook/contact-query', [
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'message' => $request->message
            ]);

            return response()->json([
                'status' => 'success'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error'
            ], 500);
        }
    }
}