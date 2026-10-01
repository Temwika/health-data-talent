@extends('layouts.admin')

@section('title', 'Applications')

@section('content')
<h1>Applications and placements</h1>

<div class="tabs">
    <a class="tab" href="{{ route('admin.applications.index') }}" @if (! $stage) aria-current="page" @endif>All</a>
    @foreach (config('hdt.stages') as $key => $label)
        <a class="tab" href="{{ route('admin.applications.index', ['stage' => $key]) }}" @if ($stage === $key) aria-current="page" @endif>{{ $label }}</a>
    @endforeach
</div>

<div class="tbl-wrap">
    @if ($applications->isEmpty())
        <p class="empty">No applications here yet. They appear when a candidate registers interest in a job, or when you shortlist a candidate from their profile.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th scope="col">Candidate</th><th scope="col">Vacancy</th><th scope="col">Source</th>
                    <th scope="col">Consent</th><th scope="col">Stage</th><th scope="col">Updated</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($applications as $application)
                    <tr>
                        <td><a href="{{ route('admin.candidates.show', $application->candidate) }}">{{ $application->candidate->name }}</a></td>
                        <td>{{ $application->vacancy->title }}<br><span class="hint">{{ $application->vacancy->organisation_name }}</span></td>
                        <td>{{ $application->source === 'applied' ? 'Applied on site' : 'Matched by staff' }}</td>
                        <td>{{ $application->consented_at?->format('j M Y') ?? 'Not recorded' }}</td>
                        <td>
                            <form class="inline-form" method="post" action="{{ route('admin.applications.update', $application) }}">
                                @csrf @method('patch')
                                <label class="sr-only" for="stage-{{ $application->id }}">Stage for {{ $application->candidate->name }}</label>
                                <select id="stage-{{ $application->id }}" name="stage">
                                    @foreach (config('hdt.stages') as $key => $label)
                                        <option value="{{ $key }}" @selected($application->stage === $key)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <button class="btn ghost small" type="submit">Update</button>
                            </form>
                        </td>
                        <td>{{ $application->updated_at->format('j M Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
{{ $applications->links('partials.pager') }}
@endsection
