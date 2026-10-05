<?php

namespace App\Filament\Resources\VacancyResource\Pages;

use App\Filament\Resources\VacancyResource;
use App\Models\AuditLog;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVacancy extends EditRecord
{
    protected static string $resource = VacancyResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()
            ->before(fn () => AuditLog::record('vacancy.deleted', null, 'Deleted vacancy '.$this->record->title))];
    }

    protected function afterSave(): void
    {
        AuditLog::record('vacancy.updated', $this->record, 'Edited vacancy '.$this->record->title);
    }
}
