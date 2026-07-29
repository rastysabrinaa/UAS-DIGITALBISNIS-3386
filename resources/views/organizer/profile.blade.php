@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-12 max-w-7xl">
    <!-- Header Profil -->
    <div class="bg-white rounded-3xl p-8 shadow-sm border border-slate-100 flex flex-col md:flex-row items-center gap-8 mb-12">
        <div class="w-24 h-24 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-4xl font-black">
            {{ strtoupper(substr($organizer->name, 0, 1)) }}
        </div>
        <div class="text-center md:text-left">
            <h1 class="text-3xl font-extrabold text-slate-800">{{ $organizer->name }}</h1>
            <p class="text-slate-500 font-medium mt-1">Penyelenggara Event Khusus Amikom</p>
            <div class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-indigo-50 text-indigo-700 rounded-full text-sm font-bold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                {{ $events->count() }} Event Diselenggarakan
            </div>
        </div>
    </div>

    <!-- Event List -->
    <h2 class="text-2xl font-black text-slate-800 mb-6 border-b pb-4">Event oleh {{ $organizer->name }}</h2>

    @if($events->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            @foreach($events as $event)
                @php
                    $isPast = \Carbon\Carbon::parse($event->date)->isPast();
                @endphp
                <a href="{{ route('events.show', $event->id) }}" class="group block">
                    <div class="bg-white rounded-3xl shadow-sm hover:shadow-2xl transition-all duration-300 border border-slate-100 overflow-hidden flex flex-col h-full">
                        <div class="relative aspect-[4/3] overflow-hidden">
                            <img src="{{ $event->poster_path ? (Str::startsWith($event->poster_path, 'http') ? $event->poster_path : asset('storage/' . $event->poster_path)) : 'https://placehold.co/600x400' }}" alt="{{ $event->title }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500 {{ $isPast ? 'grayscale-[30%]' : '' }}">
                            
                            @if($isPast)
                                <div class="absolute top-4 right-4 bg-slate-900/90 backdrop-blur-sm text-white px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider">
                                    Selesai
                                </div>
                            @else
                                <div class="absolute top-4 right-4 bg-indigo-600/90 backdrop-blur-sm text-white px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider shadow-lg">
                                    {{ \Carbon\Carbon::parse($event->date)->format('d M') }}
                                </div>
                            @endif
                        </div>
                        <div class="p-6 flex flex-col flex-grow">
                            <h3 class="text-xl font-bold text-slate-800 mb-2 line-clamp-2">{{ $event->title }}</h3>
                            <p class="text-indigo-600 font-black text-lg mt-auto">
                                {{ $event->price == 0 ? 'Gratis' : 'Rp ' . number_format($event->price, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @else
        <div class="text-center py-20 bg-slate-50 rounded-3xl border border-slate-100">
            <svg class="w-16 h-16 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            <h3 class="text-xl font-bold text-slate-600">Belum ada event</h3>
            <p class="text-slate-400 mt-2">Penyelenggara ini belum membuat event apapun.</p>
        </div>
    @endif
</div>
@endsection
