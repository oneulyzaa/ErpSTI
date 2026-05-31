<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Contoh pemakaian di routes/web.php:
     *   Route::middleware('role:admin')->group(...)
     *   Route::middleware('role:admin,finance')->group(...)
     *
     * @param  string  ...$roles  Satu atau lebih role yang diizinkan
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Pastikan user sudah login
        if (! auth()->check()) {
            return redirect()->route('login');
        }

        $userRole = auth()->user()->role;

        // Cek apakah role user ada di daftar yang diizinkan
        if (! in_array($userRole, $roles)) {
            // Jika request AJAX / API → kembalikan JSON 403
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Akses ditolak. Anda tidak memiliki izin untuk halaman ini.',
                ], 403);
            }

            // Jika request biasa → redirect ke dashboard dengan pesan error
            return redirect()
                ->route('admin.dashboard')
                ->with('error', 'Akses ditolak. Anda tidak memiliki izin untuk halaman tersebut.');
        }

        return $next($request);
    }
}