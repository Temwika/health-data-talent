<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Support\Totp;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TwoFactorController extends Controller
{
    public function challenge(Request $request)
    {
        if (! $request->user()->hasTwoFactor()) {
            return redirect()->route('admin.2fa.setup');
        }

        return view('admin.auth.two-factor');
    }

    public function verify(Request $request)
    {
        $request->validate(['code' => ['required', 'string', 'max:10']]);
        $user = $request->user();

        if (! $user->hasTwoFactor() || ! Totp::verify($user->two_factor_secret, $request->input('code'))) {
            AuditLog::record('2fa.failed', $user, 'Incorrect two-factor code');
            throw ValidationException::withMessages(['code' => 'That code isn\'t right. Check your authenticator app and try again.']);
        }

        $request->session()->regenerate();
        $request->session()->put('two_factor_passed', $user->id);

        return redirect()->intended(route('admin.dashboard'));
    }

    public function setup(Request $request)
    {
        $user = $request->user();
        if ($user->hasTwoFactor()) {
            return redirect()->route('admin.dashboard');
        }

        // Keep one pending secret until it is confirmed, so a page refresh doesn't change it.
        if (! $user->two_factor_secret) {
            $user->forceFill(['two_factor_secret' => Totp::generateSecret(10)])->save();
        }

        return view('admin.auth.two-factor-setup', [
            'secret' => $user->two_factor_secret,
            'uri' => Totp::uri($user->two_factor_secret, $user->email, 'HealthData Talent UK'),
        ]);
    }

    public function confirm(Request $request)
    {
        $request->validate(['code' => ['required', 'string', 'max:10']]);
        $user = $request->user();

        if ($user->hasTwoFactor()) {
            return redirect()->route('admin.dashboard');
        }

        if (! $user->two_factor_secret || ! Totp::verify($user->two_factor_secret, $request->input('code'))) {
            throw ValidationException::withMessages(['code' => 'That code isn\'t right. Make sure the key was entered exactly and try the newest code.']);
        }

        $user->forceFill(['two_factor_confirmed_at' => now()])->save();
        $request->session()->regenerate();
        $request->session()->put('two_factor_passed', $user->id);
        AuditLog::record('2fa.enabled', $user, 'Two-factor authentication enabled');

        return redirect()->route('admin.dashboard')->with('status', 'Two-factor authentication is on.');
    }
}
