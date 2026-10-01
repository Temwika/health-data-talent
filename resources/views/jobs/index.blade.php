@extends('layouts.site')

@section('title', 'Jobs')

@section('content')
<div class="wrap page-head">
    <h1>Current vacancies</h1>
    <p class="lede">Roles across health data, informatics, digital health and remote NGO work.</p>
</div>
<div class="wrap page-body">
    <form class="filters" method="get" action="{{ route('jobs.index') }}" role="search">
        <div class="field">
            <label for="f-q">Search</label>
            <input id="f-q" name="q" type="search" maxlength="80" value="{{ $filters['q'] ?? '' }}" placeholder="Job title, skill or organisation">
        </div>
        <div class="field">
            <label for="f-pattern">Working pattern</label>
            <select id="f-pattern" name="pattern">
                <option value="">Any</option>
                @foreach (['On-site', 'Hybrid', 'Remote'] as $pattern)
                    <option @selected(($filters['pattern'] ?? '') === $pattern)>{{ $pattern }}</option>
                @endforeach
            </select>
        </div>
        <div class="field">
            <label for="f-area">Area</label>
            <select id="f-area" name="area">
                <option value="">Any</option>
                @foreach (config('hdt.areas') as $key => $text)
                    <option value="{{ $key }}" @selected(($filters['area'] ?? '') === $key)>{{ $text }}</option>
                @endforeach
            </select>
        </div>
        <button class="btn primary" type="submit">Search</button>
    </form>

    <p class="hint" role="status">{{ $jobs->total() }} {{ Str::plural('vacancy', $jobs->total()) }}</p>

    @if ($jobs->isEmpty())
        <p class="empty">No vacancies match those filters. <a href="{{ route('jobs.index') }}">Clear the search</a>, or <a href="{{ route('candidates.create') }}">join the network</a> to hear about new roles first.</p>
    @else
        <ul class="jobs">
            @foreach ($jobs as $job)
                @include('partials.job-row', ['job' => $job])
            @endforeach
        </ul>
        {{ $jobs->links('partials.pager') }}
    @endif
</div>
@endsection
