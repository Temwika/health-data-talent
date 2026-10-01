<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTwoFactor
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user->hasTwoFactor()) {
            return config('hdt.require_2fa') ? redirect()->route('admin.2fa.setup') : $next($request);
        }

        if ($request->session()->get('two_factor_passed') !== $user->id) {
            return redirect()->route('admin.2fa.challenge');
        }

        return $next($request);
    }
}
