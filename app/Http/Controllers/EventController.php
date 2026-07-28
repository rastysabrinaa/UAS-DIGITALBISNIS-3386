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
        // Kode untuk menampilkan tiket user yang sedang login
        return view('tickets.index');
    }
}