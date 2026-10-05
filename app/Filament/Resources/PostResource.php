<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;
    protected static ?string $navigationIcon = 'heroicon-o-newspaper';
    protected static ?string $navigationLabel = 'Insights';
    protected static ?int $navigationSort = 6;

    public static function canDelete(Model $record): bool { return auth()->user()->isAdmin(); }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('title')->required()->maxLength(140)->columnSpanFull(),
            Forms\Components\Select::make('category')
                ->options(array_combine(config('hdt.post_categories'), config('hdt.post_categories')))
                ->required(),
            Forms\Components\Toggle::make('published')->label('Published'),
            Forms\Components\Textarea::make('excerpt')->required()->maxLength(300)->rows(3)->columnSpanFull(),
            Forms\Components\MarkdownEditor::make('body')->required()->maxLength(20000)->columnSpanFull(),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('category')->badge()->color('gray'),
                Tables\Columns\IconColumn::make('published_at')
                    ->label('Published')
                    ->boolean()
                    ->getStateUsing(fn (Post $record): bool => $record->published_at !== null),
                Tables\Columns\TextColumn::make('created_at')->label('Created')->date('d M Y')->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(fn (Post $record) => \App\Models\AuditLog::record('post.deleted', null, 'Deleted article '.$record->title)),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit'   => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
