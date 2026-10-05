<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Filament\Resources\PostResource;
use App\Models\AuditLog;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPost extends EditRecord
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()
            ->before(fn () => AuditLog::record('post.deleted', null, 'Deleted article '.$this->record->title))];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['published'] = $this->record->published_at !== null;
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['published_at'] = ($data['published'] ?? false)
            ? ($this->record->published_at ?? now())
            : null;
        unset($data['published']);
        return $data;
    }

    protected function afterSave(): void
    {
        AuditLog::record('post.updated', $this->record, 'Edited article '.$this->record->title);
    }
}
