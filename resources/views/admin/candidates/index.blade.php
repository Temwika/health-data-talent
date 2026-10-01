@extends('layouts.admin')

@section('title', 'Candidates')

@section('content')
<h1>Candidates</h1>

<form class="filters two" method="get" action="{{ route('admin.candidates.index') }}" role="search">
    <div class="field">
        <label for="f-q">Search</label>
        <input id="f-q" name="q" type="search" value="{{ $q }}" maxlength="80" placeholder="Name, email, role or location">
    </div>
    <div class="field">
        <label for="f-track">Track</label>
        <select id="f-track" name="track">
            <option value="">All</option>
            <option value="data" @selected($track === 'data')>Health data</option>
            <option value="doctor" @selected($track === 'doctor')>Doctors (NGO)</option>
        </select>
    </div>
    <button class="btn primary" type="submit">Filter</button>
</form>

<div class="tbl-wrap">
    @if ($candidates->isEmpty())
        <p class="empty">No candidates match.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th scope="col">Name</th><th scope="col">Track</th><th scope="col">Current role</th>
                    <th scope="col">Skills</th><th scope="col">Location</th><th scope="col">CV</th>
                    <th scope="col">Status</th><th scope="col">Joined</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($candidates as $candidate)
                    <tr>
                        <td><a href="{{ route('admin.candidates.show', $candidate) }}">{{ $candidate->name }}</a></td>
                        <td>{{ $candidate->trackLabel() }}</td>
                        <td>{{ $candidate->current_title }}</td>
                        <td>{{ Str::limit(implode(', ', $candidate->skillList()), 70) }}</td>
                        <td>{{ $candidate->location }}</td>
                        <td>{{ $candidate->cv_path ? 'Yes' : 'None' }}</td>
                        <td><span class="status {{ $candidate->status }}">{{ config('hdt.candidate_statuses')[$candidate->status] ?? $candidate->status }}</span></td>
                        <td>{{ $candidate->created_at->format('j M Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
{{ $candidates->links('partials.pager') }}
@endsection
