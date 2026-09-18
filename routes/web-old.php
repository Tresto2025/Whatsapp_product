<?php

use App\Http\Controllers\DoctorController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RedirectController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SmsBalanceController;
use App\Http\Controllers\DoctorPlansController;
use App\Http\Controllers\MessagePlansController;
use App\Http\Controllers\DoctorServicesController;
use App\Http\Controllers\DoctorAppointmentController;
use App\Http\Controllers\BroadcastMessagesController;
use App\Http\Controllers\DoctorBroadcastMessagesController;
use App\Http\Controllers\MessagePriceController;
use App\Http\Controllers\WalletController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RazorpayController;
use App\Http\Controllers\BlogPostController;
use App\Http\Controllers\BlogCategoryController;
use App\Http\Controllers\BlogFrontendController;


Route::get('/clear-cache', function () {
    Artisan::call('optimize:clear');
    return '✅ All caches cleared successfully!';
});

Route::get('/linkstorage', function () {
    Artisan::call('storage:link');
});
Route::get('/razorpay', [RazorpayController::class, 'index']);
Route::post('/razorpay/payment', [RazorpayController::class, 'payment'])->name('razorpay.payment');
Route::post('/razorpay/success', [RazorpayController::class, 'success'])->name('razorpay.success');

    Route::get('/test-payment', [PaymentController::class, 'showPaymentPage'])->name('test.payment');
