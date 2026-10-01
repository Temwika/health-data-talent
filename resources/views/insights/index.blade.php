@extends('layouts.site')

@section('title', 'Insights')

@section('content')
<div class="wrap page-head">
    <h1>Insights</h1>
    <p class="lede">Notes on hiring and careers in health data, informatics and digital health.</p>
</div>
<div class="wrap page-body">
    @if ($posts->isEmpty())
        <p class="empty">No articles yet. Check back soon.</p>
    @else
        <div class="posts">
            @foreach ($posts as $post)
                @include('insights.card', ['post' => $post])
            @endforeach
        </div>
        {{ $posts->links('partials.pager') }}
    @endif
</div>
@endsection
