<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\SmsBalance;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class SmsBalanceController extends Controller
{
    public function index()
    {
        $smsList = SmsBalance::with('doctor')->orderBy('id', 'desc')->get();
        return view('dashboard.sms_balance.index', compact('smsList'));
    }
    

    public function store(Request $request){
        $request->validate([
            'doctor_id' => 'required|exists:users,id',
            'total_sms' => 'required|integer|min:1',
        ]);
        $existing = SmsBalance::where('doctor_id', $request->doctor_id)->first();
        if ($existing) {
            $existing->total_sms += $request->total_sms;
            $existing->pending_sms += $request->total_sms;
            $existing->save();
        } else {
            SmsBalance::create([
                'doctor_id'   => $request->doctor_id,
                'total_sms'   => $request->total_sms,
                'pending_sms' => $request->total_sms,
                'spent_sms'   => 0,
                'status'      => 1
            ]);
        }
        return redirect()->route('sms.index')->with('success', 'SMS balance added successfully.');
    }

    public function edit($id)
    {
        $smsBalance = SmsBalance::with('doctor')->findOrFail($id);
        return view('dashboard.sms_balance.edit', compact('smsBalance'));
    }

    public function update(Request $request, $id){
        $request->validate([
            'total_sms' => 'required|integer|min:0',
            'pending_sms' => 'required|integer|min:0',
            'spent_sms' => 'required|integer|min:0',
        ]);
    
        $smsBalance = SmsBalance::findOrFail($id);
        $smsBalance->update([
            'total_sms' => $request->total_sms,
            'pending_sms' => $request->pending_sms,
            'spent_sms' => $request->spent_sms,
        ]);

        return redirect()->route('sms.index')->with('success', 'SMS balance updated successfully.');
    }

    public function destroy($id)
    {
        $sms = SmsBalance::findOrFail($id);
        $sms->delete();
        return redirect()->route('sms.index')->with('success', 'SMS record deleted.');
    }

    public function updateStatus(Request $request)
    {
        $sms = SmsBalance::findOrFail($request->id);
        $sms->status = $sms->status == 1 ? 0 : 1;
        $sms->save();
        return response()->json(['status' => 1, 'message' => 'status updated successfully.']);
    }

    public function searchDoctor(Request $request)
    {
        $term = $request->q;
        $doctors = User::where('role', 2)
            ->where(function ($query) use ($term) {
                $query->where('name', 'like', "%$term%")
                      ->orWhere('email', 'like', "%$term%");
            })
            ->select('id', 'name', 'email')
            ->limit(10)
            ->get();
        //echo "<pre>";
        //print_r($doctors->toArray());die;
        return response()->json($doctors);
    }
}
