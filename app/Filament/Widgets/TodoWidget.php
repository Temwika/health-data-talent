<?php

namespace App\Filament\Widgets;

use App\Models\Candidate;
use App\Models\Enquiry;
use App\Models\Organisation;
use App\Models\Vacancy;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TodoWidget extends BaseWidget
{
    protected static ?int $sort = 2;
    protected static ?string $heading = 'Action required';

    protected function getStats(): array
    {
        return [
            Stat::make('Vacancies to review', Vacancy::where('status', 'pending')->count())->color('warning'),
            Stat::make('Employers to approve', Organisation::where('status', 'pending')->count())->color('warning'),
            Stat::make('New candidates', Candidate::where('status', 'new')->count())->color('info'),
            Stat::make('Open enquiries', Enquiry::whereNull('handled_at')->count())->color('danger'),
        ];
    }
}
