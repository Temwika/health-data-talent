<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Vacancy;

class PageController extends Controller
{
    public function home()
    {
        return view('pages.home', [
            'featured' => Vacancy::live()->latest('published_at')->take(3)->get(),
            'posts' => Post::published()->latest('published_at')->take(3)->get(),
        ]);
    }
}
