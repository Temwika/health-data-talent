@extends('layouts.admin')

@section('title', 'Sign in')
@section('handles-errors', '1')

@section('content')
<div class="auth-box">
    <h1>Staff sign in</h1>
    <form class="card" method="post" action="{{ route('admin.login') }}">
        @csrf
        <x-field name="email" label="Email" type="email" required autocomplete="username" autofocus />
        <x-field name="password" label="Password" type="password" required autocomplete="current-password" />
        <div class="form-actions">
            <button class="btn primary" type="submit">Sign in</button>
        </div>
    </form>
    <p class="hint">This area is for HealthData Talent staff. Sign-ins are logged.</p>
</div>
@endsection
