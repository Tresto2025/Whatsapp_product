<?php

namespace App\Http\Controllers;

use Auth;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

class DashboardController extends Controller
{
    public function index(){
        $user_type = Auth::user()->role;
        if($user_type == 1){
          return view('dashboard');
        }
        if($user_type == 2){
            $id = Auth::user()->id;
            $doctor = User::where('id',$id)->first();
         return view('doctor-dashboard', compact('id','doctor'));
        }
        
    }
    
    public function UserLogout(){
        Auth::logout();
        return redirect('/login');
    }
    
    public function AdminPassword(){
        $id = Auth::user()->id;
        $admin = User::where('id',$id)->first();
        return view('dashboard.profile.password', compact('id','admin'));
    }
    
    public function AdminUpdatePassword(Request $request){
        $validatedData = $request->validate([
            'password' => 'min:8|required_with:confirm_password|same:confirm_password',
            'confirm_password' => 'min:8',
            'current_password' => ['required', function ($attribute, $value, $fail) {
                if (!\Hash::check($value, Auth::user()->password)) {
                    return $fail(__('The current password is incorrect.'));
                }
            }]
        ]);
        $user_details = User::where('id', $request->id)->update([
            'password' => Hash::make($request->password),
            'show_password' => $request->password
        ]);
        return redirect()->route('admin-password')->with('success', 'Profile Updated successfully.');
    }
}