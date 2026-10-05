<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Filament\Resources\PostResource;
use App\Models\AuditLog;
use Filament\Resources\Pages\CreateRecord;

class CreatePost extends CreateRecord
{
    protected static string $resource = PostResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['published_at'] = ($data['published'] ?? false) ? now() : null;
        unset($data['published']);
        return $data;
    }

    protected function afterCreate(): void
    {
        AuditLog::record('post.created', $this->record, 'Created article '.$this->record->title);
    }
}