Route::post('/payment-success', [PaymentController::class, 'paymentSuccess'])->name('payment.success');

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// })->name('home');
Route::get('/' , function(){
    return view('frontend.home');
})->name('home');
Route::get('/about', function () {
    return view('frontend.about');
})->name('about');
Route::get('/terms', function () {
    return view('terms');
})->name('terms');
Route::get('/contact', function () {
    return view('frontend.contact');
})->name('contact');
Route::get('/privacy', function () {
    return view('frontend.privacy');
})->name('privacy');
Route::get('/refunds', function () {
    return view('frontend.refunds');
})->name('refunds');
Route::get('/shipping', function () {
    return view('frontend.shipping');
})->name('shipping');
Route::get('/whatsapp-redirect/{doctor}', [RedirectController::class, 'redirectToWhatsapp'])
    ->name('whatsapp.redirect');
    

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/user-logout', [DashboardController::class, 'UserLogout'])->name('user-logout');
    
    Route::get('/all-doctors', [DoctorController::class, 'index'])->name('doctor.index');
    Route::get('/doctor/{id}/edit', [DoctorController::class, 'edit'])->name('doctor.edit');
    Route::post('/doctor-update', [DoctorController::class, 'update'])->name('doctor.update');
    Route::get('/doctor/delete/{id}', [DoctorController::class, 'destroy'])->name('doctor.destroy');
    Route::get('/doctor/details/{id}', [DoctorController::class, 'Details'])->name('doctor.details');
    Route::get('/filter-doctors', [DoctorController::class, 'FilterDoctors'])->name('filter-doctors');
    Route::post('/update-doctor-status', [DoctorController::class, 'UpdateDoctorStatus'])->name('update-doctor-status');
    Route::get('/doctor/{id}/download-qr', [DoctorController::class, 'downloadQr'])->name('doctor.download.qr');
    Route::get('/doctor/{id}/qr-pdf', [DoctorController::class, 'downloadQrPdf'])->name('doctor.qr-pdf');
    Route::get('/doctor/create', [DoctorController::class, 'create'])->name('doctor.create');
    Route::post('/doctor/store', [DoctorController::class, 'store'])->name('doctor.store');
    Route::get('/services/search', [DoctorController::class, 'search'])->name('services.search');
    
    Route::get('/sms-balance', [SmsBalanceController::class, 'index'])->name('sms.index');
    Route::post('/sms-balance/store', [SmsBalanceController::class, 'store'])->name('sms.store');
    Route::get('/sms-balance/edit/{id}', [SmsBalanceController::class, 'edit'])->name('sms.edit');
    Route::post('/sms-balance/{id}/update', [SmsBalanceController::class, 'update'])->name('sms-balance.update');
    Route::get('/sms-balance/delete/{id}', [SmsBalanceController::class, 'destroy'])->name('sms.destroy');
    Route::post('/sms-balance/status', [SmsBalanceController::class, 'updateStatus'])->name('sms.status');
    Route::get('/doctor/search', [SmsBalanceController::class, 'searchDoctor'])->name('doctor.search');
    
    Route::get('/message-plans', [MessagePlansController::class, 'index'])->name('message_plans.index');
    Route::post('/message-plans/store', [MessagePlansController::class, 'store'])->name('message_plans.store');
    Route::get('/message-plans/edit/{id}', [MessagePlansController::class, 'edit'])->name('message_plans.edit');
    Route::post('/message-plans/{id}/update', [MessagePlansController::class, 'update'])->name('message_plans.update');
    Route::get('/message-plans/delete/{id}', [MessagePlansController::class, 'destroy'])->name('message_plans.destroy');
    Route::post('/message-plans/status', [MessagePlansController::class, 'updateStatus'])->name('message_plans.status');
    
    Route::get('doctor/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::post('doctor/profile/update', [ProfileController::class, 'update'])->name('profile-update');
    Route::post('doctor/profile/update/password', [ProfileController::class, 'UpdatePassword'])->name('profile-update-password');
    
    Route::get('doctor/my-profile', [ProfileController::class, 'DoctorProfile'])->name('doctor-my-profile');
    Route::post('doctor/my-profile/update', [ProfileController::class, 'UpdateDoctorProfile'])->name('doctor-my-profile-update');
    
    Route::get('doctor/my-password', [ProfileController::class, 'DoctorPassword'])->name('doctor-my-password');
    Route::post('doctor/my-password/update', [ProfileController::class, 'UpdateDoctorPassword'])->name('doctor-my-password-update');
    
    Route::get('/doctor/appointments', [DoctorAppointmentController::class, 'index'])->name('doctor.appointments');
    Route::get('/doctor/patient-details/{id}', [DoctorAppointmentController::class, 'Patientdetails'])->name('doctor.patient-details');
    Route::get('/filter-appointments', [DoctorAppointmentController::class, 'filterAppointments'])->name('filter-appointments');
    Route::post('/doctor-appointment/cancel/{id}', [DoctorAppointmentController::class, 'cancelAppointment'])->name('appointment.cancel');
    Route::post('/appointments/reschedule', [DoctorAppointmentController::class, 'rescheduleAppointment'])->name('appointments.reschedule');
    Route::post('/appointments/get-slots', [DoctorAppointmentController::class, 'newgetSlots'])->name('appointment.get-slots');
    Route::get('/appointments/missing/{id}', [DoctorAppointmentController::class, 'missAppointment'])->name('appointment.missing');
    Route::get('/appointments/checkin/{id}', [DoctorAppointmentController::class, 'checkinAppointment'])->name('appointment.checkin');
    
    Route::get('/doctor/appointment/create', [DoctorAppointmentController::class, 'create'])->name('doctor.appointment.create');
    Route::post('/doctor/appointment/store', [DoctorAppointmentController::class, 'store'])->name('doctor.appointment.store');
    Route::post('/doctor/appointment/get-slots', [DoctorAppointmentController::class, 'getSlots'])->name('doctor.get.slots');
    
    Route::post('/doctor/toggle-booking', [DoctorController::class, 'toggleBooking'])->name('doctor.toggleBooking');
    // Show import page
    Route::get('/doctors/import', [DoctorController::class, 'showImportForm'])->name('doctors.import.form');
    Route::post('/doctors/import', [DoctorController::class, 'import'])->name('doctors.import');

    Route::get('/admin/password', [DashboardController::class, 'AdminPassword'])->name('admin-password');
    Route::post('update/password', [DashboardController::class, 'AdminUpdatePassword'])->name('admin-update-password');
    
    Route::get('/broadcast-messages', [BroadcastMessagesController::class, 'index'])->name('broadcast_messages.index');
    Route::post('/broadcast-messages', [BroadcastMessagesController::class, 'store'])->name('broadcast_messages.store');
    Route::get('/broadcast-messages/{id}/resend', [BroadcastMessagesController::class, 'resend'])->name('broadcast_messages.resend');
    Route::delete('/broadcast-messages/{id}', [BroadcastMessagesController::class, 'destroy'])->name('broadcast_messages.destroy');
    
    Route::get('/doctor/broadcast-messages', [DoctorBroadcastMessagesController::class, 'index'])->name('doctor.broadcast_messages.index');
    Route::post('/doctor/broadcast-messages', [DoctorBroadcastMessagesController::class, 'store'])->name('doctor.broadcast_messages.store');
    Route::get('/doctor/broadcast-messages/{id}/resend', [DoctorBroadcastMessagesController::class, 'resend'])->name('doctor.broadcast_messages.resend');
    Route::delete('/doctor/broadcast-messages/{id}', [DoctorBroadcastMessagesController::class, 'destroy'])->name('doctor.broadcast_messages.destroy');
    
    Route::get('/doctor/service', [DoctorServicesController::class, 'index'])->name('doctor.services.index');
    Route::post('/doctor/service', [DoctorServicesController::class, 'store'])->name('doctor.services.store');
    
    Route::get('/doctor/service/edit/{id}', [DoctorServicesController::class, 'edit'])->name('doctor.services.edit');
    Route::post('/doctor/service/{id}/update', [DoctorServicesController::class, 'update'])->name('doctor.services.update');
    Route::get('/doctor/service/delete/{id}', [DoctorServicesController::class, 'destroy'])->name('doctor.services.destroy');
    Route::post('/doctor/service/status', [DoctorServicesController::class, 'updateStatus'])->name('doctor.services.status');
    
    Route::prefix('blog')->name('blog-')->group(function () {
        // Posts
        Route::get('/posts', [BlogPostController::class, 'index'])->name('post.index');
        Route::get('/posts/create', [BlogPostController::class, 'create'])->name('post.create');
        Route::post('/posts', [BlogPostController::class, 'store'])->name('post.store');
        Route::get('/posts/{id}/edit', [BlogPostController::class, 'edit'])->name('post.edit');
        Route::post('/posts/{id}', [BlogPostController::class, 'update'])->name('post.update');
        Route::post('/posts/delete', [BlogPostController::class, 'destroy'])->name('RecycleBinPost');
        Route::get('/posts/draft', [BlogPostController::class, 'draft'])->name('post.draft');
        Route::get('/posts/recycle-bin', [BlogPostController::class, 'recycleBin'])->name('post.recycle-bin');
        Route::post('/posts/{id}/restore', [BlogPostController::class, 'restore'])->name('post.restore');
        Route::delete('/posts/{id}/force-delete', [BlogPostController::class, 'forceDelete'])->name('post.force-delete');
        
        // Categories
        Route::get('/categories', [BlogCategoryController::class, 'index'])->name('category.index');
        Route::post('/categories', [BlogCategoryController::class, 'store'])->name('category.store');
        Route::get('/categories/{id}/edit', [BlogCategoryController::class, 'edit'])->name('category.edit');
        Route::post('/categories/{id}', [BlogCategoryController::class, 'update'])->name('category.update');
        Route::post('/categories/delete', [BlogCategoryController::class, 'destroy'])->name('deleteCat');
    });
    

});
    
    Route::get('/doctor/sms-balance', [DoctorPlansController::class, 'index'])->name('doctor.sms-balance');
    Route::post('/doctor/payment-success-data', [DoctorPlansController::class, 'paymentSuccessData'])->name('doctor.payment-success-data');
    /*Route::get('/doctor/my-balance', [DoctorPlansController::class, 'MyBalance'])->name('doctor.my.balance');*/
    
    Route::post('/doctor/wallet-payment-success', [WalletController::class, 'paymentSuccess'])->name('doctor.wallet.payment.success');
    Route::get('/doctor/my-balance', [WalletController::class, 'myBalance'])->name('doctor.my.balance');
    
    Route::get('admin/message-price',[MessagePriceController::class,'index'])->name('admin.message.price');
    Route::post('admin/message-price-update',[MessagePriceController::class,'update'])->name('admin.message.price.update');
    
    Route::get('/blog', [BlogFrontendController::class, 'index'])->name('blog.index');
    Route::get('/blog/{slug}', [BlogFrontendController::class, 'show'])->name('blog.detail');



require __DIR__.'/auth.php';
