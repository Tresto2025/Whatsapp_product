<?php

namespace App\Http\Controllers;

use Razorpay\Api\Api;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\WalletBalance;
use App\Models\WalletPayments;
use App\Models\SmsLogs;
use Auth;
use Carbon\Carbon;

class WalletController extends Controller
{

    public function myBalance()
    {
        $doctorId = Auth::id();
        $tz = 'Asia/Kolkata';
        $now = Carbon::now($tz);
        $startOfToday = $now->copy()->startOfDay();
        $endOfToday   = $now->copy()->endOfDay();

        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth   = $now->copy()->endOfMonth();

        $balance = WalletBalance::where('doctor_id', $doctorId)->first();
        $sentSms = SmsLogs::where('doctor_id', $doctorId)->whereNotNull('broadcast_id')->count();
        $todaySms = SmsLogs::where('doctor_id', $doctorId)->whereNotNull('broadcast_id')->whereBetween('created_at', [$startOfToday,$endOfToday])->count();
        $monthSms = SmsLogs::where('doctor_id', $doctorId)->whereNotNull('broadcast_id')->whereBetween('created_at', [$startOfMonth,$endOfMonth])->count();
        $lastRecharge = WalletPayments::where('doctor_id',$doctorId)->latest()->first();
        $rechargeHistory = WalletPayments::where('doctor_id', $doctorId)->latest()->paginate(10);

        return view('doctor-dashboard.sms-balance.my_balance', compact('balance','sentSms','todaySms','monthSms','lastRecharge','rechargeHistory'));
    }


    public function paymentSuccess(Request $request)
{
    \Log::info('Razorpay Callback Data:', $request->all());

    $paymentId = $request->razorpay_payment_id;
    $amount    = $request->amount;
    $doctorId  = Auth::id();

    \Log::info('Doctor ID: '.$doctorId);
    \Log::info('Payment ID: '.$paymentId);
    \Log::info('Amount: '.$amount);

    $api = new \Razorpay\Api\Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));

    try {

        $payment = $api->payment->fetch($paymentId);

        \Log::info('Razorpay Payment Status: '.$payment->status);

        if($payment->status == 'captured' || $payment->status == 'authorized')
        {

            DB::transaction(function () use ($paymentId,$amount,$doctorId) {

                \Log::info('Transaction started');

                $wallet = WalletBalance::where('doctor_id',$doctorId)
                ->lockForUpdate()
                ->first();

                \Log::info('Wallet Record:', ['wallet'=>$wallet]);

                $transactionExists = WalletPayments::where('transaction_id',$paymentId)->exists();

                \Log::info('Transaction Exists: '.($transactionExists ? 'YES' : 'NO'));

                if(!$transactionExists)
                {

                    WalletPayments::create([
                        'doctor_id'=>$doctorId,
                        'transaction_id'=>$paymentId,
                        'amount'=>$amount,
                        'payment_gateway'=>'razorpay',
                        'status'=>1
                    ]);

                    \Log::info('Wallet payment record inserted');

                    if($wallet)
                    {
                        $wallet->increment('total_recharged',$amount);
                        $wallet->increment('wallet_balance',$amount);

                        \Log::info('Wallet updated successfully');
                    }
                    else
                    {
                        WalletBalance::create([
                            'doctor_id'=>$doctorId,
                            'total_recharged'=>$amount,
                            'wallet_balance'=>$amount,
                            'total_spent'=>0,
                            'status'=>1
                        ]);

                        \Log::info('New wallet created');
                    }

                } else {

                    \Log::warning('Duplicate transaction detected');

                }

            });

        } else {

            \Log::warning('Payment not captured/authorized. Status: '.$payment->status);

        }

    } catch (\Exception $e) {

        \Log::error('Razorpay verify error: '.$e->getMessage());

        return redirect()->route('doctor.my.balance')
        ->with('error','Payment verification failed');

    }

    return redirect()->route('doctor.my.balance')
    ->with('success','Wallet recharge successful');
}
}