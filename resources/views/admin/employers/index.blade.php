@extends('layouts.admin')

@section('title', 'Employers')

@section('content')
<h1>Employers</h1>

<div class="tbl-wrap">
    @if ($organisations->isEmpty())
        <p class="empty">No employer registrations yet.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th scope="col">Organisation</th><th scope="col">Contact</th><th scope="col">Service</th>
                    <th scope="col">Message</th><th scope="col">Status</th><th scope="col">Received</th><th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($organisations as $organisation)
                    <tr>
                        <td><strong>{{ $organisation->name }}</strong><br><span class="hint">{{ $organisation->sector }}</span></td>
                        <td>
                            {{ $organisation->contact_name }}@if ($organisation->contact_title), {{ $organisation->contact_title }}@endif<br>
                            <a href="mailto:{{ $organisation->email }}">{{ $organisation->email }}</a>
                            @if ($organisation->phone)<br>{{ $organisation->phone }}@endif
                        </td>
                        <td>{{ $organisation->serviceLabel() }}</td>
                        <td class="wrap-text">{{ Str::limit($organisation->message, 160) ?: '—' }}</td>
                        <td><span class="status {{ $organisation->status }}">{{ ucfirst($organisation->status) }}</span></td>
                        <td>{{ $organisation->created_at->format('j M Y') }}</td>
                        <td>
                            <div class="row-actions">
                                @if ($organisation->status !== 'approved')
                                    <form method="post" action="{{ route('admin.employers.update', $organisation) }}">
                                        @csrf @method('patch')
                                        <input type="hidden" name="status" value="approved">
                                        <button class="btn primary small" type="submit">Approve</button>
                                    </form>
                                @endif
                                @if ($organisation->status === 'pending')
                                    <form method="post" action="{{ route('admin.employers.update', $organisation) }}">
                                        @csrf @method('patch')
                                        <input type="hidden" name="status" value="declined">
                                        <button class="btn ghost small" type="submit">Decline</button>
                                    </form>
                                @endif
                                @if (auth()->user()->isAdmin())
                                    <form method="post" action="{{ route('admin.employers.destroy', $organisation) }}" data-confirm="Delete this employer registration?">
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
{{ $organisations->links('partials.pager') }}
@endsection
