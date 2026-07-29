@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-6 py-10 mt-10">
    {{-- ALERT PESAN SUKSES / ERROR --}}
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-6 py-4 rounded-2xl mb-8 flex items-center gap-3 shadow-sm">
            <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <div>
                <p class="font-bold">Berhasil!</p>
                <p class="text-sm">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-2xl mb-8 flex items-center gap-3 shadow-sm">
            <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <div>
                <p class="font-bold">Terjadi Kesalahan!</p>
                <p class="text-sm">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
        {{-- KOLOM UTAMA (GAMBAR & DESKRIPSI) --}}
        <div class="lg:col-span-2 space-y-10">
            <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
                @if($event->poster_path)
                    <img src="{{ Str::startsWith($event->poster_path, 'http') ? $event->poster_path : asset('storage/' . $event->poster_path) }}" class="w-full h-[400px] object-cover" alt="{{ $event->title }}">
                @else
                    <div class="w-full h-[400px] bg-slate-900 flex items-center justify-center text-white">
                        <h3 class="text-4xl font-extrabold px-6 text-center leading-tight">{{ $event->title }}</h3>
                    </div>
                @endif
                
                <div class="p-8 md:p-10">
                    <span class="inline-block px-4 py-1.5 mb-6 bg-indigo-100 text-indigo-700 rounded-full text-sm font-bold uppercase tracking-wider">{{ $event->category->name ?? 'Umum' }}</span>
                    <h1 class="text-4xl md:text-5xl font-extrabold text-slate-900 mb-8 leading-tight">{{ $event->title }}</h1>
                    
                    @if($event->organizer)
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-6 bg-slate-50 rounded-3xl mb-10 border border-slate-100">
                            <div class="flex items-center gap-4">
                                <div class="w-14 h-14 bg-indigo-600 rounded-full flex items-center justify-center text-white font-bold text-2xl shadow-lg shadow-indigo-200">
                                    {{ substr($event->organizer->name ?? 'P', 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-xs text-slate-400 font-bold uppercase tracking-wider">Diselenggarakan Oleh</p>
                                    <h4 class="font-bold text-lg text-slate-900">{{ $event->organizer->name ?? 'Penyelenggara' }}</h4>
                                </div>
                            </div>
                            <a href="{{ route('organizer.profile', $event->organizer_id) }}" class="px-6 py-2.5 bg-white border border-slate-200 rounded-xl font-bold text-slate-600 hover:border-indigo-600 hover:text-indigo-600 transition shadow-sm self-start sm:self-auto">
                                Lihat Profil
                            </a>
                        </div>
                    @endif

                    <h2 class="text-2xl font-bold mb-6 flex items-center gap-2">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path></svg>
                        Deskripsi Acara
                    </h2>
                    <div class="text-slate-600 whitespace-pre-line leading-relaxed text-lg">
                        {{ $event->description }}
                    </div>
                </div>
            </div>

            {{-- BAGIAN ULASAN --}}
            <div>
                <h3 class="text-3xl font-extrabold mb-8 flex items-center gap-3">
                    <svg class="w-8 h-8 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>
                    Ulasan & Testimoni
                </h3>
                
                @auth
                    <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 p-8 md:p-10 mb-10">
                        <h4 class="font-bold text-xl mb-6">Beri Ulasan untuk Acara Ini</h4>
                        <form action="{{ url('/events/' . $event->id . '/reviews') }}" method="POST" class="space-y-6">
                            @csrf
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-3 uppercase tracking-wide">Rating Bintang</label>
                                <select name="rating" class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 outline-none font-medium transition" required>
                                    <option value="" disabled selected>-- Pilih Penilaian --</option>
                                    <option value="5">⭐⭐⭐⭐⭐ (5 - Sangat Bagus)</option>
                                    <option value="4">⭐⭐⭐⭐ (4 - Bagus)</option>
                                    <option value="3">⭐⭐⭐ (3 - Cukup)</option>
                                    <option value="2">⭐⭐ (2 - Kurang)</option>
                                    <option value="1">⭐ (1 - Sangat Buruk)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-3 uppercase tracking-wide">Ulasan Anda</label>
                                <textarea name="comment" rows="4" class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 outline-none font-medium transition" placeholder="Bagikan pengalamanmu mengikuti acara ini..." required></textarea>
                            </div>
                            <button type="submit" class="px-8 py-4 bg-indigo-600 text-white font-bold rounded-2xl shadow-xl shadow-indigo-200 hover:bg-indigo-700 active:scale-95 transition-all text-lg">
                                Kirim Ulasan
                            </button>
                        </form>
                    </div>
                @else
                    <div class="bg-amber-50 border border-amber-200 text-amber-800 px-6 py-4 rounded-2xl mb-10 font-medium">
                        Silakan <a href="{{ route('login') }}" class="font-bold underline hover:text-amber-900 transition">login terlebih dahulu</a> untuk memberikan ulasan pada acara ini.
                    </div>
                @endauth

                <div class="space-y-6">
                    @forelse($event->reviews ?? [] as $review)
                        <div class="bg-white rounded-[2rem] p-8 border border-slate-100 shadow-sm hover:shadow-md transition">
                            <div class="flex justify-between items-start mb-4">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 bg-slate-100 rounded-full flex items-center justify-center text-slate-500 font-bold text-lg">
                                        {{ substr($review->user->name ?? 'U', 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-900 text-lg">{{ $review->user->name ?? 'Pengguna' }}</p>
                                        <p class="text-sm text-slate-400 font-medium">{{ $review->created_at ? $review->created_at->diffForHumans() : '-' }}</p>
                                    </div>
                                </div>
                                <div class="text-amber-400 text-xl tracking-widest">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $review->rating) ★ @else <span class="text-slate-200">★</span> @endif
                                    @endfor
                                </div>
                            </div>
                            <p class="text-slate-600 text-lg leading-relaxed mt-4">{{ $review->comment }}</p>
                        </div>
                    @empty
                        <div class="text-center py-12 bg-slate-50 rounded-[2rem] border border-dashed border-slate-300 text-slate-500">
                            <svg class="w-12 h-12 mx-auto mb-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                            <p class="text-lg font-bold">Belum ada ulasan untuk acara ini.</p>
                            <p class="mt-2">Jadilah yang pertama memberikan ulasan!</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- SIDEBAR TIKET --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-slate-100 p-8 sticky top-32">
                <h3 class="text-2xl font-extrabold mb-8 pb-6 border-b border-slate-100 flex items-center gap-3">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
                    Detail Pelaksanaan
                </h3>
                
                <div class="space-y-8">
                    <div class="flex gap-4 items-start">
                        <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Tanggal & Waktu</p>
                            <p class="font-bold text-slate-900 text-lg">{{ \Carbon\Carbon::parse($event->date)->translatedFormat('d F Y') }}</p>
                            <p class="text-slate-500">{{ \Carbon\Carbon::parse($event->date)->format('H:i') }} WIB</p>
                        </div>
                    </div>
                    
                    <div class="flex gap-4 items-start">
                        <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Lokasi</p>
                            <p class="font-bold text-slate-900 text-lg">{{ $event->location }}</p>
                        </div>
                    </div>

                    <div class="flex gap-4 items-start">
                        <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path></svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Sisa Tiket</p>
                            <span class="inline-block px-4 py-1.5 bg-emerald-100 text-emerald-700 rounded-full text-sm font-extrabold tracking-wide">{{ $event->stock ?? 0 }} Tersedia</span>
                        </div>
                    </div>

                    <div class="p-6 bg-slate-50 rounded-3xl border border-slate-100">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Harga Tiket</p>
                        <p class="text-4xl font-black text-indigo-600">
                            {{ $event->price > 0 ? 'Rp ' . number_format($event->price, 0, ',', '.') : 'Gratis' }}
                        </p>
                    </div>
                </div>

                <div class="mt-10 pt-8 border-t border-slate-100">
                    @if(($event->stock ?? 0) > 0)
                        <a href="{{ route('checkout.create', $event->id) }}" class="block w-full py-5 text-center bg-indigo-600 text-white rounded-2xl font-black text-xl shadow-xl shadow-indigo-200 hover:bg-indigo-700 active:scale-95 transition-all">
                            Beli Tiket Sekarang
                        </a>
                    @else
                        <button class="w-full py-5 bg-slate-200 text-slate-500 rounded-2xl font-black text-xl cursor-not-allowed" disabled>
                            Tiket Habis
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection