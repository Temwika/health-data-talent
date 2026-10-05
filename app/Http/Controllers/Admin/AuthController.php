<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    private const MAX_ATTEMPTS = 5;

    private const LOCKOUT_SECONDS = 900;

    public function showLogin()
    {
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Lock out per account + IP after repeated failures.
        $key = 'login:'.Str::lower($credentials['email']).'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($key, self::MAX_ATTEMPTS)) {
            $minutes = (int) ceil(RateLimiter::availableIn($key) / 60);
            throw ValidationException::withMessages([
                'email' => "Too many sign-in attempts. Try again in {$minutes} minute(s).",
            ]);
        }

        if (! Auth::attempt($credentials)) {
            RateLimiter::hit($key, self::LOCKOUT_SECONDS);
            AuditLog::record('login.failed', null, 'Failed sign-in for '.Str::limit($credentials['email'], 160, ''));

            throw ValidationException::withMessages([
                'email' => 'Those details don\'t match our records.',
            ]);
        }

        RateLimiter::clear($key);
        $request->session()->regenerate();

        $request->user()->forceFill(['last_login_at' => now()])->save();
        AuditLog::record('login', $request->user(), 'Signed in');

        return redirect()->intended(route('admin.dashboard'));
    }

    public function logout(Request $request)
    {
        AuditLog::record('logout', $request->user(), 'Signed out');

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
