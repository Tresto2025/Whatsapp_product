<?php

namespace App\Http\Controllers\Api;

use App\Models\City;
use App\Models\User;
use App\Models\State;
use App\Models\Cities;
use App\Models\Country;
use App\Models\UserInfo;
use Illuminate\Http\Request;
use App\Models\TrainerSkills;
use App\Models\TrainerProfession;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    // public function register(Request $request)
    // {

    //     $validator = Validator::make($request->all(), [
    //         'name'     => 'required|string|max:255',
    //         'email'    => 'required|string|email|max:255|unique:users',
    //         'password' => 'required|string|min:6',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json(['errors' => $validator->errors()], 422);
    //     }

    //     $user = User::create([
    //         'name'     => $request->name,
    //         'email'    => $request->email,
    //         'password' => Hash::make($request->password),
    //     ]);

    //     return response()->json([
    //         'message' => 'User registered successfully',
    //         'user'    => $user,
    //     ], 201);
    // }
    /**
     * STEP 1: Send OTP to Phone
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|min:10',
        ]);

        $phone = $request->phone;

        // Generate 4-digit OTP
        $otp = rand(1000, 9999);

        // Store OTP in Cache for 5 minutes
        // Key: otp_{phone_number}
        Cache::put("otp_{$phone}", $otp, now()->addMinutes(5));

        // TODO: Integrate SMS Gateway here (Twilio, Vonage, etc.)
        // For now, we return the OTP in the response for testing purposes
        return response()->json([
            'status' => true,
            'message' => 'OTP sent successfully',
            'otp' => $otp // Remove this in production
        ], 200);
    }

    /**
     * STEP 2: Verify OTP
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'otp' => 'required|integer',
        ]);

        $phone = $request->phone;
        $inputOtp = $request->otp;

        // Check OTP from Cache
        $cachedOtp = Cache::get("otp_{$phone}");

        if ($cachedOtp && $cachedOtp == $inputOtp) {
            // OTP is correct. Mark phone as "Verified" for the next 5 minutes
            // This allows the user to proceed to the final registration step
            Cache::put("verified_{$phone}", true, now()->addMinutes(5));

            return response()->json([
                'status' => true,
                'message' => 'Phone number verified successfully. You can now register.'
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => 'Invalid OTP'
        ], 400);
    }

    /**
     * STEP 3: Create Account (Updated)
     */
    public function doctorRegister(Request $request)
    {
        // --- SECURITY CHECK: Ensure Phone was verified via OTP in the last 5 mins ---
        $isVerified = Cache::get("verified_{$request->phone}");
        if (!$isVerified) {
            return response()->json([
                'status' => false,
                'message' => 'Phone number not verified. Please verify OTP first.'
            ], 403); // 403 Forbidden
        }

        $request->validate([
            'title' => 'nullable|string',
            'gender' => 'nullable|string',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed', // Ensure frontend sends 'password_confirmation'
            'phone' => 'required|string|unique:users,phone',
            // 'profile_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            // 'experience' => 'required|integer|min:0',
            // 'experience_certificate_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            // 'country' => 'required|integer',
            // 'state' => 'required|integer',
            // 'city' => 'required|integer',
            // 'skills' => 'nullable|string', // Receives comma-separated IDs (e.g., "1,5,12")
            // 'profession_type' => 'required|integer',
        ]);

        // --- Image Handling ---
        // $profileImageName = null;
        // if ($request->hasFile('profile_image')) {
        //     $file = $request->file('profile_image');
        //     $profileImageName = uniqid('profile_') . '.' . $file->getClientOriginalExtension();

        //     // Ensure directory exists
        //     $uploadPath = public_path('profile');
        //     if (!file_exists($uploadPath)) {
        //         mkdir($uploadPath, 0777, true);
        //     }

        //     $file->move($uploadPath, $profileImageName);
        // }

        // $certificateImageName = null;
        // if ($request->experience > 0 && $request->hasFile('experience_certificate_image')) {
        //     $file = $request->file('experience_certificate_image');
        //     $certificateImageName = uniqid('certificate_') . '.' . $file->getClientOriginalExtension();

        //     // Ensure directory exists
        //     $uploadPath = public_path('profile');
        //     if (!file_exists($uploadPath)) {
        //         mkdir($uploadPath, 0777, true);
        //     }

        //     $file->move($uploadPath, $certificateImageName);
        // }

        // --- User Creation ---
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            // 'profile_image' => $profileImageName,
            // 'experience' => $request->experience,
            // 'experience_certificate_image' => $certificateImageName,
            // Location Data (Integers)
            // 'country' => $request->country,
            // 'state' => $request->state,
            // 'city' => $request->city,
            // Professional Data
            //'skills' => $request->skills, // Stored as string: "1,5,12"
            'status' => 0, // Default status for trainers
            //'profession_type' => $request->profession_type,
            'role' => 2, // 2 = Trainer
        ]);

        // --- Token Generation ---
        $token = $user->createToken('auth_token')->plainTextToken;
        // ─── Auto-login the freshly created user ────────────────────────────────
        Auth::login($user);

        // Optional: regenerate session to prevent fixation attacks
        $request->session()->regenerate();
        return response()->json([
            'message' => 'Trainer registered successfully',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ], 201);
    }


    public function userRegister(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'phone' => 'required|string',
            'date_of_birth' => 'required|string',
            'gender' => 'required|string',
            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'height' => 'nullable|string',
            'weight' => 'nullable|string',
            'goals' => 'nullable|string',
            'target_weight' => 'nullable|string',
            'target_date' => 'nullable|string',
            'exercise_level' => 'nullable|string',
            'diet_preference' => 'nullable|string',
            'daily_water_intake' => 'nullable|string',
            'sleep_hours' => 'nullable|string',
            'supplements_taken' => 'nullable|string',
            'food_allergies' => 'nullable|string',
        ]);


        $profileImageName = null;
        if ($request->hasFile('profile_image')) {
            $file = $request->file('profile_image');
            $profileImageName = uniqid('profile_') . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('profile'), $profileImageName);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'profile_image' => $profileImageName,
            'role' => 3,
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
        ]);
        $user_info = UserInfo::create([
            'user_id' => $user->id,
            'height' => $request->height,
            'weight' => $request->weight,
            'goals' => $request->goals,
            'target_weight' => $request->target_weight,
            'target_date' => $request->target_date,
            'exercise_level' => $request->exercise_level,
            'diet_preference' => $request->diet_preference,
            'daily_water_intake' => $request->daily_water_intake,
            'sleep_hours' => $request->sleep_hours,
            'supplements_taken' => $request->supplements_taken,
            'food_allergies' => $request->food_allergies,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;
        $get_user = User::where('id', $user->id)->with('user_info')->first();
        return response()->json([
            'message' => 'User registered successfully',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $get_user
        ], 201);
    }


    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();
        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'message' => 'Login successful',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ]);
    }

    public function logout(Request $request)
    {

        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }

    public function AllCities(Request $request)
    {

        $cities = Cities::where('status', 1)->get();
        return response()->json(['status' => 1, 'all_cities' => $cities], 200);
    }

    public function TrainerType(Request $request)
    {

        $trainer_type = TrainerProfession::where('status', 1)->with('trainer_skills')->get();
        return response()->json(['status' => 1, 'trainer_type' => $trainer_type], 200);
    }

    public function getCountries()
    {
        $countries = Country::orderBy('name')->get();
        return response()->json([
            'status' => 1,
            'countries' => $countries
        ], 200);
    }

    public function getStates($countryId)
    {
        $states = State::where('country_id', $countryId)->orderBy('name')->get();
        return response()->json([
            'status' => 1,
            'states' => $states
        ], 200);
    }

    public function getCities($stateId)
    {
        $cities = City::where('state_id', $stateId)->orderBy('name')->get();
        return response()->json([
            'status' => 1,
            'cities' => $cities
        ], 200);
    }
}
