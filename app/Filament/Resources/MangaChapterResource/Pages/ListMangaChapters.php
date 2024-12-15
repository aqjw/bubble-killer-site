<?php

namespace App\Filament\Resources\MangaChapterResource\Pages;

use App\Filament\Resources\MangaChapterResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMangaChapters extends ListRecords
{
    protected static string $resource = MangaChapterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
