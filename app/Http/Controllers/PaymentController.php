<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function showPaymentPage()
    {
        return view('test-payment');
    }

    public function paymentSuccess(Request $request)
    {
        Log::info('Payment Success:', $request->all());
        echo "test";die;
        return back()->with('success', 'Payment successful!');
    }
}
