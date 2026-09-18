<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Services;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class DoctorServicesController extends Controller
{
    public function index(){
        $services = Services::whereNull('deleted_at')->get();
        return view('dashboard.services.index', compact('services'));
    }

    public function store(Request $request){
        $request->validate([
            'service_name' => 'required|string|max:255',
        ]);
        Services::create($request->all());
        return redirect()->route('doctor.services.index')->with('success', 'Services added successfully.');
    }
    
    public function edit($id){
        $service = Services::findOrFail($id);
        return view('dashboard.services.edit', compact('service'));
    }
    
    public function update(Request $request, $id){
        $service = Services::findOrFail($id);
        $request->validate([
            'service_name' => 'required|string|max:255',
        ]);
        $service->update($request->all());
        return redirect()->route('doctor.services.index')->with('success', 'Services updated successfully!');
    }
    
    public function updateStatus(Request $request){
        $service = Services::findOrFail($request->id);
        $service->status = $service->status == 1 ? 0 : 1;
        $service->save();
        return response()->json(['status' => 1, 'message' => 'Service status updated successfully.']);
    }
    
    public function destroy($id){
        Services::where('id', $id)->update(['deleted_at' => now()]);
        return response()->json(['success' => 'Service deleted successfully.']);
    }
}
