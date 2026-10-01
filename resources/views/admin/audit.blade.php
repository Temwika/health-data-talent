@extends('layouts.admin')

@section('title', 'Audit log')

@section('content')
<h1>Audit log</h1>
<p class="lede">Sign-ins, profile views, CV downloads, approvals and deletions. Entries cannot be edited from the dashboard.</p>

<div class="tbl-wrap">
    @if ($logs->isEmpty())
        <p class="empty">Nothing logged yet.</p>
    @else
        <table>
            <thead><tr><th scope="col">When</th><th scope="col">Who</th><th scope="col">Action</th><th scope="col">Detail</th><th scope="col">IP address</th></tr></thead>
            <tbody>
                @foreach ($logs as $log)
                    <tr>
                        <td class="nowrap">{{ $log->created_at->format('j M Y H:i') }}</td>
                        <td>{{ $log->user?->name ?? 'System / not signed in' }}</td>
                        <td><code>{{ $log->action }}</code></td>
                        <td class="wrap-text">{{ $log->summary }}</td>
                        <td>{{ $log->ip ?? '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
{{ $logs->links('partials.pager') }}
@endsection
