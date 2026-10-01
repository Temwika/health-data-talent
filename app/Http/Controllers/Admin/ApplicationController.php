<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\AuditLog;
use App\Models\Candidate;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ApplicationController extends Controller
{
    public function index(Request $request)
    {
        $stage = $request->query('stage');

        $applications = Application::with(['candidate', 'vacancy'])
            ->when(array_key_exists((string) $stage, config('hdt.stages')), fn ($query) => $query->where('stage', $stage))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.applications.index', compact('applications', 'stage'));
    }

    /** Link a candidate to a vacancy from a suggested match. */
    public function store(Request $request, Candidate $candidate)
    {
        $data = $request->validate([
            'vacancy_id' => ['required', 'exists:vacancies,id'],
            'consent' => ['accepted'],
        ], [
            'consent.accepted' => 'Confirm the candidate has agreed to be put forward for this role.',
        ]);

        $application = Application::firstOrCreate(
            ['candidate_id' => $candidate->id, 'vacancy_id' => $data['vacancy_id']],
            ['source' => 'matched', 'stage' => 'shortlisted', 'consented_at' => now()],
        );

        AuditLog::record('application.created', $application, $candidate->name.' put forward for '.$application->vacancy->title);

        return back()->with('status', $candidate->name.' added to the shortlist.');
    }

    public function update(Request $request, Application $application)
    {
        $data = $request->validate(['stage' => ['required', Rule::in(array_keys(config('hdt.stages')))]]);

        $application->update($data);

        if ($data['stage'] === 'placed') {
            $application->candidate->forceFill(['status' => 'placed', 'last_contact_at' => now()])->save();
        }

        AuditLog::record('application.stage', $application, $application->candidate->name.' → '.$application->stageLabel().' for '.$application->vacancy->title);

        return back()->with('status', 'Stage updated.');
    }
}
