@extends('layouts.site')

@section('title', 'About')

@section('content')
<div class="wrap page-head">
    <h1>A recruiter that speaks both healthcare and data</h1>
    <p class="lede">HealthData Talent UK connects health-data, health-informatics and digital-health professionals with the organisations that need them.</p>
</div>

<section>
    <div class="wrap split">
        <div class="prose">
            <h2>Why we exist</h2>
            <p>A good health data analyst knows SQL. A great one also knows why an admission date can be later than a discharge date, what a clinical coder does, and which question the service manager is really asking. Those people are hard to find, and generalist recruiters rarely know how to tell them apart.</p>
            <p>We work only in this space. That means our conversations with employers start from the systems, datasets and clinical context of the job, and our conversations with candidates start from the work they actually want to do next.</p>
            <h2>What we cover</h2>
            <p>Permanent roles across health data and analytics, health and clinical informatics, electronic patient record programmes, healthcare business intelligence, data science and engineering, and digital transformation. We also introduce doctors to NGOs and international health organisations for remote, contract-based work.</p>
            <h2>How we work</h2>
            <p>We act as an employment agency. We do not charge candidates. We introduce people to employers only with their agreement to that specific role, and we agree terms with employers in writing before any search begins.</p>
        </div>
        <aside class="aside static">
            <h2 class="h3">At a glance</h2>
            <ul>
                <li>Health data, informatics and digital health only</li>
                <li>NHS, health-tech, research, public health and NGOs</li>
                <li>Permanent recruitment, shortlist sourcing and talent mapping</li>
                <li>Remote NGO work for doctors</li>
                <li>Free for candidates</li>
            </ul>
        </aside>
    </div>
</section>

<section class="tint">
    <div class="wrap doors">
        <div class="door">
            <h2 class="h3">Hiring?</h2>
            <p>Tell us about the role and we'll reply within one working day.</p>
            <a class="btn primary" href="{{ route('employers.create') }}">Register as an employer</a>
        </div>
        <div class="door alt">
            <h2 class="h3">Looking for your next role?</h2>
            <p>Join the network and hear about roles that fit your experience.</p>
            <a class="btn primary" href="{{ route('candidates.create') }}">Join the talent network</a>
        </div>
    </div>
</section>
@endsection
