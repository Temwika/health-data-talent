@extends('layouts.site')

@section('title', 'Privacy notice')

@section('content')
<div class="wrap page-head">
    <h1>Privacy notice</h1>
    <p class="hint">Draft for review by a qualified adviser. Highlighted items must be completed before launch. Version {{ config('hdt.privacy_version') }}.</p>
</div>
<div class="wrap page-body prose narrow">
    <h2>Who we are</h2>
    <p>{{ config('hdt.company') }} (“we”, “us”) is a recruitment agency registered in England and Wales, company number {{ config('hdt.company_number') }}, registered office {{ implode(', ', config('hdt.address')) }}. We are the data controller for the personal information described here and are registered with the Information Commissioner's Office, registration number <span class="ph">[ICO number]</span>. Contact our data protection lead at <span class="ph">privacy@[yourdomain]</span>.</p>

    <h2>What we collect</h2>
    <h3>Candidates</h3>
    <ul>
        <li>Contact details: name, email, phone, location.</li>
        <li>Career information: CV, job titles, skills, qualifications, experience, salary expectations and availability.</li>
        <li>Right-to-work information, when needed for a specific role.</li>
        <li>For doctors: specialty and professional registration details.</li>
        <li>Notes from our conversations and records of the roles we discuss with you.</li>
        <li>A record of when you accepted this notice, including the date, your IP address and browser type.</li>
    </ul>
    <p>Please do not include special category data such as health information, religion or ethnicity in your CV. If you choose to share it, we will only use it where the law allows, for example to make reasonable adjustments for an interview.</p>
    <h3>Employers and NGOs</h3>
    <ul><li>Business contact details and information about your vacancies.</li></ul>

    <h2>Why we use it, and our lawful basis</h2>
    <ul>
        <li><strong>To find you suitable work and introduce you to employers</strong>: legitimate interests, and steps taken at your request before entering a contract.</li>
        <li><strong>To meet our legal duties</strong>, including the Conduct of Employment Agencies and Employment Businesses Regulations 2003 and right-to-work checks: legal obligation.</li>
        <li><strong>To send job alerts</strong>: your consent, which you can withdraw at any time.</li>
        <li><strong>To manage our relationship with employers</strong>: legitimate interests and contract.</li>
    </ul>

    <h2>Who we share it with</h2>
    <p>We send your CV and details to an employer only after you have agreed to be put forward for that specific role. We also use trusted service providers to host our website, email and database, under contracts that require them to protect your data. <span class="ph">[List providers, e.g. hosting, email, CRM.]</span></p>

    <h2>International transfers</h2>
    <p>Where we introduce candidates to organisations outside the UK, such as international NGOs, or use providers that store data abroad, we use appropriate safeguards such as the UK International Data Transfer Agreement or adequacy regulations.</p>

    <h2>How long we keep it</h2>
    <p>We keep candidate profiles for {{ config('hdt.retention_months') }} months after our last meaningful contact, then delete them, together with the CV file, unless you ask us to keep them. We keep records required by the Conduct Regulations for at least one year from their creation, or longer where the law requires.</p>

    <h2>Your rights</h2>
    <p>You can ask us to access, correct, delete or restrict your data, to object to how we use it, or to transfer it to you. Email <span class="ph">privacy@[yourdomain]</span> and we will respond within one month. You can also complain to the Information Commissioner's Office at ico.org.uk.</p>

    <h2>Security</h2>
    <p>Our website uses encrypted connections (HTTPS). Access to candidate data is limited to staff who need it and protected by two-factor authentication. CV files are held in private storage that cannot be reached from the public web, and each time a member of staff opens a profile or downloads a CV it is recorded.</p>

    <h2>Cookies</h2>
    <p>We use only cookies that are strictly necessary for the website to work: one that keeps your form session secure. If we add analytics, we will ask for your consent first.</p>
</div>
@endsection
