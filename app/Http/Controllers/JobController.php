<?php

namespace App\Http\Controllers;

use App\Models\Vacancy;
use Illuminate\Http\Request;

class JobController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:80'],
            'pattern' => ['nullable', 'in:On-site,Hybrid,Remote'],
            'area' => ['nullable', 'in:'.implode(',', array_keys(config('hdt.areas')))],
        ]);

        $jobs = Vacancy::live()
            ->when($filters['q'] ?? null, function ($query, $q) {
                $like = '%'.addcslashes($q, '%_\\').'%';
                $query->where(fn ($w) => $w
                    ->where('title', 'like', $like)
                    ->orWhere('organisation_name', 'like', $like)
                    ->orWhere('skills', 'like', $like)
                    ->orWhere('location', 'like', $like));
            })
            ->when($filters['pattern'] ?? null, fn ($query, $p) => $query->where('pattern', 'like', $p.'%'))
            ->when($filters['area'] ?? null, fn ($query, $a) => $query->where('area', $a))
            ->latest('published_at')
            ->paginate(10)
            ->withQueryString();

        return view('jobs.index', ['jobs' => $jobs, 'filters' => $filters]);
    }

    public function show(Vacancy $vacancy)
    {
        abort_unless($vacancy->isLive(), 404);

        return view('jobs.show', ['job' => $vacancy]);
    }
}
