<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Transaction;
use Illuminate\Http\Request;

class OrganizerController extends Controller
{
    public function dashboard()
    {
        $organizerId = auth()->id();

        // 1. Ambil Event milik Organizer ini saja
        $events = Event::where('organizer_id', $organizerId)->get();
        $eventIds = $events->pluck('id');

        // 2. Hitung Analitik Pendapatan
        $totalEvents = $events->count();
        $totalTicketsSold = Transaction::whereIn('event_id', $eventIds)
            ->where('status', 'success')
            ->count();

        $totalRevenue = Transaction::whereIn('event_id', $eventIds)
            ->where('status', 'success')
            ->sum('total_price');

        // 3. Ambil Transaksi Terbaru
        $recentTransactions = Transaction::whereIn('event_id', $eventIds)
            ->with('event')
            ->latest()
            ->take(5)
            ->get();

        return view('organizer.dashboard', compact(
            'events', 
            'totalEvents', 
            'totalTicketsSold', 
            'totalRevenue', 
            'recentTransactions'
        ));
    }

    public function showProfile($id)
    {
        $organizer = \App\Models\User::where('role', 'organizer')->findOrFail($id);
        $events = Event::where('organizer_id', $id)->with('category')->latest()->get();

        return view('organizer.profile', compact('organizer', 'events'));
    }
}