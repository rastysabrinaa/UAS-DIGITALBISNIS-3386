<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EventController as AdminEventController;
use App\Http\Controllers\EventController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TicketController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/event/1', [EventController::class, 'show'])->name('events.show');
Route::get('/checkout', [EventController::class, 'checkout'])->name('checkout');
Route::get('/my-ticket', [TicketController::class, 'ticket'])->name('ticket');

Route::group(['prefix' => 'admin', 'as' => 'admin.'], function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    // Route::get('/events', [DashboardController::class, 'indexAdmin'])->name('events.index');
    Route::resource('/events', AdminEventController::class);
    Route::get('/transactions', [DashboardController::class, 'transactionsAdmin'])->name('transactions.index');
});


Route::get('/profil', function() {
    return view('profil');
});

Route::get('/katalog', function() {
    return view('katalog');
});

Route::get('/bantuan', function() {
    return view('bantuan');
});

