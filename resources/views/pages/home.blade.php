@extends('layouts.site')

@section('content')
<section class="hero">
    <div class="wrap">
        <p class="eyebrow">Health data · Informatics · Digital health</p>
        <h1>Specialist recruitment for the people who make health data useful.</h1>
        <p class="lede">We find people who understand both the patient record and the query that reads it, and introduce them to NHS organisations, health-tech companies, researchers and NGOs.</p>
        <div class="actions">
            <a class="btn accent" href="{{ route('employers.create') }}">Hire talent</a>
            <a class="btn ghost" href="{{ route('candidates.create') }}">Join the talent network</a>
        </div>
    </div>
    <div class="trace" aria-hidden="true">
        <svg viewBox="0 0 1200 220" preserveAspectRatio="xMidYMid meet">
            <g class="grid">
                <line x1="620" y1="40" x2="1200" y2="40"/><line x1="620" y1="80" x2="1200" y2="80"/>
                <line x1="620" y1="120" x2="1200" y2="120"/><line x1="620" y1="160" x2="1200" y2="160"/>
                <line x1="620" y1="20" x2="620" y2="200"/>
            </g>
            <path class="line" d="M0 140 L75 140 L85 120 L95 140 L110 140 L120 40 L132 190 L142 140 L170 140 L185 125 L200 140 L275 140 L285 120 L295 140 L310 140 L320 40 L332 190 L342 140 L370 140 L385 125 L400 140 L475 140 L485 120 L495 140 L510 140 L520 50 L532 180 L542 140 L620 140 L680 122 L740 128 L800 102 L860 108 L920 80 L980 86 L1040 60 L1100 64 L1160 38"/>
            <g class="pts">
                <circle class="pt" cx="680" cy="122" r="5"/>
                <circle class="pt" cx="800" cy="102" r="5"/>
                <circle class="pt" cx="920" cy="80" r="5"/>
                <circle class="pt" cx="1040" cy="60" r="5"/>
                <circle class="pt" cx="1160" cy="38" r="6"/>
            </g>
            <text x="20" y="208">At the bedside</text>
            <text x="1180" y="208" text-anchor="end">On the dashboard</text>
        </svg>
    </div>
</section>

<section>
    <div class="wrap doors">
        <div class="door">
            <p class="eyebrow">For employers</p>
            <h2 class="h3">Hiring for a hard-to-fill role?</h2>
            <p>Tell us about it. We'll come back within one working day with how we'd approach the search.</p>
            <a class="btn primary" href="{{ route('employers.create') }}">Register as an employer</a>
            <a class="textlink" href="{{ route('vacancies.create') }}">or submit a vacancy</a>
        </div>
        <div class="door alt">
            <p class="eyebrow">For candidates</p>
            <h2 class="h3">Working in health data or informatics?</h2>
            <p>Join the talent network. It is free, and we never charge candidates for finding them work.</p>
            <a class="btn primary" href="{{ route('candidates.create') }}">Join the talent network</a>
            <a class="textlink" href="{{ route('jobs.index') }}">or browse current jobs</a>
        </div>
    </div>
</section>

<section class="tint">
    <div class="wrap">
        <h2>Who we place</h2>
        <p class="lede">Roles that sit where healthcare, data and technology meet. Generalist agencies tend to miss them; it is all we do.</p>
        <div class="cols">
            <div>
                <h3>Health data</h3>
                <ul>
                    <li>Health data analysts</li><li>Clinical data analysts</li><li>Data quality analysts</li>
                    <li>Population health analysts</li><li>Research data analysts</li>
                </ul>
            </div>
            <div>
                <h3>Health informatics</h3>
                <ul>
                    <li>Health informatics analysts</li><li>Clinical informatics specialists</li><li>EPR and EHR specialists</li>
                    <li>Clinical systems analysts</li><li>Information managers</li>
                </ul>
            </div>
            <div>
                <h3>Digital health</h3>
                <ul>
                    <li>Digital transformation specialists</li><li>Healthcare BI developers</li><li>Data scientists and engineers</li>
                    <li>Power BI and SQL specialists</li><li>Digital health product roles</li>
                </ul>
            </div>
        </div>
    </div>
