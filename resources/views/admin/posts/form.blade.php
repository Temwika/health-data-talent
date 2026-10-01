@extends('layouts.admin')

@section('title', $post->exists ? 'Edit article' : 'Write an article')
@section('handles-errors', '1')

@section('content')
<p><a class="textlink back" href="{{ route('admin.posts.index') }}">All articles</a></p>
<h1>{{ $post->exists ? 'Edit article' : 'Write an article' }}</h1>

<form class="card narrow-form" method="post" action="{{ $post->exists ? route('admin.posts.update', $post) : route('admin.posts.store') }}">
    @csrf
    @if ($post->exists) @method('put') @endif

    <div class="row">
        <x-field name="title" label="Title" :value="$post->title" required maxlength="140" />
        <x-select name="category" label="Category" :options="config('hdt.post_categories')" :value="$post->category" required />
    </div>
    <x-textarea name="excerpt" label="Summary" hint="Shown on cards and in search results, up to 300 characters" :value="$post->excerpt" required maxlength="300" rows="3" />
    <x-textarea name="body" label="Article" hint="Plain text. Leave a blank line between paragraphs. Start a line with ## for a sub-heading." :value="$post->body" required maxlength="20000" rows="18" />

    <label class="check">
        <input type="hidden" name="published" value="0">
        <input type="checkbox" name="published" value="1" @checked(old('published', (bool) $post->published_at))>
        <span>Published on the website</span>
    </label>

    <div class="form-actions">
        <button class="btn primary" type="submit">Save article</button>
    </div>
</form>
@endsection
