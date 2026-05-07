<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Tampilkan form login (GET) atau proses login (POST).
     */
    public function login(Request $request)
    {
        // Jika sudah login, redirect ke dashboard
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }

        // GET: tampilkan halaman login
        if ($request->isMethod('get')) {
            return view('auth.login');
        }

        // POST: validasi input
        $credentials = $request->validate([
            'email'    => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:6'],
        ], [
            'email.required'    => 'Email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
            'password.min'      => 'Password minimal 6 karakter.',
        ]);

        $remember = $request->boolean('remember');

        // Coba autentikasi
        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            return redirect()
                ->intended(route('admin.dashboard'))
                ->with('success', 'Selamat datang, ' . Auth::user()->name . '!');
        }

        // Gagal login
        return back()
            ->withInput($request->only('email', 'remember'))
            ->with('error', 'Email atau password yang Anda masukkan salah.');
    }

    /**
     * Logout: hapus sesi dan redirect ke halaman login.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('success', 'Anda berhasil keluar dari sistem.');
    }
}
