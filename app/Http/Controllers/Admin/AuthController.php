<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // 1. Tampilkan Formulir Login
    public function showLogin() {
        return view('admin.auth.login');
    }

    // 2. Memproses Submit Login
    public function login(Request $request) {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Cek Status Approval khusus untuk Organizer
            if ($user->role === 'organizer' && $user->status !== 'approved') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'email' => 'Akun Kepanitiaan/HIMA Anda belum disetujui atau ditolak oleh Superadmin.',
                ]);
            }

            // Regenerasi Session demi Keamanan
            $request->session()->regenerate();

            // REDIRECT BERDASARKAN ROLE
            if ($user->role === 'superadmin') {
                return redirect()->route('admin.superadmin.dashboard');
            }

            if ($user->role === 'organizer') {
                return redirect()->route('admin.dashboard');
            }

            // Fallback jika ada role lain (misal user umum / pembeli)
            return redirect()->route('home');
        }

        return back()->withErrors([
            'email' => 'Email atau Password yang Anda berikan tidak terdaftar di database kami.',
        ]);
    }

    // 3. Memproses Log Out
    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }
}