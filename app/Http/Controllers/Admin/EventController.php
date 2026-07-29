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
            $uploadedFileUrl = cloudinary()->uploadApi()->upload($request->file('poster_path')->getRealPath())['secure_url'];
            $validated['poster_path'] = $uploadedFileUrl;
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

        // Jika mengunggah poster_path baru, upload ke Cloudinary
        if ($request->hasFile('poster_path')) {
            $uploadedFileUrl = cloudinary()->uploadApi()->upload($request->file('poster_path')->getRealPath())['secure_url'];
            $validated['poster_path'] = $uploadedFileUrl;
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
        // Hapus file poster_path dari storage jika ada (optional since we use cloudinary mostly)
        if ($event->poster_path && !Str::startsWith($event->poster_path, 'http')) {
            Storage::disk('public')->delete($event->poster_path);
        }

        $event->delete();

        return redirect()->route('admin.events.index')
            ->with('success', 'Event berhasil dihapus!');
    }
}