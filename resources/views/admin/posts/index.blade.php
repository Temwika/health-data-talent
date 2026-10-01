@extends('layouts.admin')

@section('title', 'Content')

@section('content')
<div class="section-head">
    <h1>Insights articles</h1>
    <a class="btn primary small" href="{{ route('admin.posts.create') }}">Write an article</a>
</div>

<div class="tbl-wrap">
    @if ($posts->isEmpty())
        <p class="empty">No articles yet.</p>
    @else
        <table>
            <thead><tr><th scope="col">Title</th><th scope="col">Category</th><th scope="col">Status</th><th scope="col">Action</th></tr></thead>
            <tbody>
                @foreach ($posts as $post)
                    <tr>
                        <td><strong>{{ $post->title }}</strong></td>
                        <td>{{ $post->category }}</td>
                        <td>
                            @if ($post->published_at)
                                <span class="status live">Published {{ $post->published_at->format('j M Y') }}</span>
                            @else
                                <span class="status pending">Draft</span>
                            @endif
                        </td>
                        <td>
                            <div class="row-actions">
                                <a class="btn ghost small" href="{{ route('admin.posts.edit', $post) }}">Edit</a>
                                @if (auth()->user()->isAdmin())
                                    <form method="post" action="{{ route('admin.posts.destroy', $post) }}" data-confirm="Delete this article?">
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
{{ $posts->links('partials.pager') }}
@endsection
