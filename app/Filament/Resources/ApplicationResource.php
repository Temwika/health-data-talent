<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ApplicationResource\Pages;
use App\Models\AuditLog;
use App\Models\Application;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ApplicationResource extends Resource
{
    protected static ?string $model = Application::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?int $navigationSort = 4;

    public static function canCreate(): bool { return false; }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('stage')
                ->options(config('hdt.stages'))
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('candidate.name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('vacancy.title')->searchable(),
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
                Tables\Columns\TextColumn::make('created_at')->label('Created')->date('d M Y')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('stage')->options(config('hdt.stages')),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->after(function (Application $record): void {
                        if ($record->stage === 'placed') {
                            $record->candidate->forceFill(['status' => 'placed', 'last_contact_at' => now()])->save();
                        }
                        AuditLog::record('application.stage', $record,
                            $record->candidate->name.' → '.$record->stageLabel().' for '.$record->vacancy->title);
                    }),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListApplications::route('/'),
        ];
    }
}
