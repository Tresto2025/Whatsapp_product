<?php

namespace App\Http\Controllers;

use App\Models\MessagePlans;
use App\Models\SmsBalance;
use App\Models\SmsPayments;
use App\Models\SmsLogs;
use App\Models\WalletBalance;
use App\Models\Appointments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class DoctorPlansController extends Controller
{
    public function index()
    {
        $all_plans = MessagePlans::where('status', 1)->whereNull('deleted_at')->orderBy('price', 'ASC')->get();
        return view('doctor-dashboard.sms-balance.index', compact('all_plans'));
    }

    public function paymentSuccessData(Request $request){
        
        \Log::info('Razorpay Callback Data:', $request->all());
        $paymentId = $request->input('razorpay_payment_id');
        $planId    = $request->input('plan_id');
        $doctorId  = Auth::id();
        
        DB::transaction(function () use ($planId, $paymentId, $doctorId) {
            
            $balance = SmsBalance::where('doctor_id', $doctorId)->lockForUpdate()->first();
            $plan = MessagePlans::findOrFail($planId);
            
            if (!SmsPayments::where('transaction_id', $paymentId)->exists()) {
                SmsPayments::create([
                    'doctor_id'      => $doctorId,
                    'plan_id'        => $plan->id,
                    'transaction_id' => $paymentId,
                    'amount'         => $plan->price,
                ]);

                if ($balance) {
                    $balance->increment('total_sms', $plan->no_of_messages);
                    $balance->increment('pending_sms', $plan->no_of_messages);
                    $balance->status = 1;
                    $balance->save();
                } else {
                    SmsBalance::create([
                        'doctor_id'   => $doctorId,
                        'total_sms'   => $plan->no_of_messages,
                        'pending_sms' => $plan->no_of_messages,
                        'spent_sms'   => 0,
                        'status'      => 1,
                    ]);
                }
            }
        });
        return redirect()->route('doctor.my.balance')->with('success', 'Payment successful, SMS credited.');
    }


    
    public function MyBalance()
    {
        $doctorId = Auth::id();
        $tz = 'Asia/Kolkata';
        $now = Carbon::now($tz);
        $startOfToday = $now->copy()->startOfDay();
        $endOfToday   = $now->copy()->endOfDay();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth   = $now->copy()->endOfMonth();
        $balance = SmsBalance::where('doctor_id', $doctorId)->first();
        $todaySms = SmsLogs::where('doctor_id', $doctorId)->whereBetween('created_at', [$startOfToday, $endOfToday])->count();
        $monthSms = SmsLogs::where('doctor_id', $doctorId)->whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();
        $lastRecharge = SmsPayments::where('doctor_id', $doctorId)->latest()->first();
        return view('doctor-dashboard.sms-balance.my_balance', compact('balance', 'todaySms', 'monthSms', 'lastRecharge'));
    }

}