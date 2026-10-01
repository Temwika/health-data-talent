@extends('layouts.admin')

@section('title', 'Enquiries')

@section('content')
<h1>Enquiries</h1>

<div class="tbl-wrap">
    @if ($enquiries->isEmpty())
        <p class="empty">No messages from the contact form yet.</p>
    @else
        <table>
            <thead>
                <tr><th scope="col">From</th><th scope="col">Message</th><th scope="col">Received</th><th scope="col">Action</th></tr>
            </thead>
            <tbody>
                @foreach ($enquiries as $enquiry)
                    <tr>
                        <td>
                            <strong>{{ $enquiry->name }}</strong>
                            @if ($enquiry->organisation)<br><span class="hint">{{ $enquiry->organisation }}</span>@endif
                            <br><a href="mailto:{{ $enquiry->email }}">{{ $enquiry->email }}</a>
                        </td>
                        <td class="wrap-text"><strong>{{ $enquiry->subject }}</strong><br>{{ $enquiry->message }}</td>
                        <td>{{ $enquiry->created_at->format('j M Y') }}</td>
                        <td>
                            <form method="post" action="{{ route('admin.enquiries.update', $enquiry) }}">
                                @csrf @method('patch')
                                <button class="btn {{ $enquiry->handled_at ? 'ghost' : 'primary' }} small" type="submit">{{ $enquiry->handled_at ? 'Reopen' : 'Mark handled' }}</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
{{ $enquiries->links('partials.pager') }}
@endsection
