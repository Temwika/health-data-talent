@extends('layouts.site')

@section('title', 'Join the talent network')

@php
    $currentTrack = old('track', $track);
@endphp

@section('content')
<div class="wrap page-head">
    <h1>Join the talent network</h1>
    <p class="lede">Free to join. We never charge candidates for finding them work, and we only share your CV with an employer after you agree.</p>
</div>
<div class="wrap page-body">
    <div class="form-layout">
        <form class="card" method="post" action="{{ route('candidates.store') }}" enctype="multipart/form-data" data-candidate-form>
            @csrf
            @if (session('status'))<p class="notice success" role="status">{{ session('status') }}</p>@endif
            @if ($errors->any())<p class="notice error" role="alert">Please check the highlighted fields below. If you attached a CV, you'll need to choose it again.</p>@endif

            @if ($vacancy)
                <p class="notice">You're registering your interest in <strong>{{ $vacancy->title }}</strong> at {{ $vacancy->organisation_name }}. We'll talk to you before your details go to the employer.</p>
                <input type="hidden" name="job" value="{{ $vacancy->slug }}">
            @elseif (old('job'))
                <input type="hidden" name="job" value="{{ old('job') }}">
            @endif

            <fieldset>
                <legend>I'm registering as</legend>
                <div class="chips">
                    <label class="chip"><input type="radio" name="track" value="data" @checked($currentTrack === 'data')><span>Health data, informatics or digital health professional</span></label>
                    <label class="chip"><input type="radio" name="track" value="doctor" @checked($currentTrack === 'doctor')><span>Doctor seeking remote NGO work</span></label>
                </div>
            </fieldset>

            <fieldset>
                <legend>About you</legend>
                <div class="row">
                    <x-field name="name" label="Full name" required maxlength="100" autocomplete="name" />
                    <x-field name="email" label="Email" type="email" required maxlength="160" autocomplete="email" />
                    <x-field name="phone" label="Phone" type="tel" maxlength="25" autocomplete="tel" />
                    <x-field name="location" label="Where you live" required maxlength="80" placeholder="Town or city, country" />
                </div>
            </fieldset>

            <fieldset>
                <legend>Your experience</legend>
                <div class="row">
                    <x-field name="current_title" label="Current job title" required maxlength="100" />
                    <x-select name="years" label="Years of experience" :options="config('hdt.years')" required />
                    <x-select name="qualification" label="Highest qualification" :options="config('hdt.qualifications')" />
                </div>

                <div data-track="doctor" @class(['hidden' => $currentTrack !== 'doctor'])>
                    <div class="row">
                        <x-field name="specialty" label="Specialty" :required="$currentTrack === 'doctor'" maxlength="100" placeholder="e.g. Public health, General practice" />
                        <x-field name="registration" label="Registration body and number" hint="(optional)" maxlength="80" placeholder="e.g. GMC 1234567" />
                    </div>
                    <x-chips name="ngo_work" label="Remote work you're interested in" :options="config('hdt.ngo_work')" />
                </div>

                <div data-track="data" @class(['hidden' => $currentTrack !== 'data'])>
                    <x-chips name="expertise" label="Areas of expertise" :options="config('hdt.expertise')" />
                    <x-chips name="tools" label="Tools and systems" :options="config('hdt.tools')" />
                </div>
            </fieldset>

            <fieldset>
                <legend>What you're looking for</legend>
                <div class="row">
                    <x-field name="desired_role" label="Role you want" required maxlength="100" :value="$vacancy?->title" />
                    <x-select name="pattern" label="Working pattern" :options="config('hdt.candidate_patterns')" placeholder="Any" />
                    <x-field name="salary" label="Salary expectation" maxlength="40" placeholder="e.g. £45,000" />
                    <x-select name="availability" label="Availability" :options="config('hdt.availability')" />
                    <x-select name="right_to_work" label="Right to work in the UK" :options="config('hdt.right_to_work')" />
                </div>
            </fieldset>

            <fieldset>
                <legend>Your CV</legend>
                <label class="drop" for="f-cv" data-drop>
                    <input id="f-cv" name="cv" type="file" accept=".pdf,.doc,.docx" @error('cv') aria-invalid="true" aria-describedby="f-cv-err" @enderror>
                    <strong data-drop-label>Choose a CV file</strong><br>
                    <span class="hint">PDF or Word, up to 5 MB. Please leave out your date of birth, photo and health details.</span>
                </label>
                <span class="err" id="f-cv-err" data-drop-error>@error('cv'){{ $message }}@enderror</span>
            </fieldset>

            <x-form-end submit="Join the network">
                <label class="check">
                    <input type="checkbox" name="marketing" value="1" @checked(old('marketing'))>
                    <span>Send me relevant job alerts by email. You can unsubscribe at any time.</span>
                </label>
            </x-form-end>
        </form>

        <aside class="aside">
            <h2 class="h3">Our promises to you</h2>
            <ul>
                <li>Joining is free, always.</li>
                <li>Your CV goes to an employer only after you say yes to that specific role.</li>
                <li>You can ask us to see, correct or delete your data at any time.</li>
                <li>We keep inactive profiles for no longer than {{ config('hdt.retention_months') }} months.</li>
            </ul>
        </aside>
    </div>
</div>
@endsection
