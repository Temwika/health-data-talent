@extends('layouts.admin')

@section('title', 'Set up two-factor')
@section('handles-errors', '1')

@section('content')
<div class="auth-box">
    <h1>Set up two-factor authentication</h1>
    <p>Staff accounts can see candidates' personal data, so a second step is required at every sign-in.</p>
    <ol class="plain-steps">
        <li>Open an authenticator app (Google Authenticator, Microsoft Authenticator, Authy or 1Password).</li>
        <li>Add an account and choose <strong>enter a setup key</strong>. Use type “time based”.</li>
        <li>Enter this key:
            <p class="secret"><code>{{ trim(chunk_split($secret, 4, ' ')) }}</code></p>
            <p class="hint">On a phone you can <a href="{{ $uri }}">open it in your authenticator app</a> instead.</p>
        </li>
        <li>Enter the 6-digit code the app shows.</li>
    </ol>
    <form class="card" method="post" action="{{ route('admin.2fa.setup') }}">
        @csrf
        <x-field name="code" label="6-digit code" required inputmode="numeric" autocomplete="one-time-code" maxlength="7" />
        <div class="form-actions">
            <button class="btn primary" type="submit">Turn on two-factor</button>
        </div>
    </form>
</div>
@endsection
