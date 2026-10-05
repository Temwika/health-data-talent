<?php

namespace App\Filament\Widgets;

use App\Models\Application;
use App\Models\Candidate;
use App\Models\Organisation;
use App\Models\Vacancy;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('Candidates', Candidate::count()),
            Stat::make('Employers', Organisation::count()),
            Stat::make('Live jobs', Vacancy::live()->count()),
            Stat::make('Applications', Application::count()),
            Stat::make('Placements', Application::where('stage', 'placed')->count()),
        ];
    }
}
