<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Candidate;
use App\Models\Vacancy;
use App\Support\Matcher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class CandidateController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q'));
        $track = $request->query('track');

        $candidates = Candidate::query()
            ->when($q !== '', function ($query) use ($q) {
                $like = '%'.addcslashes($q, '%_\\').'%';
                $query->where(fn ($w) => $w
                    ->whereLike('name', $like)
                    ->orWhereLike('email', $like)
                    ->orWhereLike('current_title', $like)
                    ->orWhereLike('location', $like));
            })
            ->when(in_array($track, ['data', 'doctor'], true), fn ($query) => $query->where('track', $track))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.candidates.index', compact('candidates', 'q', 'track'));
    }

    public function show(Candidate $candidate)
    {
        AuditLog::record('candidate.viewed', $candidate, 'Viewed profile of '.$candidate->name);

        $candidate->load(['applications.vacancy', 'consents']);
        $applied = $candidate->applications->pluck('vacancy_id');

        $suggested = Vacancy::whereIn('status', ['live', 'pending'])
            ->whereNotIn('id', $applied)
            ->where('area', $candidate->isDoctor() ? '=' : '!=', 'doctor')
            ->get()
            ->map(fn (Vacancy $v) => ['vacancy' => $v, 'score' => Matcher::score($candidate, $v)])
            ->filter(fn ($m) => $m['score'] > 0)
            ->sortByDesc('score')
            ->take(5)
            ->values();

        return view('admin.candidates.show', compact('candidate', 'suggested'));
    }

    public function update(Request $request, Candidate $candidate)
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(config('hdt.candidate_statuses')))],
            'notes' => ['nullable', 'string', 'max:5000'],
            'contacted' => ['nullable', 'boolean'],
        ]);

        $candidate->status = $data['status'];
        $candidate->notes = $data['notes'] ?? null;
        if ($request->boolean('contacted')) {
            // Meaningful contact restarts the retention clock.
            $candidate->last_contact_at = now();
        }
        $candidate->save();

        AuditLog::record('candidate.updated', $candidate, 'Updated '.$candidate->name.' (status: '.$candidate->status.')');

        return back()->with('status', 'Profile updated.');
    }

    public function cv(Candidate $candidate)
    {
        abort_unless($candidate->cv_path && Storage::disk('local')->exists($candidate->cv_path), 404);

        AuditLog::record('cv.downloaded', $candidate, 'Downloaded CV of '.$candidate->name);

        return Storage::disk('local')->download($candidate->cv_path, $candidate->cv_original_name, [
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'no-store, private',
        ]);
    }

    public function destroy(Candidate $candidate)
    {
        $name = $candidate->name;
        $candidate->delete(); // model hook removes the CV file and consent records

        AuditLog::record('candidate.deleted', null, 'Deleted profile and CV of '.$name);

        return redirect()->route('admin.candidates.index')->with('status', 'Profile and CV permanently deleted.');
    }
}
