<?php

use Illuminate\Support\Facades\Route;

// Import Controllers (Admin & Superadmin)
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EventController as AdminEventController;
use App\Http\Controllers\Admin\PartnerController;
use App\Http\Controllers\Admin\ScannerController;
use App\Http\Controllers\Admin\SuperAdminController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\VoucherController;

// Import Controllers (Public, User, & Organizer)
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\JabatanController;
use App\Http\Controllers\MidtransWebhookController;
use App\Http\Controllers\OrganizerController;
use App\Http\Controllers\PengurusController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SocialiteController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ==================== AUTHENTICATION & OAUTH ====================
Route::get('/login', function () {
    return redirect()->route('admin.login');
})->name('login');

Route::get('/auth/google', [SocialiteController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [SocialiteController::class, 'handleGoogleCallback'])->name('auth.google.callback');

// ==================== WEBHOOKS ====================
Route::post('/midtrans/callback', [MidtransWebhookController::class, 'handle'])->name('midtrans.callback');

// ==================== PUBLIC / FRONTEND ====================
Route::get('/', [HomeController::class, 'index'])->name('home');

// Halaman Statis
Route::view('/profil', 'profil')->name('profil');
Route::view('/katalog', 'katalog')->name('katalog');
Route::view('/bantuan', 'bantuan')->name('bantuan');

// Resource Umum
Route::resource('jabatan', JabatanController::class);
Route::resource('pengurus', PengurusController::class);

// Public Dynamic Routes
Route::get('/event/{event}', [EventController::class, 'show'])->name('events.show');
Route::get('/organizer/profile/{id}', [OrganizerController::class, 'showProfile'])->name('organizer.profile');
Route::view('/organizer/join', 'organizer.join')->name('organizer.join');

// ==================== USER AUTHENTICATED (Pembeli / Mahasiswa) ====================
Route::middleware(['auth'])->group(function () {
    // Tiket Saya
    Route::get('/my-tickets', [EventController::class, 'myTickets'])->name('tickets.index');

    // Checkout & Pembayaran
    Route::get('/checkout/{event}', [CheckoutController::class, 'create'])->name('checkout.create');
    Route::post('/checkout/{event}/apply-voucher', [CheckoutController::class, 'applyVoucher'])->name('checkout.apply-voucher');
    Route::post('/checkout/{event}', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/payment/{order_id}', [CheckoutController::class, 'payment'])->name('checkout.payment');
    Route::get('/success/{order_id}', [CheckoutController::class, 'success'])->name('checkout.success');

    // Review Event
    Route::post('/events/{eventId}/reviews', [ReviewController::class, 'store'])->name('events.reviews.store');

    // Pengajuan Organizer
    Route::post('/apply-organizer', function () {
        $user = auth()->user();
        if ($user->role === 'user') {
            $user->role = 'organizer';
            $user->status = 'pending';
            $user->save();
            return redirect()->route('admin.dashboard')->with('success', 'Pengajuan menjadi organizer berhasil dikirim dan menunggu persetujuan admin.');
        }
        return back()->with('error', 'Gagal mengajukan.');
    })->name('apply.organizer');
});

// ==================== DASHBOARD PANEL (ADMIN / ORGANIZER / SUPERADMIN) ====================
// ==================== DASHBOARD PANEL (ADMIN / ORGANIZER / SUPERADMIN) ====================
Route::prefix('admin')->name('admin.')->group(function () {
    
    // Auth Admin (Guest)
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.post');
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/superadmin-dashboard', [DashboardController::class, 'index'])->name('superadmin.dashboard');

    // 1. DASHBOARD BERSAMA (Bisa diakses Organizer & Superadmin)
    Route::middleware(['auth', 'role:organizer,superadmin'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::resource('/events', AdminEventController::class);
        Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
        
        // Scanner Routes
        Route::get('/scanner/{event}', [ScannerController::class, 'index'])->name('scanner.index');
        Route::post('/scanner/{event}/verify', [ScannerController::class, 'verify'])->name('scanner.verify');

        // Vouchers / Dynamic Pricing
        Route::resource('/vouchers', VoucherController::class)->only(['index', 'store', 'destroy']);
    });

    // 2. FITUR KHUSUS SUPERADMIN
    Route::middleware(['auth', 'role:superadmin'])->group(function () {
        Route::resource('/partners', PartnerController::class);
        Route::resource('/categories', CategoryController::class);
        
        // Pengawasan Kelayakan Penyelenggara
        Route::get('/organizers', [SuperAdminController::class, 'indexOrganizers'])->name('organizers.index');
        Route::patch('/organizers/{id}/status', [SuperAdminController::class, 'updateStatus'])->name('organizers.updateStatus');
    });

});