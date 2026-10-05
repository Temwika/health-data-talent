<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VacancyResource\Pages;
use App\Models\AuditLog;
use App\Models\Candidate;
use App\Models\Vacancy;
use App\Support\Matcher;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class VacancyResource extends Resource
{
    protected static ?string $model = Vacancy::class;
    protected static ?string $navigationIcon = 'heroicon-o-briefcase';
    protected static ?int $navigationSort = 3;

    public static function canDelete(Model $record): bool { return auth()->user()->isAdmin(); }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('title')->required()->maxLength(120)->columnSpanFull(),
            Forms\Components\TextInput::make('organisation_name')->label('Organisation')->required()->maxLength(120),
            Forms\Components\TextInput::make('location')->required()->maxLength(80),
            Forms\Components\Select::make('pattern')->options(array_combine(config('hdt.patterns'), config('hdt.patterns')))->required(),
            Forms\Components\TextInput::make('salary')->required()->maxLength(60),
            Forms\Components\Select::make('contract_type')->options(array_combine(config('hdt.contract_types'), config('hdt.contract_types')))->required(),
            Forms\Components\Select::make('area')->options(config('hdt.areas'))->required(),
            Forms\Components\TextInput::make('skills')->required()->maxLength(300)->columnSpanFull(),
            Forms\Components\Textarea::make('description')->required()->maxLength(4000)->rows(8)->columnSpanFull(),
            Forms\Components\TextInput::make('contact_name')->maxLength(100),
            Forms\Components\TextInput::make('contact_email')->email()->maxLength(160),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('organisation_name')->label('Organisation')->searchable(),
                Tables\Columns\TextColumn::make('area')
                    ->formatStateUsing(fn (string $state): string => config('hdt.areas')[$state] ?? $state)
                    ->badge()->color('gray'),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'live' => 'success',
                        'pending' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('applications_count')->label('Apps')
                    ->counts('applications')->sortable(),
                Tables\Columns\TextColumn::make('published_at')->label('Published')->date('d M Y')->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('Approve')->color('success')->icon('heroicon-o-check')
                    ->visible(fn (Vacancy $record): bool => $record->status === 'pending')
                    ->action(function (Vacancy $record): void {
                        $record->status = 'live';
                        $record->published_at ??= now();
                        $record->save();
                        AuditLog::record('vacancy.live', $record, $record->title.' approved');
                        Notification::make()->title('Vacancy approved and live')->success()->send();
                    }),
                Tables\Actions\Action::make('close')
                    ->label('Close')->color('gray')->icon('heroicon-o-x-mark')
                    ->visible(fn (Vacancy $record): bool => $record->status === 'live')
                    ->requiresConfirmation()
                    ->action(function (Vacancy $record): void {
                        $record->status = 'closed';
                        $record->save();
                        AuditLog::record('vacancy.closed', $record, $record->title.' closed');
                        Notification::make()->title('Vacancy closed')->send();
                    }),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(fn (Vacancy $record) => AuditLog::record('vacancy.deleted', null, 'Deleted vacancy '.$record->title)),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListVacancies::route('/'),
            'create' => Pages\CreateVacancy::route('/create'),
            'edit'   => Pages\EditVacancy::route('/{record}/edit'),
        ];
    }
}
