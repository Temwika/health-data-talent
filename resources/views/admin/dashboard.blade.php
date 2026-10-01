@extends('layouts.admin')

@section('content')
<h1>Dashboard</h1>

<div class="stats">
    @foreach ($stats as $stat)
        <a class="stat" href="{{ route($stat['route']) }}">
            <strong>{{ number_format($stat['value']) }}</strong>
            <span>{{ $stat['label'] }}</span>
        </a>
    @endforeach
</div>

<div class="admin-grid">
    <section class="panel">
        <h2>Needs attention</h2>
        <ul class="todo">
            @foreach ($todo as $item)
                <li>
                    <a href="{{ route($item['route']) }}">{{ $item['label'] }}</a>
                    <span @class(['count', 'is-zero' => $item['value'] === 0])>{{ $item['value'] }}</span>
                </li>
            @endforeach
            <li>
                <span>Profiles within a month of the {{ config('hdt.retention_months') }}-month retention limit</span>
                <span @class(['count', 'is-zero' => $retentionDue === 0])>{{ $retentionDue }}</span>
            </li>
        </ul>
    </section>

    <section class="panel">
        <h2>Pipeline</h2>
        <ul class="todo">
            @foreach (config('hdt.stages') as $key => $label)
                <li>
                    <a href="{{ route('admin.applications.index', ['stage' => $key]) }}">{{ $label }}</a>
                    <span @class(['count', 'is-zero' => ! ($stageCounts[$key] ?? 0)])>{{ $stageCounts[$key] ?? 0 }}</span>
                </li>
            @endforeach
        </ul>
    </section>
</div>

<h2>Latest candidates</h2>
<div class="tbl-wrap">
    @if ($recentCandidates->isEmpty())
        <p class="empty">No candidates yet.</p>
    @else
        <table>
            <thead><tr><th scope="col">Name</th><th scope="col">Track</th><th scope="col">Current role</th><th scope="col">Location</th><th scope="col">Joined</th></tr></thead>
            <tbody>
                @foreach ($recentCandidates as $candidate)
                    <tr>
                        <td><a href="{{ route('admin.candidates.show', $candidate) }}">{{ $candidate->name }}</a></td>
                        <td>{{ $candidate->trackLabel() }}</td>
                        <td>{{ $candidate->current_title }}</td>
                        <td>{{ $candidate->location }}</td>
                        <td>{{ $candidate->created_at->format('j M Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
