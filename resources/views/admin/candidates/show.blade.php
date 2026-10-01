@extends('layouts.admin')

@section('title', $candidate->name)

@section('content')
<p><a class="textlink back" href="{{ route('admin.candidates.index') }}">All candidates</a></p>
<h1>{{ $candidate->name }}</h1>
<p class="lede">{{ $candidate->current_title }} · {{ $candidate->trackLabel() }}</p>

<div class="admin-grid wide-left">
    <div>
        <section class="panel">
            <h2>Profile</h2>
            <dl class="details">
                <dt>Email</dt><dd><a href="mailto:{{ $candidate->email }}">{{ $candidate->email }}</a></dd>
                <dt>Phone</dt><dd>{{ $candidate->phone ?: 'Not given' }}</dd>
                <dt>Location</dt><dd>{{ $candidate->location }}</dd>
                <dt>Experience</dt><dd>{{ $candidate->years }} years</dd>
                <dt>Qualification</dt><dd>{{ $candidate->qualification ?: 'Not given' }}</dd>
                @if ($candidate->isDoctor())
                    <dt>Specialty</dt><dd>{{ $candidate->specialty }}</dd>
                    <dt>Registration</dt><dd>{{ $candidate->registration ?: 'Not given' }}</dd>
                    <dt>Remote work</dt><dd>{{ implode(', ', $candidate->ngo_work ?? []) ?: 'Not given' }}</dd>
                @else
                    <dt>Expertise</dt><dd>{{ implode(', ', $candidate->expertise ?? []) ?: 'Not given' }}</dd>
                    <dt>Tools</dt><dd>{{ implode(', ', $candidate->tools ?? []) ?: 'Not given' }}</dd>
                @endif
                <dt>Wants</dt><dd>{{ $candidate->desired_role }}</dd>
                <dt>Pattern</dt><dd>{{ $candidate->pattern ?: 'Any' }}</dd>
                <dt>Salary</dt><dd>{{ $candidate->salary ?: 'Not given' }}</dd>
                <dt>Availability</dt><dd>{{ $candidate->availability ?: 'Not given' }}</dd>
                <dt>Right to work</dt><dd>{{ $candidate->right_to_work ?: 'Not given' }}</dd>
                <dt>CV</dt>
                <dd>
                    @if ($candidate->cv_path)
                        <a href="{{ route('admin.candidates.cv', $candidate) }}">{{ $candidate->cv_original_name }}</a>
                        ({{ number_format($candidate->cv_size / 1024) }} KB)
                        @if ($candidate->cv_scan_status === 'clean')
                            <span class="status live">Malware scan: clean</span>
                        @else
                            <span class="status pending">Not malware-scanned. Open with care.</span>
                        @endif
                    @else
                        None uploaded
                    @endif
                </dd>
            </dl>
        </section>

        <section class="panel">
            <h2>Applications</h2>
            @if ($candidate->applications->isEmpty())
                <p class="hint">Not linked to any vacancy yet.</p>
            @else
                <ul class="todo">
                    @foreach ($candidate->applications as $application)
                        <li>
                            <span>{{ $application->vacancy->title }}, {{ $application->vacancy->organisation_name }}</span>
                            <span class="status">{{ $application->stageLabel() }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif

            <h3>Suggested vacancies</h3>
            @if ($suggested->isEmpty())
                <p class="hint">No open vacancies match this profile's skills.</p>
            @else
                @foreach ($suggested as $match)
                    <form class="match" method="post" action="{{ route('admin.applications.store', $candidate) }}">
                        @csrf
                        <input type="hidden" name="vacancy_id" value="{{ $match['vacancy']->id }}">
                        <p><strong>{{ $match['vacancy']->title }}</strong>, {{ $match['vacancy']->organisation_name }} <span class="hint">({{ $match['score'] }} skill {{ Str::plural('match', $match['score']) }})</span></p>
                        <label class="check"><input type="checkbox" name="consent" value="1" required><span>Candidate has agreed to be put forward for this role</span></label>
                        <button class="btn ghost small" type="submit">Add to shortlist</button>
                    </form>
                @endforeach
            @endif
        </section>
    </div>

    <div>
        <section class="panel">
            <h2>Status and notes</h2>
            <form method="post" action="{{ route('admin.candidates.update', $candidate) }}">
                @csrf
                @method('patch')
                <x-select name="status" label="Status" :options="config('hdt.candidate_statuses')" :value="$candidate->status" :placeholder="false" required />
                <x-textarea name="notes" label="Notes" :value="$candidate->notes" maxlength="5000" rows="6" />
                <label class="check"><input type="checkbox" name="contacted" value="1"><span>I had meaningful contact with this candidate today (restarts the retention clock)</span></label>
                <div class="form-actions"><button class="btn primary small" type="submit">Save</button></div>
            </form>
        </section>

        <section class="panel">
            <h2>Data protection</h2>
            <dl class="details">
                <dt>Last contact</dt><dd>{{ $candidate->last_contact_at?->format('j M Y') ?? 'Never' }}</dd>
                <dt>Delete by</dt><dd>{{ $candidate->retentionDue()?->format('j M Y') ?? 'Not set' }}</dd>
                <dt>Job alerts</dt><dd>{{ $candidate->marketing_opt_in ? 'Opted in' : 'No' }}</dd>
            </dl>
            <h3>Consent record</h3>
            <ul class="plain">
                @forelse ($candidate->consents as $consent)
                    <li>{{ str_replace('_', ' ', $consent->type) }}, v{{ $consent->version }}, {{ $consent->granted_at->format('j M Y H:i') }}</li>
                @empty
                    <li>No consent recorded.</li>
                @endforelse
            </ul>
            @if (auth()->user()->isAdmin())
                <form method="post" action="{{ route('admin.candidates.destroy', $candidate) }}" data-confirm="Permanently delete this profile and CV? This cannot be undone.">
                    @csrf
                    @method('delete')
                    <button class="btn danger small" type="submit">Delete profile and CV</button>
                </form>
            @endif
        </section>
    </div>
</div>
@endsection
