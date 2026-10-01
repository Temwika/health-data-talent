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
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <script src="{{ asset('js/site.js') }}" defer></script>
</head>
<body>
<a class="skip" href="#main">Skip to content</a>

<header class="site">
    <div class="wrap bar">
        <a class="logo" href="{{ route('home') }}" aria-label="HealthData Talent UK home">
            <svg width="36" height="36" viewBox="0 0 36 36" aria-hidden="true">
                <rect class="logo-tile" width="36" height="36" rx="9"/>
                <path class="logo-trace" d="M5 20h6l2.5-6 4 12 3-9 2 3h8.5"/>
                <circle class="logo-dot" cx="30.5" cy="20" r="2.4"/>
            </svg>
            <span><b>HealthData Talent</b><small>UK Limited</small></span>
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
        <div class="foot">
            <div>
                <h2 class="foot-h">{{ config('hdt.company') }}</h2>
                <p class="foot-about">Specialist recruitment for health data, health informatics and digital health, and remote work for doctors with NGOs.</p>
                <p><a href="mailto:{{ config('hdt.contact_email') }}">{{ config('hdt.contact_email') }}</a></p>
            </div>
            <div>
                <h2 class="foot-h">Get started</h2>
                <ul>
                    <li><a href="{{ route('employers.create') }}">Hire talent</a></li>
                    <li><a href="{{ route('vacancies.create') }}">Submit a vacancy</a></li>
                    <li><a href="{{ route('candidates.create') }}">Join the network</a></li>
                    <li><a href="{{ route('jobs.index') }}">Jobs</a></li>
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
            {{ config('hdt.company') }} is registered in England and Wales, company number <span class="ph">[number]</span>.
            Registered office: <span class="ph">[address]</span>. ICO registration <span class="ph">[number]</span>.
            We do not charge candidates for work-finding services. © {{ date('Y') }}
        </p>
    </div>
</footer>
</body>
</html>
