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
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class FilterImagesMangaChapter extends Page
{
    use InteractsWithRecord;

    protected static string $resource = MangaChapterResource::class;

    protected static string $view = 'filament.resources.manga-chapter-resource.pages.filter-images';

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
                'bubble' => $media->getCustomProperty('bubble', true),
                'crop' => $media->getCustomProperty('crop', true),
                'delete' => false,
            ];
        }
    }

    public function willDeleteImage(int $image_id): bool
    {
        return $this->data[$image_id]['delete'] ?? false;
    }

    public function getImagesProperty()
    {
        return $this->record->media()->where('collection_name', 'split')->get();
    }

    public function hasUnsavedChanges(): bool
    {
        foreach ($this->data as $image_id => $image_data) {
            $media = $this->images->where('id', $image_id)->first();
            if (! $media) {
                continue;
            }

            if (
                $image_data['delete'] ||
                $image_data['bubble'] !== $media->getCustomProperty('bubble', true) ||
                $image_data['crop'] !== $media->getCustomProperty('crop', true)
            ) {
                return true;
            }
        }

        return false;
    }

    public function submitAction(): Action
    {
        return Action::make('submit')
            ->label('Сохранить')
            ->requiresConfirmation()
            ->action(fn () => $this->submit());
    }

    public function markDoneAction(): Action
    {
        return Action::make('markDone')
            ->label('Готово')
            ->requiresConfirmation()
            ->disabled($this->hasUnsavedChanges())
            ->action(fn () => $this->markDone());
    }

    public function submit(): void
    {
        foreach ($this->data as $image_id => $image_data) {
            $media = Media::find($image_id);
            if ($image_data['delete']) {
                $media->delete();
            } else {
                $media->setCustomProperty('bubble', $image_data['bubble']);
                $media->setCustomProperty('crop', $image_data['crop']);
                $media->save();
            }
        }

        Notification::make()
            ->success()
            ->title('Изменения успешно сохранены!')
            ->send();
    }

    public function markDone(): void
    {
        dispatch(new CreateBubbleMasksJob(mangaChapterId: $this->record->id));

        Notification::make()
            ->success()
            ->title('Картинки отправлены на обработку!')
            ->send();

        $this->redirect(MangaChapterResource::getUrl('index'));
    }
}