</section>

@if ($featured->isNotEmpty())
<section>
    <div class="wrap">
        <div class="section-head">
            <h2>Featured opportunities</h2>
            <a class="textlink" href="{{ route('jobs.index') }}">See all jobs</a>
        </div>
        <ul class="jobs">
            @foreach ($featured as $job)
                @include('partials.job-row', ['job' => $job])
            @endforeach
        </ul>
    </div>
</section>
@endif

<section class="tint">
    <div class="wrap">
        <h2>How hiring with us works</h2>
        <ol class="steps">
            <li><h3>Understand the role</h3><p>We agree the skills, systems and healthcare context the job really needs.</p></li>
            <li><h3>Search the market</h3><p>We search our specialist network and approach people who aren't actively looking.</p></li>
            <li><h3>Screen properly</h3><p>Every candidate is interviewed and their technical and healthcare experience checked.</p></li>
            <li><h3>Send a focused shortlist</h3><p>Usually three to five people, each introduced only with their consent.</p></li>
            <li><h3>Support interviews</h3><p>We arrange interviews and pass on feedback both ways.</p></li>
            <li><h3>Make the appointment</h3><p>We help with the offer and stay in touch after the start date.</p></li>
        </ol>
    </div>
</section>

<section>
    <div class="wrap">
        <h2>Services for employers</h2>
        <p class="lede">Our rates depend on the role and the service, so we quote once we understand what you need. Nothing is charged until terms are agreed in writing.</p>
        <div class="services">
            <div class="svc"><h3>Specialist recruitment</h3><p>End-to-end search, screening and shortlisting for permanent roles. You pay only when you hire.</p><span class="price">Rates on request</span></div>
            <div class="svc"><h3>Shortlist sourcing</h3><p>For teams running their own process: we deliver a screened shortlist of qualified people.</p><span class="price">Rates on request</span></div>
            <div class="svc"><h3>Talent mapping</h3><p>Research on where specific skills sit in the market, for workforce planning or a hard-to-fill team.</p><span class="price">Rates on request</span></div>
        </div>
    </div>
</section>

<section class="band">
    <div class="wrap band-grid">
        <div>
            <h2>Doctors for remote NGO work</h2>
            <p class="lede">We also connect doctors with NGOs and international health organisations offering work-from-home contracts.</p>
            <div class="actions">
                <a class="btn accent" href="{{ route('candidates.create', ['track' => 'doctor']) }}">Register as a doctor</a>
                <a class="btn ghost" href="{{ route('employers.create', ['service' => 'ngo-doctor']) }}">Find a doctor for your NGO</a>
            </div>
        </div>
        <div>
            <p class="list-title">Typical remote work includes</p>
            <ul>
                <li>Clinical advisory and technical review</li>
                <li>Guideline and protocol development</li>
                <li>Telemedicine and case review</li>
                <li>Research, evaluation and data review</li>
                <li>Training and programme support</li>
            </ul>
        </div>
    </div>
</section>

<section>
    <div class="wrap">
        <h2>How we look after your information</h2>
        <p class="lede">CVs and career histories are personal. We treat them that way.</p>
        <div class="trust">
            <div><h3>Consent first</h3><p>Your CV goes to an employer only after you say yes to that specific role.</p></div>
            <div><h3>Private by design</h3><p>CVs are held in private storage, never at a public web address, and every download is logged.</p></div>
            <div><h3>Kept only as long as needed</h3><p>Inactive profiles are deleted after {{ config('hdt.retention_months') }} months. Ask us to delete yours at any time.</p></div>
            <div><h3>Free for candidates</h3><p>We never charge candidates for finding them work. Employers pay our fees.</p></div>
        </div>
    </div>
</section>

@if ($posts->isNotEmpty())
<section class="tint">
    <div class="wrap">
        <div class="section-head">
            <h2>Insights</h2>
            <a class="textlink" href="{{ route('insights.index') }}">All articles</a>
        </div>
        <div class="posts">
            @foreach ($posts as $post)
                @include('insights.card', ['post' => $post])
            @endforeach
        </div>
    </div>
</section>
@endif
@endsection
