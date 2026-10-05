<?php

namespace App\Filament\Resources\CandidateResource\Pages;

use App\Filament\Resources\CandidateResource;
use App\Models\AuditLog;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditCandidate extends EditRecord
{
    protected static string $resource = CandidateResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()
            ->before(fn () => AuditLog::record('candidate.deleted', null, 'Deleted profile and CV of '.$this->record->name))];
    }

    public function mount(string|int $record): void
    {
        parent::mount($record);
        AuditLog::record('candidate.viewed', $this->record, 'Viewed profile of '.$this->record->name);
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $contacted = $data['contacted'] ?? false;
        unset($data['contacted']);

        $record->fill($data);
        if ($contacted) {
            $record->last_contact_at = now();
        }
        $record->save();

        AuditLog::record('candidate.updated', $record, 'Updated '.$record->name.' (status: '.$record->status.')');

        return $record;
    }
}
