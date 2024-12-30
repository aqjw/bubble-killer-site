<?php

namespace App\Filament\Resources\MangaChapterResource\Pages;

use App\Filament\Resources\MangaChapterResource;
use App\Jobs\Process\RemoveBubblesJob;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MaskVerificationMangaChapter extends Page
{
    use InteractsWithRecord;

    protected static string $resource = MangaChapterResource::class;

    protected static string $view = 'filament.resources.manga-chapter-resource.pages.mask-verification';

    public function getHeading(): string|Htmlable
    {
        return "Том {$this->record->volume} - Глава {$this->record->number}";
    }

    public function getSubheading(): string|Htmlable|null
    {
        return $this->record->manga->title;
    }

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);
    }

    public function getItemsProperty(): array
    {
        $images = $this->record
            ->media()
            ->where('collection_name', 'split')
            ->where('custom_properties->bubble', true)
            ->get();

        $imageNames = $images->pluck('name');

        $masks = $this->record
            ->media()
            ->where('collection_name', 'bubble_mask')
            ->whereIn('name', $imageNames)
            ->get()
            ->keyBy('name');

        return $images->map(function ($image) use ($masks) {
            $mask = $masks->get($image->name);
            return ['image' => $image, 'mask' => $mask];
        })->toArray();
    }

    public function getMaxContentWidth(): MaxWidth
    {
        return MaxWidth::Full;
    }

    public function markDoneAction(): Action
    {
        return Action::make('markDone')
            ->label('Готово')
            ->requiresConfirmation()
            ->action(function () {
                $this->record->update(['status' => \App\Enums\MangaChapterStatus::BubbleRemoval]);
                // TODO: uncomment original
                // dispatch(new RemoveBubblesJob(mangaChapterId: $this->record->id));
    
                Notification::make()
                    ->success()
                    ->title('Картинки отправлены на обработку!')
                    ->send();

                $this->redirect(MangaChapterResource::getUrl('index'));
            });
    }
}
