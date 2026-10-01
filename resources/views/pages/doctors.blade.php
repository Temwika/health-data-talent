@extends('layouts.site')

@section('title', 'Doctors & NGOs')

@section('content')
<section class="band band-top">
    <div class="wrap band-grid">
        <div>
            <h1>Remote work for doctors with NGOs and global health organisations</h1>
            <p class="lede">We connect doctors with NGOs offering work-from-home contracts, so clinical expertise can reach programmes that need it, wherever you live.</p>
            <div class="actions">
                <a class="btn accent" href="{{ route('candidates.create', ['track' => 'doctor']) }}">Register as a doctor</a>
                <a class="btn ghost" href="{{ route('employers.create', ['service' => 'ngo-doctor']) }}">I'm an NGO</a>
            </div>
        </div>
        <div>
            <p class="list-title">The kind of work involved</p>
            <ul>
                <li>Clinical advisory and technical review</li>
                <li>Guideline and protocol development</li>
                <li>Telemedicine and remote case review</li>
                <li>Research, evaluation and health data review</li>
                <li>Training health workers online</li>
            </ul>
        </div>
    </div>
</section>

<section>
    <div class="wrap">
        <h2>How it works</h2>
        <ol class="steps">
            <li><h3>Register</h3><p>Tell us your specialty, registration and the remote work you'd like.</p></li>
            <li><h3>We check credentials</h3><p>We verify registration and references before any introduction.</p></li>
            <li><h3>We match you</h3><p>When an NGO brief fits, we ask whether you'd like to be put forward.</p></li>
            <li><h3>The NGO engages you</h3><p>Contracts are agreed directly between you and the NGO.</p></li>
        </ol>
        <p class="notice spaced">Clinical work that involves treating patients may require registration in the patient's country. NGOs remain responsible for checking that each contract meets local licensing rules.</p>
    </div>
</section>

<section class="tint">
    <div class="wrap doors">
        <div class="door">
            <p class="eyebrow">For doctors</p>
            <h2 class="h3">Put your clinical experience to work remotely</h2>
            <p>Registration is free. We contact you only when a brief fits your specialty and availability.</p>
            <a class="btn primary" href="{{ route('candidates.create', ['track' => 'doctor']) }}">Register as a doctor</a>
        </div>
        <div class="door alt">
            <p class="eyebrow">For NGOs</p>
            <h2 class="h3">Need clinical expertise for a programme?</h2>
            <p>Tell us the brief. We'll introduce credential-checked doctors who have agreed to be put forward.</p>
            <a class="btn primary" href="{{ route('employers.create', ['service' => 'ngo-doctor']) }}">Tell us what you need</a>
        </div>
    </div>
</section>
@endsection
