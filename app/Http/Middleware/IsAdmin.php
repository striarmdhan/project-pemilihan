<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth; // <--- 1. WAJIB TAMBAHKAN INI

class IsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        // Pastikan cek login dulu (Auth::check), baru cek role
        if (Auth::check() && Auth::user()->role === 'admin') {
            return $next($request);
        }

        // Kalau bukan admin, tendang balik
        return redirect('/dashboard')->with('error', 'Akses ditolak!');
    }
}
