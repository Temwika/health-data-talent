<?php

namespace App\Filament\Resources\VacancyResource\Pages;

use App\Filament\Resources\VacancyResource;
use App\Models\AuditLog;
use Filament\Resources\Pages\CreateRecord;

class CreateVacancy extends CreateRecord
{
    protected static string $resource = VacancyResource::class;

    protected function afterCreate(): void
    {
        AuditLog::record('vacancy.created', $this->record, 'Created vacancy '.$this->record->title);
    }
}
