<?php

namespace App\Http\Controllers;

use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Exception;

class SocialiteController extends Controller
{
    /**
     * Mengarahkan pengguna ke halaman autentikasi Google.
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Menerima callback dari Google setelah autentikasi.
     */
    public function handleGoogleCallback()
    {
        try {
            // Mengambil data user dari Google
            // Catatan: Jika mengalami masalah session/state mismatch saat uji coba, gunakan ->stateless()->user()
            $googleUser = Socialite::driver('google')->user();

            // 1. Cari user berdasarkan google_id terlebih dahulu
            $user = User::where('google_id', $googleUser->getId())->first();

            if ($user) {
                // Jika user sudah pernah login via Google sebelumnya
                Auth::login($user);
            } else {
                // 2. Jika belum ada google_id, cek apakah email sudah terdaftar secara manual
                $existingUser = User::where('email', $googleUser->getEmail())->first();

                if ($existingUser) {
                    // Hubungkan akun yang ada dengan google_id
                    $existingUser->update([
                        'google_id' => $googleUser->getId(),
                    ]);
                    
                    $user = $existingUser;
                    Auth::login($user);
                } else {
                    // 3. Jika benar-benar akun baru, buat user baru dengan default role 'user'
                    $newUser = User::create([
                        'name'      => $googleUser->getName(),
                        'email'     => $googleUser->getEmail(),
                        'google_id' => $googleUser->getId(),
                        'password'  => bcrypt(Str::random(16)), // Password acak agar tidak null
                        'role'      => 'user',                  // Default role registrasi baru
                    ]);

                    $user = $newUser;
                    Auth::login($user);
                }
            }

            // --- REDIRECT CERDAS BERDASARKAN ROLE ---
            $userRole = strtolower(trim((string) $user->role));
            if (in_array($userRole, ['admin', 'organizer', 'superadmin'], true)) {
                return redirect()->intended('/admin/dashboard');
            }

            // Jika user biasa / pembeli tiket
            return redirect()->intended('/');

        } catch (Exception $e) {
            return redirect('/login')->with('error', 'Gagal login menggunakan Google: ' . $e->getMessage());
        }
    }
}