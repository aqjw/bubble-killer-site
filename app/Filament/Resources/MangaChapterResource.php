<?php

namespace App\Filament\Resources;

use App\Enums\MangaChapterStatus;
use App\Filament\Resources\MangaChapterResource\Pages;
use App\Filament\Resources\MangaChapterResource\RelationManagers;
use App\Models\MangaChapter;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MangaChapterResource extends Resource
{
    protected static ?string $model = MangaChapter::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('manga_id')
                    ->label('Манхва')
                    ->relationship(name: 'manga', titleAttribute: 'title')
                    ->required(),
                Forms\Components\Group::make([
                    Forms\Components\TextInput::make('volume')
                        ->label('Том')
                        ->default(1)
                        ->required(),
                    Forms\Components\TextInput::make('number')
                        ->label('Глава')
                        ->required(),
                ])->columns(2)
                // Forms\Components\Select::make('status')
                //     ->options(MangaChapterStatus::class)
                //     ->default(MangaChapterStatus::Pending)
                //     ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID'),
                Tables\Columns\TextColumn::make('manga.title')
                    ->label('Манхва'),
                Tables\Columns\TextColumn::make('volume')
                    ->label('Том'),
                Tables\Columns\TextColumn::make('number')
                    ->label('Глава'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Статус')
                    ->badge(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('filter-images')
                    ->label('Filter Images')
                    ->visible(fn ($record) => MangaChapterStatus::ImageFiltering->is($record->status))
                    ->action(fn ($record) => redirect(self::getUrl('filter-images', ['record' => $record]))),

                Tables\Actions\Action::make('mask-verification')
                    ->label('Mask Verification')
                    ->visible(fn ($record) => MangaChapterStatus::MaskVerification->is($record->status))
                    ->action(fn ($record) => redirect(self::getUrl('mask-verification', ['record' => $record]))),

                Tables\Actions\Action::make('clear-verification')
                    ->label('Clear Verification')
                    ->visible(fn ($record) => MangaChapterStatus::ClearVerification->is($record->status))
                    ->action(fn ($record) => redirect(self::getUrl('clear-verification', ['record' => $record]))),

                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                ])
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
            'index' => Pages\ListMangaChapters::route('/'),
            'create' => Pages\CreateMangaChapter::route('/create'),
            'edit' => Pages\EditMangaChapter::route('/{record}/edit'),
            'filter-images' => Pages\FilterImagesMangaChapter::route('/{record}/filtering'),
            'mask-verification' => Pages\MaskVerificationMangaChapter::route('/{record}/mask-verification'),
            'clear-verification' => Pages\ClearVerificationMangaChapter::route('/{record}/clear-verification'),
        ];
    }
}
