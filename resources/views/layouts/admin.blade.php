<!DOCTYPE html>
<html lang="en-GB">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'Dashboard') — HealthData Talent admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500;12..96,700;12..96,800&family=Public+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/site.css') }}">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <script src="{{ asset('js/site.js') }}" defer></script>
</head>
<body class="admin">
<a class="skip" href="#main">Skip to content</a>

<header class="site">
    <div class="wrap bar">
        <a class="logo" href="{{ route('admin.dashboard') }}">
            <svg width="36" height="36" viewBox="0 0 36 36" aria-hidden="true">
                <rect class="logo-tile" width="36" height="36" rx="9"/>
                <path class="logo-trace" d="M5 20h6l2.5-6 4 12 3-9 2 3h8.5"/>
                <circle class="logo-dot" cx="30.5" cy="20" r="2.4"/>
            </svg>
            <span><b>HealthData Talent</b><small>Admin</small></span>
        </a>
        @auth
            <button class="menu-btn" type="button" aria-expanded="false" aria-controls="nav" data-menu>Menu</button>
            <nav class="main" id="nav" aria-label="Admin">
                <ul>
                    @php
                        $links = [
                            'admin.dashboard' => ['Dashboard', 'admin'],
                            'admin.candidates.index' => ['Candidates', 'admin/candidates*'],
                            'admin.employers.index' => ['Employers', 'admin/employers*'],
                            'admin.vacancies.index' => ['Jobs', 'admin/vacancies*'],
                            'admin.applications.index' => ['Applications', 'admin/applications*'],
                            'admin.enquiries.index' => ['Enquiries', 'admin/enquiries*'],
                            'admin.posts.index' => ['Content', 'admin/posts*'],
                        ];
                        if (auth()->user()->isAdmin()) {
                            $links['admin.audit'] = ['Audit log', 'admin/audit*'];
                        }
                    @endphp
                    @foreach ($links as $route => $link)
                        <li><a href="{{ route($route) }}" @if (request()->is($link[1])) aria-current="page" @endif>{{ $link[0] }}</a></li>
                    @endforeach
                    <li>
                        <form method="post" action="{{ route('admin.logout') }}">
                            @csrf
                            <button class="navlink" type="submit">Sign out</button>
                        </form>
                    </li>
                </ul>
            </nav>
        @endauth
    </div>
</header>

<main id="main" class="wrap admin-main">
    @if (session('status'))<p class="notice success" role="status">{{ session('status') }}</p>@endif
    @if ($errors->any() && ! View::hasSection('handles-errors'))
        <p class="notice error" role="alert">{{ $errors->first() }}</p>
    @endif
    @yield('content')
</main>
</body>
</html>
