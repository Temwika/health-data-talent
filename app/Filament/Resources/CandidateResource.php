<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CandidateResource\Pages;
use App\Filament\Resources\CandidateResource\RelationManagers;
use App\Models\Candidate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class CandidateResource extends Resource
{
    protected static ?string $model = Candidate::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?int $navigationSort = 1;
    protected static ?string $recordTitleAttribute = 'name';

    public static function canCreate(): bool { return false; }
    public static function canDelete(Model $record): bool { return auth()->user()->isAdmin(); }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Update')->schema([
                Forms\Components\Select::make('status')
                    ->options(config('hdt.candidate_statuses'))
                    ->required(),
                Forms\Components\Textarea::make('notes')->rows(4)->maxLength(5000),
                Forms\Components\Toggle::make('contacted')
                    ->label('Mark as contacted today')
                    ->helperText('Resets the retention clock.')
                    ->default(false),
            ]),

            Forms\Components\Section::make('Profile')->schema([
                Forms\Components\Placeholder::make('track')
                    ->content(fn (Candidate $record): string => $record->trackLabel()),
                Forms\Components\Placeholder::make('name')
                    ->content(fn (Candidate $record): string => $record->name),
                Forms\Components\Placeholder::make('email')
                    ->content(fn (Candidate $record): string => $record->email),
                Forms\Components\Placeholder::make('location')
                    ->content(fn (Candidate $record): ?string => $record->location),
                Forms\Components\Placeholder::make('current_title')->label('Current role')
                    ->content(fn (Candidate $record): ?string => $record->current_title),
                Forms\Components\Placeholder::make('availability')
                    ->content(fn (Candidate $record): ?string => $record->availability),
                Forms\Components\Placeholder::make('salary')->label('Salary expectation')
                    ->content(fn (Candidate $record): ?string => $record->salary),
                Forms\Components\Placeholder::make('right_to_work')
                    ->content(fn (Candidate $record): ?string => $record->right_to_work),
                Forms\Components\Placeholder::make('skills')->label('Skills / expertise')
                    ->content(fn (Candidate $record): string => implode(', ', $record->skillList())),
                Forms\Components\Placeholder::make('last_contact_at')->label('Last contacted')
                    ->content(fn (Candidate $record): string => $record->last_contact_at?->format('d M Y') ?? '—'),
            ])->columns(2)->collapsible(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('email')->searchable(),
                Tables\Columns\TextColumn::make('track')
                    ->formatStateUsing(fn (string $state): string => $state === 'doctor' ? 'Doctor' : 'Health data')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'doctor' ? 'success' : 'info'),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'new' => 'warning',
                        'screened', 'active' => 'info',
                        'placed' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('current_title')->label('Role')->searchable(),
                Tables\Columns\TextColumn::make('location')->searchable(),
                Tables\Columns\TextColumn::make('created_at')->label('Registered')
                    ->date('d M Y')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('track')
                    ->options(['data' => 'Health data', 'doctor' => 'Doctor (NGO)']),
                Tables\Filters\SelectFilter::make('status')
                    ->options(config('hdt.candidate_statuses')),
            ])
            ->actions([
                Tables\Actions\Action::make('downloadCv')
                    ->label('CV')->icon('heroicon-o-arrow-down-tray')->color('gray')
                    ->url(fn (Candidate $record): string => route('admin.candidates.cv', $record))
                    ->visible(fn (Candidate $record): bool => $record->cv_path !== null),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(fn (Candidate $record) => \App\Models\AuditLog::record(
                        'candidate.deleted', null, 'Deleted profile and CV of '.$record->name
                    )),
            ])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make()
                    ->visible(fn (): bool => auth()->user()->isAdmin()),
            ])])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelationManagers(): array
    {
        return [RelationManagers\ApplicationsRelationManager::class];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCandidates::route('/'),
            'edit'  => Pages\EditCandidate::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'email', 'current_title'];
    }
}
