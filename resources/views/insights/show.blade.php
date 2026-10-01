@extends('layouts.site')

@section('title', $post->title)
@section('description', $post->excerpt)

@section('content')
<article>
    <div class="wrap page-head">
        <p class="eyebrow">{{ $post->category }} · {{ $post->published_at->format('j F Y') }}</p>
        <h1>{{ $post->title }}</h1>
        <p class="lede">{{ $post->excerpt }}</p>
    </div>
    <div class="wrap page-body prose narrow">
        @foreach ($post->blocks() as $block)
            @if ($block['type'] === 'h2')
                <h2>{{ $block['text'] }}</h2>
            @else
                <p>{{ $block['text'] }}</p>
            @endif
        @endforeach
        <p><a class="textlink" href="{{ route('insights.index') }}">All articles</a></p>
    </div>
</article>
@endsection
