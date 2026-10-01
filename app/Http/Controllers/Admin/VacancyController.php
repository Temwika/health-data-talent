<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Candidate;
use App\Models\Vacancy;
use App\Support\Matcher;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class VacancyController extends Controller
{
    public function index()
    {
        $vacancies = Vacancy::withCount('applications')->latest()->paginate(15);

        // Suggested matches only come from candidates still in play.
        $pool = Candidate::whereIn('status', ['new', 'screened', 'active'])->latest()->take(500)->get();
        $matches = $vacancies->getCollection()->mapWithKeys(
            fn (Vacancy $v) => [$v->id => Matcher::top($v, $pool)]
        );

        return view('admin.vacancies.index', compact('vacancies', 'matches'));
    }

    public function create()
    {
        return view('admin.vacancies.form', ['vacancy' => new Vacancy(['contract_type' => 'Permanent'])]);
    }

    public function store(Request $request)
    {
        $vacancy = Vacancy::create($this->validated($request));
        AuditLog::record('vacancy.created', $vacancy, 'Created vacancy '.$vacancy->title);

        return redirect()->route('admin.vacancies.index')->with('status', 'Vacancy saved as pending. Approve it to publish.');
    }

    public function edit(Vacancy $vacancy)
    {
        return view('admin.vacancies.form', compact('vacancy'));
    }

    public function update(Request $request, Vacancy $vacancy)
    {
        $vacancy->update($this->validated($request));
        AuditLog::record('vacancy.updated', $vacancy, 'Edited vacancy '.$vacancy->title);

        return redirect()->route('admin.vacancies.index')->with('status', 'Vacancy updated.');
    }

    public function status(Request $request, Vacancy $vacancy)
    {
        $data = $request->validate(['status' => ['required', 'in:pending,live,closed']]);

        $vacancy->status = $data['status'];
        if ($data['status'] === 'live') {
            $vacancy->published_at ??= now();
        }
        $vacancy->save();

        AuditLog::record('vacancy.'.$data['status'], $vacancy, $vacancy->title.' set to '.$data['status']);

        return back()->with('status', match ($data['status']) {
            'live' => 'Vacancy approved and live on the jobs board.',
            'closed' => 'Vacancy closed.',
            default => 'Vacancy unpublished.',
        });
    }

    public function destroy(Vacancy $vacancy)
    {
        $title = $vacancy->title;
        $vacancy->delete();

        AuditLog::record('vacancy.deleted', null, 'Deleted vacancy '.$title);

        return back()->with('status', 'Vacancy deleted.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'organisation_name' => ['required', 'string', 'max:120'],
            'location' => ['required', 'string', 'max:80'],
            'pattern' => ['required', Rule::in(config('hdt.patterns'))],
            'salary' => ['required', 'string', 'max:60'],
            'contract_type' => ['required', Rule::in(config('hdt.contract_types'))],
            'area' => ['required', Rule::in(array_keys(config('hdt.areas')))],
            'skills' => ['required', 'string', 'max:300'],
            'description' => ['required', 'string', 'max:4000'],
            'contact_name' => ['nullable', 'string', 'max:100'],
            'contact_email' => ['nullable', 'email', 'max:160'],
        ]);
    }
}
