<?php

use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API routes
|--------------------------------------------------------------------------
| One webhook URL shared by every tenant. Which tenant a payload belongs to is
| decided from metadata.phone_number_id inside it, not from the URL.
*/

Route::get('webhook', [WebhookController::class, 'verify']);
Route::post('webhook', [WebhookController::class, 'receive']);
