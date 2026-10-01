@extends('layouts.admin')

@section('title', 'Jobs')

@section('content')
<div class="section-head">
    <h1>Jobs</h1>
    <a class="btn primary small" href="{{ route('admin.vacancies.create') }}">Add a vacancy</a>
</div>

<div class="tbl-wrap">
    @if ($vacancies->isEmpty())
        <p class="empty">No vacancies yet.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th scope="col">Role</th><th scope="col">Status</th><th scope="col">Applications</th>
                    <th scope="col">Suggested candidates</th><th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($vacancies as $vacancy)
                    <tr>
                        <td>
                            <strong>{{ $vacancy->title }}</strong>
                            @if ($vacancy->is_example)<span class="tag ex">Example</span>@endif
                            <br><span class="hint">{{ $vacancy->organisation_name }} · {{ $vacancy->location }} · {{ $vacancy->salary }}</span>
                            @if ($vacancy->contact_email)<br><a class="hint" href="mailto:{{ $vacancy->contact_email }}">{{ $vacancy->contact_name }} ({{ $vacancy->contact_email }})</a>@endif
                        </td>
                        <td><span class="status {{ $vacancy->status }}">{{ ['pending' => 'Pending review', 'live' => 'Live', 'closed' => 'Closed'][$vacancy->status] ?? $vacancy->status }}</span></td>
                        <td>{{ $vacancy->applications_count }}</td>
                        <td>
                            @forelse ($matches[$vacancy->id] as $match)
                                <a href="{{ route('admin.candidates.show', $match['candidate']) }}">{{ $match['candidate']->name }}</a> ({{ $match['score'] }})@if (! $loop->last), @endif
                            @empty
                                <span class="hint">No matches yet</span>
                            @endforelse
                        </td>
                        <td>
                            <div class="row-actions">
                                <form method="post" action="{{ route('admin.vacancies.status', $vacancy) }}">
                                    @csrf @method('patch')
                                    <input type="hidden" name="status" value="{{ $vacancy->isLive() ? 'pending' : 'live' }}">
                                    <button class="btn primary small" type="submit">{{ $vacancy->isLive() ? 'Unpublish' : 'Approve' }}</button>
                                </form>
                                <a class="btn ghost small" href="{{ route('admin.vacancies.edit', $vacancy) }}">Edit</a>
                                @if ($vacancy->status !== 'closed')
                                    <form method="post" action="{{ route('admin.vacancies.status', $vacancy) }}">
                                        @csrf @method('patch')
                                        <input type="hidden" name="status" value="closed">
                                        <button class="btn ghost small" type="submit">Close</button>
                                    </form>
                                @endif
                                @if (auth()->user()->isAdmin())
                                    <form method="post" action="{{ route('admin.vacancies.destroy', $vacancy) }}" data-confirm="Delete this vacancy and its applications?">
                                        @csrf @method('delete')
                                        <button class="btn danger small" type="submit">Delete</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
{{ $vacancies->links('partials.pager') }}
@endsection
