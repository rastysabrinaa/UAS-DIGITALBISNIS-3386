@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-6 py-10 mt-10">
    <h1 class="text-4xl font-extrabold text-slate-900 mb-2">Tiket Saya</h1>
    <p class="text-slate-500 mb-10">Daftar semua tiket acara yang telah Anda beli.</p>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($tickets as $ticket)
            <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm overflow-hidden flex flex-col hover:shadow-lg transition">
                <div class="h-40 bg-indigo-50 relative">
                    @if($ticket->event && $ticket->event->poster_path)
                        <img src="{{ Str::startsWith($ticket->event->poster_path, 'http') ? $ticket->event->poster_path : asset('storage/' . $ticket->event->poster_path) }}" class="w-full h-full object-cover" alt="Banner">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-slate-800 text-white font-bold text-xl px-4 text-center">
                            {{ $ticket->event->title ?? 'Acara Tidak Ditemukan' }}
                        </div>
                    @endif
                    <div class="absolute top-4 right-4 px-3 py-1 bg-emerald-500 text-white text-xs font-bold uppercase rounded-lg shadow-sm">
                        Berhasil
                    </div>
                </div>
                <div class="p-6 flex-1 flex flex-col">
                    <p class="text-xs font-bold text-indigo-600 uppercase tracking-wider mb-2">Order ID: {{ $ticket->order_id }}</p>
                    <h3 class="text-xl font-bold text-slate-900 mb-4 line-clamp-2">{{ $ticket->event->title ?? 'Acara Dihapus' }}</h3>
                    
                    <div class="mt-auto space-y-3">
                        <div class="flex items-center gap-3 text-sm text-slate-600">
                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <span>{{ $ticket->event ? \Carbon\Carbon::parse($ticket->event->date)->translatedFormat('d F Y, H:i') : '-' }} WIB</span>
                        </div>
                        <div class="flex items-center gap-3 text-sm text-slate-600">
                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <span class="line-clamp-1">{{ $ticket->event->location ?? '-' }}</span>
                        </div>
                    </div>
                    
                    <div class="mt-6 pt-6 border-t border-slate-100">
                        <a href="{{ route('checkout.success', $ticket->order_id) }}" class="block w-full py-3 text-center bg-indigo-50 text-indigo-700 font-bold rounded-xl hover:bg-indigo-600 hover:text-white transition">
                            Lihat E-Ticket
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-16 text-center bg-slate-50 rounded-[2rem] border border-dashed border-slate-200">
                <svg class="w-16 h-16 mx-auto mb-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
                <h3 class="text-xl font-bold text-slate-700 mb-2">Belum Ada Tiket</h3>
                <p class="text-slate-500 mb-6">Anda belum membeli tiket acara apa pun.</p>
                <a href="/#events" class="inline-block px-8 py-3 bg-indigo-600 text-white font-bold rounded-xl shadow-lg hover:bg-indigo-700 transition">
                    Jelajahi Event
                </a>
            </div>
        @endforelse
    </div>
</div>
@endsection
