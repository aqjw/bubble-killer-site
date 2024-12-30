<?php

namespace App\Filament\Resources\MangaChapterResource\Pages;

use App\Enums\MangaChapterStatus;
use App\Filament\Resources\MangaChapterResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListMangaChapters extends ListRecords
{
    protected static string $resource = MangaChapterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return collect([
            MangaChapterStatus::ImageFiltering,
            MangaChapterStatus::MaskVerification,
            MangaChapterStatus::ClearVerification,
            MangaChapterStatus::FrameMaskVerification,
            MangaChapterStatus::Finalized,
        ])
            ->mapWithKeys(fn (MangaChapterStatus $status) => [
                $status->getLabel() => Tab::make()
                    ->modifyQueryUsing(fn (Builder $query) => $query->where('status', $status->value))
            ])
            ->prepend(Tab::make(), 'all')
            ->toArray();
    }
}
