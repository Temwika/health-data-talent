@extends('layouts.site')

@section('title', $job->title)
@section('description', Str::limit($job->description, 150))

@section('content')
<div class="wrap page-head">
    <p class="eyebrow">{{ $job->areaLabel() }}@if ($job->is_example) · Example listing @endif</p>
    <h1>{{ $job->title }}</h1>
    <div class="meta">
        <span>{{ $job->organisation_name }}</span>
        <span>{{ $job->location }}</span>
        <span>{{ $job->pattern }}</span>
        <span>{{ $job->salary }}</span>
        <span>{{ $job->contract_type }}</span>
    </div>
</div>
<div class="wrap page-body">
    <div class="form-layout">
        <div class="prose">
            @if ($job->is_example)
                <p class="notice">This is an example listing that shows the kind of role we recruit for. It is not a live vacancy.</p>
            @endif
            <h2>About the role</h2>
            @foreach (preg_split('/\R+/', trim($job->description)) as $paragraph)
                <p>{{ $paragraph }}</p>
            @endforeach
            <h2>Essential skills</h2>
            <ul class="taglist">
                @foreach ($job->skillList() as $skill)
                    <li>{{ $skill }}</li>
                @endforeach
            </ul>
            <p><a class="textlink" href="{{ route('jobs.index') }}">Back to all jobs</a></p>
        </div>
        <aside class="aside">
            <h2 class="h3">Interested?</h2>
            <p>Register your interest and we'll call you about the role. Your details go to the employer only after you agree.</p>
            <a class="btn primary block" href="{{ route('candidates.create', ['job' => $job->slug]) }}">Register your interest</a>
        </aside>
    </div>
</div>
@endsection
