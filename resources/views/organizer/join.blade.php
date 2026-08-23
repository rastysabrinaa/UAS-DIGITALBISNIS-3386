@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 md:px-6 py-12 md:py-20">
    <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-slate-100 flex flex-col md:flex-row">
        <!-- Image Section -->
        <div class="w-full md:w-5/12 bg-indigo-600 relative overflow-hidden hidden md:block">
            <div class="absolute inset-0 opacity-20 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
            <div class="relative z-10 p-10 h-full flex flex-col justify-center text-white">
                <h2 class="text-3xl font-bold mb-4 leading-tight">Jadilah Bagian dari Kesuksesan!</h2>
                <p class="text-indigo-100 leading-relaxed mb-8">Buat acaramu, atur tiket, dan pantau pendapatanmu secara instan dalam satu platform.</p>
                <div class="space-y-4 text-sm font-medium">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-indigo-500 flex items-center justify-center shrink-0">✨</div>
                        <span>Akses Dashboard Khusus</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-indigo-500 flex items-center justify-center shrink-0">🚀</div>
                        <span>Penjualan Tiket Otomatis</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-indigo-500 flex items-center justify-center shrink-0">💸</div>
                        <span>Pemantauan Pendapatan</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Section -->
        <div class="w-full md:w-7/12 p-8 md:p-12">
            <div class="mb-8">
                <h3 class="text-2xl md:text-3xl font-extrabold text-slate-800 mb-2">Daftar Penyelenggara</h3>
                <p class="text-slate-500">Mulai perjalananmu membuat event hebat hari ini. Gratis pendaftaran!</p>
            </div>

            @auth
                @if(Auth::user()->role === 'user')
                    <div class="bg-indigo-50 p-6 rounded-2xl mb-6 border border-indigo-100">
                        <p class="text-indigo-800 font-medium mb-4">Halo, {{ Auth::user()->name }}! Klik tombol di bawah ini untuk mengupgrade akun Anda menjadi Organizer.</p>
                        <form action="{{ route('apply.organizer') }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full py-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-lg shadow-indigo-200 transition-all hover:scale-[1.02]">
                                Ajukan Akun Organizer Sekarang
                            </button>
                        </form>
                    </div>
                @elseif(Auth::user()->role === 'organizer' && Auth::user()->status === 'pending')
                    <div class="bg-amber-50 p-6 rounded-2xl mb-6 border border-amber-100 text-center">
                        <div class="text-4xl mb-4">⏳</div>
                        <h4 class="text-amber-800 font-bold text-lg mb-2">Pengajuan Sedang Diproses</h4>
                        <p class="text-amber-700 text-sm">Tim kami sedang meninjau pengajuan Anda. Mohon tunggu beberapa saat hingga akun Anda disetujui oleh Superadmin.</p>
                    </div>
                @else
                    <div class="bg-emerald-50 p-6 rounded-2xl mb-6 border border-emerald-100 text-center">
                        <div class="text-4xl mb-4">🎉</div>
                        <h4 class="text-emerald-800 font-bold text-lg mb-2">Anda Sudah Menjadi Organizer!</h4>
                        <a href="{{ route('admin.dashboard') }}" class="inline-block mt-4 px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl transition">
                            Masuk ke Dashboard
                        </a>
                    </div>
                @endif
            @else
                <div class="space-y-6">
                    <p class="text-slate-600 text-sm">Untuk mendaftar sebagai penyelenggara, Anda harus login dengan akun Google terlebih dahulu.</p>
                    
                    <a href="{{ route('auth.google') }}" class="flex items-center justify-center gap-3 w-full py-4 bg-white border-2 border-slate-200 hover:border-indigo-600 hover:bg-indigo-50 text-slate-700 font-bold rounded-xl transition-all shadow-sm">
                        <svg class="w-6 h-6" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/><path d="M1 1h22v22H1z" fill="none"/></svg>
                        Login via Google & Daftar
                    </a>
                </div>
            @endauth

            <div class="mt-8 pt-6 border-t border-slate-100">
                <p class="text-xs text-slate-400">Dengan mendaftar, Anda menyetujui Syarat dan Ketentuan serta Kebijakan Privasi AmikomEventHub.</p>
            </div>
        </div>
    </div>
</div>
@endsection
