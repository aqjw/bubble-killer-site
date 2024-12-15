<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MangaResource\Pages;
use App\Filament\Resources\MangaResource\RelationManagers;
use App\Models\Manga;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MangaResource extends Resource
{
    protected static ?string $model = Manga::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required(),
                Forms\Components\TextInput::make('slug_mangalib'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID'),
                Tables\Columns\TextColumn::make('title')
                    ->label('Название'),
                Tables\Columns\TextColumn::make('slug_mangalib')
                    ->label('MangaLib'),
                Tables\Columns\TextColumn::make('chapters_count')
                    ->counts('chapters')
                    ->label('Кол-во глав'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMangas::route('/'),
            'create' => Pages\CreateManga::route('/create'),
            'edit' => Pages\EditManga::route('/{record}/edit'),
        ];
    }
}
