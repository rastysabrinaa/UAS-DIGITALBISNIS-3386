<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    /**
     * Menampilkan daftar semua event untuk admin.
     */
    public function index()
    {
        $events = Event::with('category')->latest()->paginate(10);
        return view('admin.events.index', compact('events'));
    }

    /**
     * Menampilkan form tambah event baru.
     */
    public function create()
    {
        $categories = Category::all();
        return view('admin.events.create', compact('categories'));
    }

    /**
     * Menyimpan data event baru ke database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string',
            'date'        => 'required|date',
            'location'    => 'required|string|max:255',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0', // Diubah ke min:0 agar bisa set stok 0
            'poster_path' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $validated['slug'] = Str::slug($request->title);
        $validated['organizer_id'] = auth()->id();

        // Upload poster_path jika ada
        if ($request->hasFile('poster_path')) {
            $path = $request->file('poster_path')->store('posters', 's3');
            $validated['poster_path'] = Storage::disk('s3')->url($path);
        }

        Event::create($validated);

        return redirect()->route('admin.events.index')
            ->with('success', 'Event berhasil ditambahkan!');
    }

    /**
     * Menampilkan detail event spesifik di admin.
     */
    public function show(Event $event)
    {
        return view('admin.events.show', compact('event'));
    }

    /**
     * Menampilkan form edit event.
     */
    public function edit(Event $event)
    {
        $categories = Category::all();
        return view('admin.events.edit', compact('event', 'categories'));
    }

    /**
     * Memperbarui data event di database.
     */
    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string',
            'date'        => 'required|date',
            'location'    => 'required|string|max:255',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0', // Diubah ke min:0
            'poster_path' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $validated['slug'] = Str::slug($request->title);

        // Jika mengunggah poster_path baru, upload ke S3
        if ($request->hasFile('poster_path')) {
            $path = $request->file('poster_path')->store('posters', 's3');
            $validated['poster_path'] = Storage::disk('s3')->url($path);
        }

        // Eksekusi Update ke Database
        $event->update($validated);

        return redirect()->route('admin.events.index')
            ->with('success', 'Event berhasil diperbarui!');
    }

    /**
     * Menghapus event beserta poster_path-nya.
     */
    public function destroy(Event $event)
    {
        // Hapus file poster_path dari storage jika ada
        if ($event->poster_path && Str::contains($event->poster_path, 'supabase.co')) {
            $path = parse_url($event->poster_path, PHP_URL_PATH);
            // Extract the path after bucket name (e.g., 'event-posters/posters/filename.jpg' -> 'posters/filename.jpg')
            $bucketSegment = '/event-posters/';
            if (Str::contains($path, $bucketSegment)) {
                $s3Path = explode($bucketSegment, $path)[1];
                Storage::disk('s3')->delete($s3Path);
            }
        }

        $event->delete();

        return redirect()->route('admin.events.index')
            ->with('success', 'Event berhasil dihapus!');
    }
}