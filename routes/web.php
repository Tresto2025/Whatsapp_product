<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Tenant\ConversationController;
use App\Http\Controllers\Tenant\TemplateController;
use App\Http\Controllers\Tenant\TenantSignupController;
use App\Http\Controllers\Tenant\WhatsAppConnectionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web routes
|--------------------------------------------------------------------------
| The platform is a signed-in product: the root URL goes to sign-in, and a
| visitor with a session is forwarded to their dashboard by the `guest`
| middleware on /login.
*/

Route::redirect('/', '/login')->name('home');

/*
 * Self-serve signup: creates the workspace and its first admin together.
 */
Route::middleware('guest')->group(function () {
    Route::get('signup', [TenantSignupController::class, 'create'])->name('tenant.signup');
    Route::post('signup', [TenantSignupController::class, 'store'])->name('tenant.signup.store');
});

Route::middleware(['auth', 'tenant'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    /*
     * The inbox is the day-to-day workspace, so agents and admins both reach it.
     * Route-model binding on Conversation inherits the tenant scope.
     */
    Route::prefix('inbox')->name('conversations.')->group(function () {
        Route::get('/', [ConversationController::class, 'index'])->name('index');
        Route::get('{conversation}', [ConversationController::class, 'show'])->name('show');
        Route::post('{conversation}/reply', [ConversationController::class, 'reply'])->name('reply');
    });

    /*
     * Connecting the tenant's own Meta number is an owner-level action, so
     * agents are excluded. Route-model binding on WhatsappAccount inherits the
     * tenant global scope, so one tenant cannot address another's row.
     */
    Route::middleware('tenant_admin')->prefix('tenant/whatsapp')->name('tenant.whatsapp.')->group(function () {
        Route::get('/', [WhatsAppConnectionController::class, 'index'])->name('index');
        Route::get('connect', [WhatsAppConnectionController::class, 'create'])->name('create');
        Route::post('connect', [WhatsAppConnectionController::class, 'store'])->name('store');
        Route::post('{account}/recheck', [WhatsAppConnectionController::class, 'recheck'])->name('recheck');
        Route::post('{account}/default', [WhatsAppConnectionController::class, 'makeDefault'])->name('default');
        Route::delete('{account}', [WhatsAppConnectionController::class, 'destroy'])->name('destroy');
    });

    /*
     * Templates are workspace configuration (they mirror Meta's approved list),
     * so they sit behind the admin gate alongside the connection.
     */
    Route::middleware('tenant_admin')->prefix('templates')->name('tenant.templates.')->group(function () {
        Route::get('/', [TemplateController::class, 'index'])->name('index');
        Route::post('sync', [TemplateController::class, 'sync'])->name('sync');
    });
});

require __DIR__.'/auth.php';
