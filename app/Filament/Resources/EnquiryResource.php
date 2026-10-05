<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EnquiryResource\Pages;
use App\Models\Enquiry;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EnquiryResource extends Resource
{
    protected static ?string $model = Enquiry::class;
    protected static ?string $navigationIcon = 'heroicon-o-envelope';
    protected static ?string $navigationLabel = 'Enquiries';
    protected static ?int $navigationSort = 5;

    public static function canCreate(): bool { return false; }
    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool { return false; }

    public static function form(Form $form): Form { return $form->schema([]); }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('email')->searchable(),
                Tables\Columns\TextColumn::make('organisation')->searchable(),
                Tables\Columns\TextColumn::make('subject'),
                Tables\Columns\IconColumn::make('handled_at')
                    ->label('Handled')
                    ->boolean()
                    ->getStateUsing(fn (Enquiry $record): bool => $record->handled_at !== null),
                Tables\Columns\TextColumn::make('created_at')->label('Received')->date('d M Y')->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('handled')
                    ->attribute('handled_at')
                    ->nullable()
                    ->trueLabel('Handled')
                    ->falseLabel('Open'),
            ])
            ->actions([
                Tables\Actions\Action::make('toggle')
                    ->label(fn (Enquiry $record): string => $record->handled_at ? 'Reopen' : 'Mark handled')
                    ->icon(fn (Enquiry $record): string => $record->handled_at ? 'heroicon-o-arrow-uturn-left' : 'heroicon-o-check')
                    ->action(function (Enquiry $record): void {
                        $record->handled_at = $record->handled_at ? null : now();
                        $record->save();
                        Notification::make()
                            ->title($record->handled_at ? 'Marked as handled.' : 'Reopened.')
                            ->success()->send();
                    }),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEnquiries::route('/'),
        ];
    }
}
