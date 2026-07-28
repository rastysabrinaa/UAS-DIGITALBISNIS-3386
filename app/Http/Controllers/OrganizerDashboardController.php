<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Transaction; // Pastikan Model Transaction sudah ada
use Illuminate\Http\Request;

class OrganizerDashboardController extends Controller
{
    /**
     * Dashboard Analitik Pendapatan Penyelenggara
     */
    public function index()
    {
        $organizerId = auth()->id();

        // Mengambil total acara milik organizer
        $totalEvents = Event::where('organizer_id', $organizerId)->count();

        // Mengambil total pendapatan dari transaksi yang sudah dibayar (paid)
        $totalRevenue = Transaction::whereHas('event', function ($q) use ($organizerId) {
            $q->where('organizer_id', $organizerId);
        })->where('status', 'paid')->sum('total_price');

        return view('organizer.dashboard', compact('totalEvents', 'totalRevenue'));
    }
}