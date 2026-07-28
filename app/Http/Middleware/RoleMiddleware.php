<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!auth()->check()) {
            return redirect()->route('admin.login');
        }

        $user = auth()->user();

        // Cek apakah role user ada dalam parameter yang diizinkan
        if (!in_array($user->role, $roles)) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki izin ke halaman ini.');
        }

        // Khusus Organizer: Wajib disetujui oleh Superadmin dulu
        if ($user->role === 'organizer' && $user->status !== 'approved') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('admin.login')->with('error', 'Akun Kepanitiaan/HIMA Anda belum disetujui atau telah ditolak oleh Superadmin.');
        }

        return $next($request);
    }
}