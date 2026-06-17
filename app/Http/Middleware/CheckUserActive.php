<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckUserActive
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Khusus pengecekan untuk role 'siswa'
            if ($user->hasRole('siswa')) {
                $student = $user->student;

                // Jika data relasi student tidak ada, ATAU is_active bernilai 0
                if (!$student || $student->is_active == 0) {

                    // 1. Logout Paksa
                    Auth::logout();

                    // 2. Hancurkan Session
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();

                    // 3. Tendang ke halaman Login
                    return redirect('/login')->with('error', 'Sesi berakhir! Akun kamu telah dinonaktifkan oleh Admin.');
                }
            }
        }

        return $next($request);
    }
}
