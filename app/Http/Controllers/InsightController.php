<?php

namespace App\Http\Controllers;

use App\Models\Post;

class InsightController extends Controller
{
    public function index()
    {
        return view('insights.index', [
            'posts' => Post::published()->latest('published_at')->paginate(9),
        ]);
    }

    public function show(Post $post)
    {
        abort_unless($post->published_at?->isPast(), 404);

        return view('insights.show', ['post' => $post]);
    }
}
