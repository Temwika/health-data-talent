<!DOCTYPE html>
<html lang="en-GB">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="description" content="@yield('description', 'HealthData Talent UK: specialist recruitment for health data, health informatics and digital health professionals. We also connect doctors with NGOs offering remote contracts.')">
    <title>@yield('title', 'Specialist health data recruitment') — HealthData Talent UK</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500;12..96,700;12..96,800&family=Public+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/site.css') }}">
    <meta property="og:image" content="{{ asset('img/logo-full.png') }}">
    <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png">
    <script src="{{ asset('js/site.js') }}" defer></script>
</head>
<body>
<a class="skip" href="#main">Skip to content</a>

<header class="site">
    <div class="wrap bar">
        <a class="logo" href="{{ route('home') }}" aria-label="HealthData Talent UK home">
            <img src="{{ asset('img/logo-mark.png') }}" width="76" height="76" alt="">
            <span><b>HealthData</b><b>Talent UK</b></span>
        </a>
        <button class="menu-btn" type="button" aria-expanded="false" aria-controls="nav" data-menu>Menu</button>
        <nav class="main" id="nav" aria-label="Main">
            <ul>
                @foreach ([
                    'employers.create' => ['Employers', 'employers*'],
                    'candidates.create' => ['Candidates', 'candidates*'],
                    'doctors' => ['Doctors & NGOs', 'doctors'],
                    'jobs.index' => ['Jobs', 'jobs*'],
                    'insights.index' => ['Insights', 'insights*'],
                    'about' => ['About', 'about'],
                    'contact.create' => ['Contact', 'contact'],
                ] as $route => [$text, $pattern])
                    <li><a href="{{ route($route) }}" @if (request()->is($pattern)) aria-current="page" @endif>{{ $text }}</a></li>
                @endforeach
            </ul>
        </nav>
    </div>
</header>

<main id="main">
    @yield('content')
</main>

<footer class="site-foot">
    <div class="wrap">
        <div class="foot-brand">
            <a class="foot-logo" href="{{ route('home') }}" aria-label="HealthData Talent UK home">
                <img src="{{ asset('img/logo-mark.png') }}" width="64" height="64" alt="">
                <span><b>HealthData</b><b>Talent UK</b></span>
            </a>
            <p class="foot-tag">Connecting health data, informatics and digital-health talent.</p>
            <div class="foot-cta">
                <a class="btn accent small" href="{{ route('employers.create') }}">Hire talent</a>
                <a class="btn ghost small" href="{{ route('candidates.create') }}">Join the network</a>
            </div>
        </div>
        <div class="foot">
            <div>
                <h2 class="foot-h">Find us</h2>
                <address>
                    {{ config('hdt.company') }}<br>
                    {!! implode('<br>', array_map('e', config('hdt.address'))) !!}
                </address>
                <p><a href="mailto:{{ config('hdt.contact_email') }}">{{ config('hdt.contact_email') }}</a></p>
            </div>
            <div>
                <h2 class="foot-h">Employers</h2>
                <ul>
                    <li><a href="{{ route('employers.create') }}">Hire talent</a></li>
                    <li><a href="{{ route('vacancies.create') }}">Submit a vacancy</a></li>
                    <li><a href="{{ route('employers.create', ['service' => 'ngo-doctor']) }}">Find a doctor for your NGO</a></li>
                </ul>
            </div>
            <div>
                <h2 class="foot-h">Candidates</h2>
                <ul>
                    <li><a href="{{ route('candidates.create') }}">Join the network</a></li>
                    <li><a href="{{ route('jobs.index') }}">Jobs</a></li>
                    <li><a href="{{ route('doctors') }}">Doctors &amp; NGOs</a></li>
                </ul>
            </div>
            <div>
                <h2 class="foot-h">Company</h2>
                <ul>
                    <li><a href="{{ route('about') }}">About</a></li>
                    <li><a href="{{ route('insights.index') }}">Insights</a></li>
                    <li><a href="{{ route('contact.create') }}">Contact</a></li>
                    <li><a href="{{ route('privacy') }}">Privacy notice</a></li>
                    <li><a href="{{ route('terms') }}">Terms</a></li>
                </ul>
            </div>
        </div>
        <p class="legalline">
            {{ config('hdt.company') }} is registered in England and Wales, company number {{ config('hdt.company_number') }}.
            Registered office: {{ implode(', ', config('hdt.address')) }}. ICO registration <span class="ph">[number]</span>.
            We do not charge candidates for work-finding services. © {{ date('Y') }}
        </p>
    </div>
</footer>
</body>
</html>
