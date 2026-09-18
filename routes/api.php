<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\SmsBalanceController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\Api\WhatsAppController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::post('/register', [AuthController::class, 'register']);
// Route::post('/login', [AuthController::class, 'login']);

Route::post('/whatsapp/webhook', [WhatsAppController::class, 'webhook']);
Route::post('/send-whatsapp', [WhatsAppController::class, 'apisendMessage']);

Route::get('/webhook', [WebhookController::class, 'verify']);
Route::post('/webhook', [WebhookController::class, 'receive']);


Route::group(['prefix' => 'v1'], function () {
     Route::post('/doctor-register', [AuthController::class, 'doctorRegister']);
    Route::post('/send-otp', [AuthController::class, 'sendOtp']);
    Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);

     Route::get('/trainer-type', [AuthController::class, 'TrainerType']);

    // Add these new routes for location data
    Route::get('/countries', [AuthController::class, 'getCountries']);
    Route::get('/states/{countryId}', [AuthController::class, 'getStates']);
    Route::get('/cities/{stateId}', [AuthController::class, 'getCities']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/logout', [AuthController::class, 'logout']);
});
