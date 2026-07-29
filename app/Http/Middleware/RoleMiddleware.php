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

        if ($request->is('admin/*') && !$request->secure() && app()->environment('production')) {
            return redirect()->secure($request->getRequestUri());
        }

        $user = auth()->user();
        $userRole = strtolower(trim((string) $user->role));
        $allowedRoles = array_map(fn ($role) => strtolower(trim((string) $role)), $roles);

        // Cek apakah role user ada dalam parameter yang diizinkan
        if (!in_array($userRole, $allowedRoles, true)) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki izin ke halaman ini.');
        }

        // Khusus Organizer: Wajib disetujui oleh Superadmin dulu
        if ($userRole === 'organizer' && ($user->status ?? 'pending') !== 'approved') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('admin.login')->with('error', 'Akun Kepanitiaan/HIMA Anda belum disetujui atau telah ditolak oleh Superadmin.');
        }

        return $next($request);
    }
}