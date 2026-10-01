@extends('layouts.site')

@section('title', 'Submit a vacancy')

@section('content')
<div class="wrap page-head">
    <h1>Submit a vacancy</h1>
    <p class="lede">Send us the role. We review every vacancy and agree terms with you before it is published.</p>
</div>
<div class="wrap page-body">
    <div class="tabs">
        <a class="tab" href="{{ route('employers.create') }}">Register your organisation</a>
        <a class="tab" href="{{ route('vacancies.create') }}" aria-current="page">Submit a vacancy</a>
    </div>
    <div class="form-layout">
        <form class="card" method="post" action="{{ route('vacancies.store') }}">
            @csrf
            @if (session('status'))<p class="notice success" role="status">{{ session('status') }}</p>@endif
            @if ($errors->any())<p class="notice error" role="alert">Please check the highlighted fields below.</p>@endif

            <fieldset>
                <legend>The role</legend>
                <div class="row">
                    <x-field name="title" label="Job title" required maxlength="120" />
                    <x-field name="organisation_name" label="Organisation" required maxlength="120" autocomplete="organization" />
                    <x-field name="location" label="Location" required maxlength="80" placeholder="e.g. Leeds" />
                    <x-select name="pattern" label="Working pattern" :options="config('hdt.patterns')" required />
                    <x-field name="salary" label="Salary range" required maxlength="60" placeholder="e.g. £40,000–£48,000" />
                    <x-select name="contract_type" label="Contract type" :options="config('hdt.contract_types')" value="Permanent" :placeholder="false" required />
                </div>
                <x-field name="skills" label="Essential skills" hint="Separate with commas" required maxlength="300" placeholder="SQL, Power BI, NHS datasets" />
                <x-textarea name="description" label="Description" required maxlength="4000" rows="8" />
            </fieldset>
            <fieldset>
                <legend>Your contact details</legend>
                <div class="row">
                    <x-field name="contact_name" label="Full name" required maxlength="100" autocomplete="name" />
                    <x-field name="contact_email" label="Work email" type="email" required maxlength="160" autocomplete="email" />
                </div>
            </fieldset>

            <x-form-end submit="Submit vacancy" terms />
        </form>

        <aside class="aside">
            <h2 class="h3">Before it goes live</h2>
            <ul>
                <li>We check the details and confirm the role with you.</li>
                <li>We agree terms of business in writing.</li>
                <li>Only then does the vacancy appear on the jobs board.</li>
                <li>You carry out your own right-to-work checks on appointment.</li>
            </ul>
        </aside>
    </div>
</div>
@endsection
