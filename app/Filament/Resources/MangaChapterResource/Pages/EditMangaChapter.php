<?php

namespace App\Filament\Resources\MangaChapterResource\Pages;

use App\Filament\Resources\MangaChapterResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMangaChapter extends EditRecord
{
    protected static string $resource = MangaChapterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
