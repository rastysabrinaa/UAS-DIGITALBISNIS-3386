<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Menjumlahkan semua nominal total_price dari kolom Transaksi Lunas
        $totalRevenue = Transaction::whereIn('status', ['settlement', 'success'])->sum('total_price');
        
        // 2. Menghitung berapa orang tamu yang tiketnya sudah Lunas
        $ticketsSold = Transaction::whereIn('status', ['settlement', 'success'])->count();
        
        // 3. Menghitung jumlah Acara Mendatang yang aktif diselenggarakan
        $activeEvents = Event::where('date', '>=', now())->count();
        
        // 4. Menghitung transaksi ngadat (Status belum dibayar pelanggan / Pending)
        $pendingOrders = Transaction::where('status', 'pending')->count();
        
        // 5. Menyertakan 5 daftar riwayat pesanan (History) paling mutakhir di panel
        $recentTransactions = Transaction::with('event')->latest()->take(5)->get();

        // =========================================================================
        // 6. OLAH DATA GRAFIK PERTUMBUHAN (6 BULAN TERAKHIR)
        // =========================================================================
        $months = [];
        $userData = [];
        $eventData = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthName = $date->translatedFormat('F Y'); // Contoh: Juli 2026
            
            // Hitung Jumlah Pengguna/User Baru Terdaftar per Bulan
            $usersCount = User::whereYear('created_at', $date->year)
                              ->whereMonth('created_at', $date->month)
                              ->count();

            // Hitung Jumlah Event Baru Dibuat per Bulan
            $eventsCount = Event::whereYear('created_at', $date->year)
                                ->whereMonth('created_at', $date->month)
                                ->count();

            $months[] = $monthName;
            $userData[] = $usersCount;
            $eventData[] = $eventsCount;
        }

        return view('admin.dashboard', compact(
            'totalRevenue', 
            'ticketsSold', 
            'activeEvents', 
            'pendingOrders', 
            'recentTransactions',
            'months',
            'userData',
            'eventData'
        ));
    }

    public function indexAdmin() 
    {
        return view('admin.events');
    }

    public function transactionsAdmin() 
    {
        return view('admin.transactions');
    }
}