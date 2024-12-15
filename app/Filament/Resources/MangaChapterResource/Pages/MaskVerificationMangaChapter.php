<?php

namespace App\Filament\Resources\MangaChapterResource\Pages;

use App\Filament\Resources\MangaChapterResource;
use App\Jobs\Process\CreateBubbleMasksJob;
use App\Jobs\Process\CreateMasksJob;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MaskVerificationMangaChapter extends Page
{
    use InteractsWithRecord;

    protected static string $resource = MangaChapterResource::class;

    protected static string $view = 'filament.resources.manga-chapter-resource.pages.mask-verification';

    public array $data = [];


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

        /** @var Media $media */
        foreach ($this->images as $media) {
            $this->data[$media->id] = [
                'musk' => null,
            ];
        }
    }

    public function getMaskUrl(string $image_name): string
    {
        return $this->record
            ->media()
            ->where('collection_name', 'bubble_mask')
            ->where('name', $image_name)
            ->first()
            ->getUrl();
    }

    public function getImagesProperty()
    {
        return $this->record
            ->media()
            ->where('collection_name', 'split')
            ->where('custom_properties->bubble', true)
            ->get();
    }

    public function hasUnsavedChanges(): bool
    {
        foreach ($this->data as $image_data) {
            if (! empty($image_data['mask'])) {
                return true;
            }
        }

        return false;
    }

    public function markDoneAction(): Action
    {
        return Action::make('markDone')
            ->label('Готово')
            ->requiresConfirmation()
            ->disabled($this->hasUnsavedChanges())
            ->action(fn () => $this->markDone());
    }

    public function saveMask(Media $media, $base64): void
    {
        // Найти существующую маску для текущего media
        $mask_media = $this->record
            ->media()
            ->where('collection_name', 'bubble_mask')
            ->where('name', $media->name)
            ->first();

        if ($mask_media) {
            $fileData = base64_decode($base64);
            Storage::disk($mask_media->disk)->put($mask_media->getPath(), $fileData);
        } else {
            $this->record
                ->addMediaFromBase64($base64, 'public')
                ->usingName($media->name)
                ->usingFileName("{$media->name}.png")
                ->toMediaCollection('bubble_mask');
        }


        Notification::make()
            ->success()
            ->title('Маска успешно сохранена!')
            ->send();
    }

    public function markDone(): void
    {
        // dispatch(new CreateBubbleMasksJob(mangaChapterId: $this->record->id));

        // Notification::make()
        //     ->success()
        //     ->title('Картинки отправлены на обработку!')
        //     ->send();

        // $this->redirect(MangaChapterResource::getUrl('index'));
    }
}
