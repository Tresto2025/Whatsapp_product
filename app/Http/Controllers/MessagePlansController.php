<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\MessagePlans;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class MessagePlansController extends Controller
{
    public function index(){
        $plans = MessagePlans::whereNull('deleted_at')->get();
        return view('dashboard.message_plans.index', compact('plans'));
    }

    public function create(){
        return view('dashboard.message_plans.create');
    }

    public function store(Request $request){
        $request->validate([
            'plan_name' => 'required|string|max:255',
            'no_of_messages' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'description' => 'required',
        ]);
        MessagePlans::create($request->all());
        return redirect()->route('message_plans.index')->with('success', 'Plan created successfully!');
    }

    public function edit($id){
        $plan = MessagePlans::findOrFail($id);
        return view('dashboard.message_plans.edit', compact('plan'));
    }

    public function update(Request $request, $id){
        $plan = MessagePlans::findOrFail($id);
        $request->validate([
            'plan_name' => 'required|string|max:255',
            'no_of_messages' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'description' => 'required',
        ]);
        $plan->update($request->all());
        return redirect()->route('message_plans.index')->with('success', 'Plan updated successfully!');
    }
    
    public function updateStatus(Request $request){
        $doctor = MessagePlans::findOrFail($request->id);
        $doctor->status = $doctor->status == 1 ? 0 : 1;
        $doctor->save();
        return response()->json(['status' => 1, 'message' => 'MessagePlans status updated successfully.']);
    }

    public function destroy($id){
        MessagePlans::where('id', $id)->update(['deleted_at' => now()]);
        return response()->json(['success' => 'Plan deleted successfully.']);
    }

}
