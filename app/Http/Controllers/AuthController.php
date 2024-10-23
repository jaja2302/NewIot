<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pengguna;
use Illuminate\Support\Facades\Cache;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // dd('test');
        $credentials = $request->validate([
            'email' => ['required'],
            'password' => ['required'],
        ]);

        $pengguna = Pengguna::where('email', $credentials['email'])
            ->where('password', $credentials['password'])
            ->first();

        // dd($pengguna);
        if ($pengguna) {
            Auth::login($pengguna);
            $request->session()->regenerate();
            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout(Request $request)
    {
        // Clear the user-specific cache
        Cache::forget('user_location');
        Cache::forget('weather_data_' . Auth::id());

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
