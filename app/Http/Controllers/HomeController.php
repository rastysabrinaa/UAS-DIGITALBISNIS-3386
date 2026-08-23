<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Event;
use App\Models\Partners;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request) 
    {
        $categories = \Illuminate\Support\Facades\Cache::remember('categories', 3600, function () {
            return Category::all();
        });

        $partners = \Illuminate\Support\Facades\Cache::remember('partners', 3600, function () {
            return Partners::all();
        });

        // 1. Inisialisasi query tanpa membatasi tanggal (agar event berlalu tetap tampil)
        $query = Event::with('category');

        // 2. Filter berdasarkan slug kategori jika ada query string ?category=...
        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // 3. Pengurutan Pintar:
        // - Event yang AKAN DATANG ditaruh di paling atas (diurutkan dari tanggal terdekat)
        // - Event yang SUDAH SELESAI ditaruh di bawahnya (diurutkan dari yang baru selesai)
        $events = $query->orderByRaw("CASE WHEN date >= NOW() THEN 0 ELSE 1 END")
                       ->orderByRaw("CASE WHEN date >= NOW() THEN date END ASC")
                       ->orderByRaw("CASE WHEN date < NOW() THEN date END DESC")
                       ->get();

        return view('welcome', compact('events', 'categories', 'partners'));
    }
}