<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrganisationResource\Pages;
use App\Models\AuditLog;
use App\Models\Organisation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class OrganisationResource extends Resource
{
    protected static ?string $model = Organisation::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    protected static ?string $navigationLabel = 'Employers';
    protected static ?string $modelLabel = 'Employer';
    protected static ?int $navigationSort = 2;

    public static function canCreate(): bool { return false; }
    public static function canDelete(Model $record): bool { return auth()->user()->isAdmin(); }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('status')
                ->options(['pending' => 'Pending', 'approved' => 'Approved', 'declined' => 'Declined'])
                ->required(),
            Forms\Components\Section::make('Details')->schema([
                Forms\Components\Placeholder::make('name')->content(fn (Organisation $record): string => $record->name),
                Forms\Components\Placeholder::make('sector')->content(fn (Organisation $record): ?string => $record->sector),
                Forms\Components\Placeholder::make('contact_name')->content(fn (Organisation $record): ?string => $record->contact_name),
                Forms\Components\Placeholder::make('email')->content(fn (Organisation $record): ?string => $record->email),
                Forms\Components\Placeholder::make('service')->label('Service requested')
                    ->content(fn (Organisation $record): string => $record->serviceLabel()),
                Forms\Components\Placeholder::make('message')
                    ->content(fn (Organisation $record): ?string => $record->message)
                    ->columnSpanFull(),
            ])->columns(2)->collapsible(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('sector'),
                Tables\Columns\TextColumn::make('email')->searchable(),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'approved' => 'success',
                        'pending' => 'warning',
                        default => 'danger',
                    }),
                Tables\Columns\TextColumn::make('created_at')->label('Submitted')->date('d M Y')->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('Approve')->color('success')->icon('heroicon-o-check')
                    ->visible(fn (Organisation $record): bool => $record->status !== 'approved')
                    ->action(function (Organisation $record): void {
                        $record->status = 'approved';
                        $record->save();
                        AuditLog::record('employer.approved', $record, $record->name.' approved');
                        Notification::make()->title('Employer approved')->success()->send();
                    }),
                Tables\Actions\EditAction::make()
                    ->after(fn (Organisation $record) => AuditLog::record('employer.updated', $record, $record->name.' updated')),
                Tables\Actions\DeleteAction::make()
                    ->before(fn (Organisation $record) => AuditLog::record('employer.deleted', null, 'Deleted employer '.$record->name)),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrganisations::route('/'),
            'edit'  => Pages\EditOrganisation::route('/{record}/edit'),
        ];
    }
}
