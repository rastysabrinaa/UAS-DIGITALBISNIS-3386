<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Event;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReviewController extends Controller
{
    public function store(Request $request, $eventId)
    {
        $event = Event::findOrFail($eventId);

        // 1. Validasi: Cek apakah user sudah pernah memberi ulasan
        $existingReview = Review::where('user_id', auth()->id())
            ->where('event_id', $event->id)
            ->first();

        if ($existingReview) {
            return back()->with('error', 'Anda sudah memberikan ulasan untuk acara ini.');
        }

        // 2. Validasi Waktu (Gunakan $event->date atau $event->end_date)
        $eventDate = $event->end_date ?? $event->date;
        $eventEndDate = Carbon::parse($eventDate);
        $allowedReviewDate = $eventEndDate->copy()->addDay();

        if (Carbon::now()->lt($allowedReviewDate)) {
            return back()->with('error', 'Ulasan baru dapat diberikan 1 hari setelah acara selesai.');
        }

        // 3. Validasi Input Form
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        // 4. Simpan Ulasan ke Database
        Review::create([
            'user_id'      => auth()->id(),
            'event_id'     => $event->id,
            'organizer_id' => $event->organizer_id ?? $event->user_id, // Fallback jika organizer_id null
            'rating'       => $request->rating,
            'comment'      => $request->comment,
        ]);

        return back()->with('success', 'Ulasan berhasil ditambahkan!');
    }
}