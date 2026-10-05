<?php

namespace App\Filament\Resources\CandidateResource\RelationManagers;

use App\Models\AuditLog;
use App\Models\Application;
use App\Models\Vacancy;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ApplicationsRelationManager extends RelationManager
{
    protected static string $relationship = 'applications';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('stage')
                ->options(config('hdt.stages'))
                ->required(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('vacancy.title'),
                Tables\Columns\TextColumn::make('stage')
                    ->formatStateUsing(fn (string $state): string => config('hdt.stages')[$state] ?? $state)
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'placed' => 'success',
                        'offer' => 'warning',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('source'),
                Tables\Columns\TextColumn::make('created_at')->date('d M Y'),
            ])
            ->headerActions([
                Tables\Actions\Action::make('putForward')
                    ->label('Put forward for vacancy')
                    ->form([
                        Forms\Components\Select::make('vacancy_id')
                            ->label('Vacancy')
                            ->options(Vacancy::whereIn('status', ['live', 'pending'])->pluck('title', 'id'))
                            ->required(),
                        Forms\Components\Checkbox::make('consent')
                            ->label('The candidate has agreed to be put forward for this role')
                            ->required()
                            ->accepted(),
                    ])
                    ->action(function (array $data): void {
                        $candidate = $this->getOwnerRecord();
                        $application = Application::firstOrCreate(
                            ['candidate_id' => $candidate->id, 'vacancy_id' => $data['vacancy_id']],
                            ['source' => 'matched', 'stage' => 'shortlisted', 'consented_at' => now()],
                        );
                        AuditLog::record('application.created', $application, $candidate->name.' put forward for '.$application->vacancy->title);
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->after(fn (Application $record) => AuditLog::record(
                        'application.stage', $record,
                        $record->candidate->name.' → '.$record->stageLabel().' for '.$record->vacancy->title
                    )),
            ]);
    }
}
