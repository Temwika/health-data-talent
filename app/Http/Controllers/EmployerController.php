<?php

namespace App\Http\Controllers;

use App\Models\Consent;
use App\Models\Organisation;
use App\Models\Vacancy;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class EmployerController extends Controller
{
    public function create(Request $request)
    {
        return view('pages.employers', [
            'presetService' => $request->query('service') === 'ngo-doctor' ? 'ngo-doctor' : null,
        ]);
    }

    public function store(Request $request)
    {
        $thanks = 'Your registration has been received. We\'ll be in touch within one working day.';

        // Honeypot: bots fill the hidden field. Answer as if it worked and store nothing.
        if ($request->filled('website')) {
            return redirect()->route('employers.create')->with('status', $thanks);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'sector' => ['required', Rule::in(config('hdt.sectors'))],
            'contact_name' => ['required', 'string', 'max:100'],
            'contact_title' => ['nullable', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:160'],
            'phone' => ['nullable', 'regex:/^[+()\d\s-]{7,25}$/'],
            'service' => ['required', Rule::in(array_keys(config('hdt.services')))],
            'message' => ['nullable', 'string', 'max:2000'],
            'privacy' => ['accepted'],
        ], [
            'phone.regex' => 'Enter a phone number using digits, spaces and +.',
            'privacy.accepted' => 'Please confirm to continue.',
        ]);

        $organisation = Organisation::create($data);
        Consent::record($organisation, 'privacy_notice', $request);

        return redirect()->route('employers.create')
            ->with('status', 'Thanks, '.Str::before($data['contact_name'], ' ').'. '.$thanks);
    }

    public function createVacancy()
    {
        return view('pages.vacancy');
    }

    public function storeVacancy(Request $request)
    {
        $thanks = 'Vacancy submitted. We\'ll review it and confirm terms with you before it goes live on the jobs board.';

        if ($request->filled('website')) {
            return redirect()->route('vacancies.create')->with('status', $thanks);
        }

        $data = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'organisation_name' => ['required', 'string', 'max:120'],
            'location' => ['required', 'string', 'max:80'],
            'pattern' => ['required', Rule::in(config('hdt.patterns'))],
            'salary' => ['required', 'string', 'max:60'],
            'contract_type' => ['required', Rule::in(config('hdt.contract_types'))],
            'skills' => ['required', 'string', 'max:300'],
            'description' => ['required', 'string', 'max:4000'],
            'contact_name' => ['required', 'string', 'max:100'],
            'contact_email' => ['required', 'email', 'max:160'],
            'privacy' => ['accepted'],
        ], [
            'privacy.accepted' => 'Please confirm to continue.',
        ]);

        // Always created as "pending": nothing reaches the jobs board without staff approval.
        $vacancy = Vacancy::create($data);
        Consent::record($vacancy, 'privacy_and_terms', $request);

        return redirect()->route('vacancies.create')->with('status', $thanks);
    }
}
