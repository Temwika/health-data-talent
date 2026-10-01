@extends('layouts.admin')

@section('title', 'Two-factor code')
@section('handles-errors', '1')

@section('content')
<div class="auth-box">
    <h1>Enter your code</h1>
    <p>Open your authenticator app and enter the 6-digit code for HealthData Talent UK.</p>
    <form class="card" method="post" action="{{ route('admin.2fa.challenge') }}">
        @csrf
        <x-field name="code" label="6-digit code" required inputmode="numeric" autocomplete="one-time-code" maxlength="7" autofocus />
        <div class="form-actions">
            <button class="btn primary" type="submit">Verify</button>
        </div>
    </form>
    <p class="hint">Lost your device? An administrator can reset it with <code>php artisan hdt:reset-2fa your@email</code>.</p>
</div>
@endsection
