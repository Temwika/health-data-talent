@extends('layouts.site')

@section('title', 'Contact')

@section('content')
<div class="wrap page-head">
    <h1>Contact us</h1>
    <p class="lede">Questions about hiring, a role, or your data. We reply within one working day.</p>
</div>
<div class="wrap page-body">
    <div class="form-layout">
        <form class="card" method="post" action="{{ route('contact.store') }}">
            @csrf
            @if (session('status'))<p class="notice success" role="status">{{ session('status') }}</p>@endif
            @if ($errors->any())<p class="notice error" role="alert">Please check the highlighted fields below.</p>@endif

            <div class="row">
                <x-field name="name" label="Full name" required maxlength="100" autocomplete="name" />
                <x-field name="email" label="Email" type="email" required maxlength="160" autocomplete="email" />
                <x-field name="organisation" label="Organisation" hint="(optional)" maxlength="120" autocomplete="organization" />
                <x-field name="subject" label="Subject" required maxlength="120" />
            </div>
            <x-textarea name="message" label="Message" required maxlength="3000" rows="7" />

            <x-form-end submit="Send message" />
        </form>

        <aside class="aside">
            <h2 class="h3">Other ways to reach us</h2>
            <ul>
                <li>Email <a href="mailto:{{ config('hdt.contact_email') }}">{{ config('hdt.contact_email') }}</a></li>
                <li>Hiring? <a href="{{ route('employers.create') }}">Register as an employer</a></li>
                <li>Job seeking? <a href="{{ route('candidates.create') }}">Join the network</a></li>
                <li>Data request? See the <a href="{{ route('privacy') }}">privacy notice</a></li>
            </ul>
        </aside>
    </div>
</div>
@endsection
