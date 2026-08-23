<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'AmikomEventHub')</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

    <!-- PWA -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#4f46e5">
    <link rel="apple-touch-icon" href="https://placehold.co/192x192/4f46e5/ffffff?text=AH">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .glass {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
        }
    </style>
</head>

<body class="bg-slate-50 text-slate-900">

    <!-- Navigation -->
    <nav class="glass sticky top-0 md:top-8 z-50 md:mx-4 md:mt-4 px-4 md:px-6 py-4 md:rounded-2xl border-b md:border border-white/20 shadow-lg flex flex-wrap justify-between items-center">
        <div class="flex items-center gap-2">
            <img src="{{ asset('images/logo.png') }}" alt="AmikomEventHub Logo" class="w-10 h-10 rounded-full shadow-sm bg-white">
            <span class="text-xl font-bold tracking-tight">AmikomEventHub</span>
        </div>
        
        <!-- Hamburger Button -->
        <button id="mobile-menu-btn" class="md:hidden p-2 text-slate-700 hover:bg-slate-100 rounded-lg focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>

        <!-- Desktop Menu -->
        <div class="hidden md:flex gap-8 font-medium">
            <a href="/" class="hover:text-indigo-600 transition">Beranda</a>
            <a href="/#events" class="hover:text-indigo-600 transition">Jelajahi Event</a>
            <a href="{{ route('organizer.join') }}" class="hover:text-indigo-600 transition">Buat Event</a>
        </div>

        <div class="hidden md:flex gap-3 items-center">
            @auth
                <div class="relative group">
                    <button class="px-5 py-2.5 bg-slate-100 rounded-xl font-semibold hover:bg-slate-200 transition flex items-center gap-2">
                        {{ Auth::user()->name }}
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-slate-100 hidden group-hover:block z-50">
                        <div class="p-2 space-y-1">
                            @if(Auth::user()->role === 'superadmin' || (Auth::user()->role === 'organizer' && Auth::user()->status === 'approved'))
                                <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 rounded-lg">Dashboard Admin</a>
                            @elseif(Auth::user()->role === 'organizer' && Auth::user()->status === 'pending')
                                <span class="block px-4 py-2 text-sm text-amber-600 font-medium">⏳ Menunggu Persetujuan</span>
                            @elseif(Auth::user()->role === 'user')
                                <a href="{{ route('organizer.join') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 rounded-lg">Ajukan sbg Organizer</a>
                            @endif
                            <a href="{{ route('tickets.index') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 rounded-lg">Tiket Saya</a>
                            <form action="{{ route('admin.logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 rounded-lg">Logout</button>
                            </form>
                        </div>
                    </div>
                </div>
            @else
                <a href="{{ route('admin.login') }}" class="px-5 py-2.5 rounded-xl font-semibold hover:bg-slate-200 transition">Login</a>
                <a href="{{ route('auth.google') }}" class="px-5 py-2.5 bg-indigo-600 text-white rounded-xl font-semibold shadow-lg shadow-indigo-200 hover:bg-indigo-700 transition">Daftar</a>
            @endauth
        </div>

        <!-- Mobile Menu Dropdown -->
        <div id="mobile-menu" class="hidden w-full md:hidden mt-4 pb-4 border-t border-slate-200">
            <div class="flex flex-col gap-4 mt-4 font-medium px-2">
                <a href="/" class="hover:text-indigo-600 transition block">Beranda</a>
                <a href="/#events" class="hover:text-indigo-600 transition block">Jelajahi Event</a>
                <a href="{{ route('organizer.join') }}" class="hover:text-indigo-600 transition block">Buat Event</a>
                
                <hr class="border-slate-200 my-2">
                
                @auth
                    <div class="text-sm text-slate-500 mb-2">Halo, {{ Auth::user()->name }}</div>
                    @if(Auth::user()->role === 'superadmin' || (Auth::user()->role === 'organizer' && Auth::user()->status === 'approved'))
                        <a href="{{ route('admin.dashboard') }}" class="hover:text-indigo-600 transition block">Dashboard Admin</a>
                    @elseif(Auth::user()->role === 'organizer' && Auth::user()->status === 'pending')
                        <span class="text-amber-600 font-medium block">⏳ Menunggu Persetujuan Organizer</span>
                    @elseif(Auth::user()->role === 'user')
                        <a href="{{ route('organizer.join') }}" class="hover:text-indigo-600 transition block">Ajukan sbg Organizer</a>
                    @endif
                    <a href="{{ route('tickets.index') }}" class="hover:text-indigo-600 transition block">Tiket Saya</a>
                    <form action="{{ route('admin.logout') }}" method="POST" class="mt-2">
                        @csrf
                        <button type="submit" class="text-red-600 hover:text-red-700 block w-full text-left">Logout</button>
                    </form>
                @else
                    <div class="flex flex-col gap-3 mt-2">
                        <a href="{{ route('admin.login') }}" class="text-center py-2.5 bg-slate-100 rounded-xl font-semibold hover:bg-slate-200 transition">Login</a>
                        <a href="{{ route('auth.google') }}" class="text-center py-2.5 bg-indigo-600 text-white rounded-xl font-semibold hover:bg-indigo-700 transition">Daftar</a>
                    </div>
                @endauth
            </div>
        </div>
    </nav>

    @yield('content')

    <!-- Footer -->
    <footer class="bg-indigo-900 text-indigo-100 py-20 px-6 mt-20">
        <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-5 gap-12">
            <div class="space-y-4 col-span-2">
                <div class="flex items-center gap-2">
                    <img src="{{ asset('images/logo.png') }}" alt="AmikomEventHub Logo" class="w-10 h-10 rounded-full shadow-sm bg-white">
                    <span class="text-2xl font-bold text-white">AmikomEventHub</span>
                </div>
                <p class="max-w-xs text-indigo-300">Platform reservasi tiket event online terbaik untuk mahasiswa dan
                    penyelenggara profesional.</p>
            </div>
            <div>
                <h4 class="text-white font-bold mb-6">Kategori</h4>
                <ul class="space-y-4">
                    @foreach ($categories as $cat)
                    <li><a href="/?category={{ $cat->slug }}" class="hover:text-white transition">{{ $cat->name }}</a></li>
                    @endforeach
                </ul>
            </div>
            <div>
                <h4 class="text-white font-bold mb-6">Navigasi</h4>
                <ul class="space-y-4">
                    <li><a href="#" class="hover:text-white transition">Home</a></li>
                    <li><a href="#" class="hover:text-white transition">Semua Event</a></li>
                    <li><a href="#" class="hover:text-white transition">Semua Event</a></li>
                    <li><a href="#" class="hover:text-white transition">Cara Bayar</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-white font-bold mb-6">Hubungi Kami</h4>
                <ul class="space-y-4">
                    <li>support@eventtiket.com</li>
                    <li>+62 812 3456 7890</li>
                </ul>
            </div>
        </div>
        <div class="max-w-7xl mx-auto pt-12 mt-12 border-t border-indigo-800 text-center text-indigo-400 text-sm">
            &copy; 2024 AmikomEventHub. Masterfully crafted with passion by Rasty Sabrina (24.12.3386).
        </div>
    </footer>

    <script>
        document.getElementById('mobile-menu-btn').addEventListener('click', function() {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        });
    </script>

    <!-- PWA Service Worker -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js').then(reg => {
                    console.log('ServiceWorker registered!', reg.scope);
                }).catch(err => {
                    console.log('ServiceWorker registration failed: ', err);
                });
            });
        }
    </script>
</body>
</html>