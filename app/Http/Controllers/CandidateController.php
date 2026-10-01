<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Candidate;
use App\Models\Consent;
use App\Models\Vacancy;
use App\Support\CvStore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CandidateController extends Controller
{
    public function create(Request $request)
    {
        $vacancy = $request->query('job')
            ? Vacancy::live()->where('slug', $request->query('job'))->first()
            : null;

        $track = $vacancy
            ? ($vacancy->area === 'doctor' ? 'doctor' : 'data')
            : ($request->query('track') === 'doctor' ? 'doctor' : 'data');

        return view('pages.candidates', ['vacancy' => $vacancy, 'track' => $track]);
    }

    public function store(Request $request, CvStore $cvStore)
    {
        if ($request->filled('website')) {
            return redirect()->route('candidates.create')->with('status', 'Welcome to the network. We\'ll be in touch when a role matches your experience.');
        }

        $data = $request->validate([
            'track' => ['required', Rule::in(['data', 'doctor'])],
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:160'],
            'phone' => ['nullable', 'regex:/^[+()\d\s-]{7,25}$/'],
            'location' => ['required', 'string', 'max:80'],
            'current_title' => ['required', 'string', 'max:100'],
            'years' => ['required', Rule::in(config('hdt.years'))],
            'qualification' => ['nullable', Rule::in(config('hdt.qualifications'))],
            'specialty' => ['nullable', 'required_if:track,doctor', 'string', 'max:100'],
            'registration' => ['nullable', 'string', 'max:80'],
            'expertise' => ['nullable', 'array'],
            'expertise.*' => [Rule::in(config('hdt.expertise'))],
            'tools' => ['nullable', 'array'],
            'tools.*' => [Rule::in(config('hdt.tools'))],
            'ngo_work' => ['nullable', 'array'],
            'ngo_work.*' => [Rule::in(config('hdt.ngo_work'))],
            'desired_role' => ['required', 'string', 'max:100'],
            'pattern' => ['nullable', Rule::in(config('hdt.candidate_patterns'))],
            'salary' => ['nullable', 'string', 'max:40'],
            'availability' => ['nullable', Rule::in(config('hdt.availability'))],
            'right_to_work' => ['nullable', Rule::in(config('hdt.right_to_work'))],
            // Extension here; CvStore then checks the file's leading bytes match that type.
            'cv' => ['nullable', 'file', 'extensions:pdf,doc,docx', 'max:'.config('hdt.cv_max_kb')],
            'job' => ['nullable', 'string', 'max:160'],
            'privacy' => ['accepted'],
        ], [
            'phone.regex' => 'Enter a phone number using digits, spaces and +.',
            'specialty.required_if' => 'Tell us your specialty.',
            'cv.extensions' => 'Upload a PDF or Word document.',
            'cv.max' => 'This file is larger than 5 MB. Please upload a smaller version.',
            'privacy.accepted' => 'Please confirm to continue.',
        ]);

        // Keep only the fields that belong to the chosen track.
        if ($data['track'] === 'doctor') {
            $data['expertise'] = $data['tools'] = null;
        } else {
            $data['specialty'] = $data['registration'] = $data['ngo_work'] = null;
        }
        $data['marketing_opt_in'] = $request->boolean('marketing');

        $cv = $request->hasFile('cv') ? $cvStore->store($request->file('cv')) : null;

        try {
            DB::transaction(function () use ($data, $cv, $request) {
                $candidate = new Candidate($data);
                if ($cv) {
                    $candidate->cv_path = $cv['path'];
                    $candidate->cv_original_name = $cv['name'];
                    $candidate->cv_size = $cv['size'];
                    $candidate->cv_scan_status = $cv['scan'];
                }
                $candidate->save();

                Consent::record($candidate, 'privacy_notice', $request);
                if ($candidate->marketing_opt_in) {
                    Consent::record($candidate, 'job_alerts', $request);
                }

                $vacancy = empty($data['job']) ? null : Vacancy::live()->where('slug', $data['job'])->first();
                if ($vacancy) {
                    Application::create([
                        'candidate_id' => $candidate->id,
                        'vacancy_id' => $vacancy->id,
                        'source' => 'applied',
                        'consented_at' => now(),
                    ]);
                }
            });
        } catch (\Throwable $e) {
            // Don't leave an orphaned CV behind if the profile could not be saved.
            if ($cv) {
                Storage::disk('local')->delete($cv['path']);
            }
            throw $e;
        }

        return redirect()->route('candidates.create')->with(
            'status',
            'Welcome to the network, '.Str::before($data['name'], ' ').'. We\'ll be in touch when a role matches your experience.'
        );
    }
}
