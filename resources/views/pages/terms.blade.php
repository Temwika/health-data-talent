@extends('layouts.site')

@section('title', 'Terms')

@section('content')
<div class="wrap page-head">
    <h1>Terms</h1>
    <p class="hint">Draft outline. Have these reviewed by a solicitor or recruitment-compliance adviser before your first placement.</p>
</div>
<div class="wrap page-body prose narrow">
    <h2>Website terms of use</h2>
    <p>This website is operated by {{ config('hdt.company') }}. Information on job listings is provided by employers; we check it before publishing but cannot guarantee it is complete. You must not misuse the site, submit false information or attempt to access data that is not yours.</p>

    <h2>Candidates</h2>
    <ul>
        <li>We act as an employment agency introducing you for permanent roles, or to organisations that engage you directly.</li>
        <li>We do not charge you any fee for finding you work.</li>
        <li>We will tell you about each role, including the employer, before we introduce you, and only introduce you with your agreement.</li>
        <li>You agree that the information you give us is accurate.</li>
    </ul>

    <h2>Employers: summary of terms of business</h2>
    <ul>
        <li>Our full terms of business are agreed in writing before we start work on a role.</li>
        <li>Specialist recruitment fees are a percentage of first-year basic salary, agreed with you per role, payable on the candidate's start date within <span class="ph">[30]</span> days.</li>
        <li>A rebate applies if a placed candidate leaves within <span class="ph">[12]</span> weeks <span class="ph">[set scale]</span>.</li>
        <li>A fee is payable if you engage a candidate we introduced within <span class="ph">[12]</span> months of introduction.</li>
        <li>Shortlist sourcing and talent mapping are charged at the fixed price in your proposal.</li>
        <li>You confirm the role exists, give us accurate details about it as required by the Conduct Regulations, and carry out your own right-to-work checks.</li>
    </ul>

    <h2>Complaints</h2>
    <p>Email <a href="mailto:{{ config('hdt.contact_email') }}">{{ config('hdt.contact_email') }}</a>. We acknowledge complaints within two working days and aim to resolve them within 14 days.</p>
</div>
@endsection
