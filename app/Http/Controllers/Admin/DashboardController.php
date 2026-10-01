<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Candidate;
use App\Models\Enquiry;
use App\Models\Organisation;
use App\Models\Vacancy;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $stageCounts = Application::selectRaw('stage, count(*) as total')->groupBy('stage')->pluck('total', 'stage');

        return view('admin.dashboard', [
            'stats' => [
                ['label' => 'Candidates', 'value' => Candidate::count(), 'route' => 'admin.candidates.index'],
                ['label' => 'Employers', 'value' => Organisation::count(), 'route' => 'admin.employers.index'],
                ['label' => 'Live jobs', 'value' => Vacancy::live()->count(), 'route' => 'admin.vacancies.index'],
                ['label' => 'Applications', 'value' => Application::count(), 'route' => 'admin.applications.index'],
                ['label' => 'Placements', 'value' => $stageCounts['placed'] ?? 0, 'route' => 'admin.applications.index'],
            ],
            'todo' => [
                ['label' => 'Vacancies to review', 'value' => Vacancy::where('status', 'pending')->count(), 'route' => 'admin.vacancies.index'],
                ['label' => 'Employers to approve', 'value' => Organisation::where('status', 'pending')->count(), 'route' => 'admin.employers.index'],
                ['label' => 'New candidates', 'value' => Candidate::where('status', 'new')->count(), 'route' => 'admin.candidates.index'],
                ['label' => 'Open enquiries', 'value' => Enquiry::whereNull('handled_at')->count(), 'route' => 'admin.enquiries.index'],
            ],
            'stageCounts' => $stageCounts,
            'recentCandidates' => Candidate::latest()->take(5)->get(),
            'retentionDue' => Candidate::where('last_contact_at', '<', now()->subMonths(config('hdt.retention_months') - 1))->count(),
        ]);
    }
}
