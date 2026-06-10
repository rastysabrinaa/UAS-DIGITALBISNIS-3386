<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Category;

class EventController extends Controller
{
    public function show(Event $event)
    {
        $categories = Category::all(); // ambil semua kategori
        return view('event-detail', compact('event', 'categories'));
    }

    public function checkout() {
        return view('checkout');
    }
}
