<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PostController extends Controller
{
    public function index()
    {
        return view('admin.posts.index', ['posts' => Post::latest()->paginate(20)]);
    }

    public function create()
    {
        return view('admin.posts.form', ['post' => new Post]);
    }

    public function store(Request $request)
    {
        $post = new Post($this->validated($request));
        $post->published_at = $request->boolean('published') ? now() : null;
        $post->save();

        AuditLog::record('post.created', $post, 'Created article '.$post->title);

        return redirect()->route('admin.posts.index')->with('status', 'Article saved.');
    }

    public function edit(Post $post)
    {
        return view('admin.posts.form', compact('post'));
    }

    public function update(Request $request, Post $post)
    {
        $post->fill($this->validated($request));
        $post->published_at = $request->boolean('published') ? ($post->published_at ?? now()) : null;
        $post->save();

        AuditLog::record('post.updated', $post, 'Edited article '.$post->title);

        return redirect()->route('admin.posts.index')->with('status', 'Article updated.');
    }

    public function destroy(Post $post)
    {
        $title = $post->title;
        $post->delete();

        AuditLog::record('post.deleted', null, 'Deleted article '.$title);

        return back()->with('status', 'Article deleted.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:140'],
            'category' => ['required', Rule::in(config('hdt.post_categories'))],
            'excerpt' => ['required', 'string', 'max:300'],
            'body' => ['required', 'string', 'max:20000'],
            'published' => ['nullable', 'boolean'],
        ]);
    }
}
