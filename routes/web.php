<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EventController as AdminEventController;
use App\Http\Controllers\Admin\PartnerController;
use App\Http\Controllers\EventController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TicketController;

Route::get('/login', function() {
    return redirect()->route('admin.login');
})->name('login');

Route::post('/midtrans/callback', [\App\Http\Controllers\MidtransWebhookController::class, 'handle']);

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.post');
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    Route::middleware(['auth', 'admin'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        // Route::get('/events', [DashboardController::class, 'indexAdmin'])->name('events.index');
        Route::resource('/events', AdminEventController::class);
        Route::get('/transactions', [DashboardController::class, 'transactionsAdmin'])->name('transactions.index');
        Route::resource('/partners', PartnerController::class);
        Route::resource('/categories', \App\Http\Controllers\Admin\CategoryController::class);
        Route::get('transactions', [\App\Http\Controllers\Admin\TransactionController::class, 'index'])->name('transactions.index');    
    });
});

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/event/{event}', [EventController::class, 'show'])->name('events.show');
Route::get('/checkout', [EventController::class, 'checkout'])->name('checkout');
Route::get('/my-ticket', [TicketController::class, 'ticket'])->name('ticket');
Route::get('/checkout/{event}', [App\Http\Controllers\CheckoutController::class, 'create'])->name('checkout.create');
Route::post('/checkout/{event}', [App\Http\Controllers\CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/payment/{order_id}', [\App\Http\Controllers\CheckoutController::class, 'payment'])->name('checkout.payment');
Route::get('/success/{order_id}', [\App\Http\Controllers\CheckoutController::class, 'success'])->name('checkout.success');
// Route::group(['prefix' => 'admin', 'as' => 'admin.'], function () {
// });

Route::get('/profil', function() {
    return view('profil');
});

Route::get('/katalog', function() {
    return view('katalog');
});

Route::get('/bantuan', function() {
    return view('bantuan');
});

