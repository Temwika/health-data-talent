<article class="post-card">
    <p class="eyebrow">{{ $post->category }}</p>
    <h3><a href="{{ route('insights.show', $post) }}">{{ $post->title }}</a></h3>
    <p>{{ $post->excerpt }}</p>
    <p class="hint">{{ $post->published_at->format('j M Y') }}</p>
</article>
