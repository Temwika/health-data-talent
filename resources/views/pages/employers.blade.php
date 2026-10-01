@extends('layouts.site')

@section('title', 'Hire talent')

@section('content')
<div class="wrap page-head">
    <h1>Hire health data talent</h1>
    <p class="lede">Register your organisation, or send us a vacancy directly.</p>
</div>
<div class="wrap page-body">
    <div class="tabs">
        <a class="tab" href="{{ route('employers.create') }}" aria-current="page">Register your organisation</a>
        <a class="tab" href="{{ route('vacancies.create') }}">Submit a vacancy</a>
    </div>
    <div class="form-layout">
        <form class="card" method="post" action="{{ route('employers.store') }}">
            @csrf
            @if (session('status'))<p class="notice success" role="status">{{ session('status') }}</p>@endif
            @if ($errors->any())<p class="notice error" role="alert">Please check the highlighted fields below.</p>@endif

            <fieldset>
                <legend>Your organisation</legend>
                <div class="row">
                    <x-field name="name" label="Organisation name" required maxlength="120" autocomplete="organization" />
                    <x-select name="sector" label="Sector" :options="config('hdt.sectors')" placeholder="Choose a sector" required />
                </div>
            </fieldset>
            <fieldset>
                <legend>Your details</legend>
                <div class="row">
                    <x-field name="contact_name" label="Full name" required maxlength="100" autocomplete="name" />
                    <x-field name="contact_title" label="Job title" maxlength="100" autocomplete="organization-title" />
                    <x-field name="email" label="Work email" type="email" required maxlength="160" autocomplete="email" />
                    <x-field name="phone" label="Phone" type="tel" maxlength="25" autocomplete="tel" />
                </div>
            </fieldset>
            <fieldset>
                <legend>What do you need?</legend>
                <x-select name="service" label="Service" :options="config('hdt.services')" :value="$presetService" placeholder="Choose a service" required />
                <x-textarea name="message" label="Tell us about the role or need" hint="(optional)" maxlength="2000" />
            </fieldset>

            <x-form-end submit="Send registration" />
        </form>

        <aside class="aside">
            <h2 class="h3">What happens next</h2>
            <ul>
                <li>We reply within one working day.</li>
                <li>A short call to understand the role.</li>
                <li>We agree terms in writing before any search begins.</li>
                <li>Vacancies are reviewed before they appear on our jobs board.</li>
            </ul>
        </aside>
    </div>
</div>

<section class="tint">
    <div class="wrap">
        <h2>Services</h2>
        <div class="services">
            <div class="svc"><h3>Specialist recruitment</h3><p>End-to-end search, screening and shortlisting for permanent roles. You pay only when you hire.</p><span class="price">Fee agreed per role</span></div>
            <div class="svc"><h3>Shortlist sourcing</h3><p>For teams running their own process: we deliver a screened shortlist of qualified people.</p><span class="price">From £750</span></div>
            <div class="svc"><h3>Talent mapping</h3><p>Research on where specific skills sit in the market, for workforce planning or a hard-to-fill team.</p><span class="price">From £1,000</span></div>
            <div class="svc"><h3>Doctors for NGOs</h3><p>Credential-checked doctors for remote advisory, guideline, telemedicine, research and training contracts.</p><span class="price">Fee agreed per brief</span></div>
        </div>
    </div>
</section>
@endsection
