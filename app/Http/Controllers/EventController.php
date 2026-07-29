<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    /**
     * Menampilkan detail event untuk publik
     */
    public function show(Event $event)
    {
        return view('events.show', compact('event'));
    }

    /**
     * Menampilkan daftar tiket saya (Customer)
     */
    public function myTickets()
    {
        // Ambil tiket milik user yang sedang login
        $tickets = \App\Models\Transaction::with('event')
            ->where('customer_email', auth()->user()->email)
            ->whereIn('status', ['success', 'settlement', 'capture'])
            ->latest()
            ->get();
            
        return view('tickets.index', compact('tickets'));
    }
}