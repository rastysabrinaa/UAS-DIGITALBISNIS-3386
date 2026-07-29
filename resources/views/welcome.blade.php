@extends('layouts.app')

@section('content')

    <!-- Hero Section -->
    <section class="max-w-7xl mx-auto px-6 py-20 flex flex-col md:flex-row items-center gap-12">
        <div class="flex-1 space-y-8">
            <span
                class="inline-block px-4 py-1.5 bg-indigo-100 text-indigo-700 rounded-full text-sm font-bold uppercase tracking-wider">#1
                Event Platform</span>
            <h1 class="text-5xl md:text-7xl font-extrabold leading-tight">
                Temukan & Pesan <span class="text-indigo-600">Tiket Event</span> Impianmu.
            </h1>
            <p class="text-lg text-slate-500 max-w-lg leading-relaxed">
                Dari konser musik hingga workshop teknologi, semua ada di genggamanmu. Pesan aman & cepat dengan
                Midtrans.
            </p>
            <div class="flex gap-4">
                <a href="#events"
                    class="px-8 py-4 bg-indigo-600 text-white rounded-2xl font-bold text-lg shadow-xl shadow-indigo-200 hover:scale-105 transition-transform">
                    Mulai Jelajah
                </a>
                <a href="#"
                    class="px-8 py-4 border-2 border-slate-200 rounded-2xl font-bold text-lg hover:border-indigo-600 hover:text-indigo-600 transition">
                    Cara Pesan
                </a>
            </div>
        </div>
        <div class="flex-1 relative">
            <div
                class="absolute -top-10 -left-10 w-64 h-64 bg-indigo-400 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob">
            </div>
            <div
                class="absolute -bottom-10 -right-10 w-64 h-64 bg-purple-400 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-2000">
            </div>
            <img src="assets/concert.png" alt="Concert"
                class="rounded-[2rem] shadow-2xl relative z-10 w-full object-cover aspect-[4/5] object-center">

            <div class="absolute -bottom-6 -left-6 glass p-6 rounded-2xl shadow-xl z-20 border border-white">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center text-green-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 font-bold uppercase">Terverifikasi</p>
                        <p class="font-bold">Pembayaran Aman via Midtrans</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Events Grid -->
    <section id="events" class="max-w-7xl mx-auto px-6 py-20">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-12 gap-4">
            <div>
                <h2 class="text-3xl font-extrabold mb-2">Jelajahi Event</h2>
                <p class="text-slate-500 font-medium">Temukan acara seru mendatang maupun ulasan acara yang telah berlangsung!</p>
            </div>
            <div class="flex gap-2 flex-wrap">
                <a href="/" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-xl text-black font-semibold text-sm transition">Semua Kategori</a>

                @foreach ($categories as $cat)
                <a href="/?category={{ $cat->slug }}" class="px-4 py-2 bg-indigo-100 hover:bg-indigo-200 text-indigo-700 rounded-xl font-semibold text-sm transition">
                    {{ $cat->name }}
                </a>
                @endforeach
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($events as $event)
                @php
                    $isPast = \Carbon\Carbon::parse($event->date)->isPast();
                @endphp

                <!-- Event Card -->
                <div class="group bg-white rounded-3xl border border-slate-100 shadow-sm hover:shadow-2xl transition-all duration-300 overflow-hidden flex flex-col justify-between">
                    <div>
                        <div class="relative overflow-hidden aspect-[3/4]">
                            <img src="{{ $event->poster_path ? (Str::startsWith($event->poster_path, 'http') ? $event->poster_path : asset('storage/' . $event->poster_path)) : 'https://placehold.co/600x800' }}" alt="{{ $event->title }}"
                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500 {{ $isPast ? 'grayscale-[30%]' : '' }}">

                            <!-- Kategori Badge -->
                            <div class="absolute top-4 left-4 px-3 py-1 bg-white/90 backdrop-blur rounded-lg text-xs font-bold uppercase text-indigo-600 shadow-sm">
                                {{ $event->category->name }}
                            </div>

                            <!-- Status Badge (Mendatang / Selesai) -->
                            <div class="absolute top-4 right-4 px-3 py-1 rounded-lg text-xs font-bold uppercase shadow-sm {{ $isPast ? 'bg-slate-800 text-white' : 'bg-emerald-500 text-white' }}">
                                {{ $isPast ? 'Selesai' : 'Mendatang' }}
                            </div>
                        </div>

                        <div class="p-6">
                            <h3 class="text-xl font-bold mb-2 group-hover:text-indigo-600 transition line-clamp-2">
                                {{ $event->title }}
                            </h3>
                            
                            <div class="flex items-center gap-2 text-slate-500 text-sm mb-4">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>{{ \Carbon\Carbon::parse($event->date)->translatedFormat('d M Y, H:i') }} WIB</span>
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="px-6 pb-6">
                        @if($isPast)
                            <a href="{{ route('events.show', $event->id) }}" class="block text-center w-full py-3 bg-amber-500 hover:bg-amber-600 text-white rounded-xl font-bold transition shadow-md shadow-amber-100">
                                ⭐ Lihat & Beri Ulasan
                            </a>
                        @else
                            <a href="{{ route('events.show', $event->id) }}" class="block text-center w-full py-3 bg-indigo-50 text-indigo-600 rounded-xl font-bold hover:bg-indigo-600 hover:text-white transition">
                                Beli Tiket
                            </a>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12 text-slate-400">
                    <p class="text-lg font-bold">Belum ada event yang tersedia saat ini.</p>
                </div>
            @endforelse
        </div>
    </section>

    <!-- Partners Section -->
    <section class="max-w-7xl mx-auto px-6 py-20 bg-slate-50 rounded-[3rem] mb-20">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-extrabold mb-2">Partner Resmi Kami</h2>
            <p class="text-slate-500 font-medium">Platform AmikomEventHub didukung oleh berbagai partner terbaik.</p>
        </div>
        <div class="flex flex-wrap justify-center gap-8 md:gap-12 items-center">
            @foreach($partners as $partner)
            <div class="flex flex-col items-center justify-center p-4 grayscale hover:grayscale-0 transition duration-300">
                <img src="{{ $partner->logo_url }}" alt="{{ $partner->name }}" class="max-h-16 object-contain mb-2">
                <span class="text-sm font-bold text-slate-400">{{ $partner->name }}</span>
            </div>
            @endforeach
        </div>
    </section>

@endsection