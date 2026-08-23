<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Voucher;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    public function index()
    {
        // Hanya ambil event milik organizer ini (atau semua jika superadmin)
        $user = auth()->user();
        $query = Event::query();
        
        if ($user->role !== 'superadmin') {
            $query->where('organizer_id', $user->id);
        }
        $events = $query->get();

        // Ambil voucher yang event_id nya null (semua event), atau yang event-nya milik organizer ini
        $eventIds = $events->pluck('id')->toArray();
        $vouchers = Voucher::whereNull('event_id')
            ->orWhereIn('event_id', $eventIds)
            ->latest()
            ->paginate(10);

        return view('admin.vouchers.index', compact('vouchers', 'events'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:vouchers,code',
            'discount_percent' => 'required|integer|min:1|max:100',
            'event_id' => 'nullable|exists:events,id',
            'valid_until' => 'nullable|date',
            'quota' => 'nullable|integer|min:1',
        ]);

        $user = auth()->user();
        if ($request->event_id && $user->role !== 'superadmin') {
            $event = Event::findOrFail($request->event_id);
            if ($event->organizer_id !== $user->id) {
                return back()->with('error', 'Unauthorized: Anda tidak bisa membuat voucher untuk event orang lain.');
            }
        }

        Voucher::create([
            'code' => strtoupper($request->code),
            'discount_percent' => $request->discount_percent,
            'event_id' => $request->event_id,
            'valid_until' => $request->valid_until,
            'quota' => $request->quota,
        ]);

        return back()->with('success', 'Voucher berhasil dibuat!');
    }

    public function destroy($id)
    {
        $voucher = Voucher::findOrFail($id);
        $user = auth()->user();
        
        // Cek otoritas
        if ($voucher->event_id && $user->role !== 'superadmin') {
            if ($voucher->event->organizer_id !== $user->id) {
                return back()->with('error', 'Unauthorized.');
            }
        }
        // Superadmin bebas menghapus, tapi organizer tidak boleh menghapus voucher global (yang dibuat admin untuk semua event)
        if (is_null($voucher->event_id) && $user->role !== 'superadmin') {
            return back()->with('error', 'Unauthorized: Hanya superadmin yang dapat menghapus voucher global.');
        }

        $voucher->delete();
        return back()->with('success', 'Voucher dihapus!');
    }
}
