<?php

namespace App\Filament\Resources\MangaChapterResource\Pages;

use App\Filament\Resources\MangaChapterResource;
use App\Jobs\Process\CreateBubbleMasksJob;
use App\Jobs\Process\QualityImprovementJob;
use App\Services\CropService;
use App\Services\ImageSplitter;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
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
            ];
        }
    }

    public function getImagesProperty()
    {
        return $this->record
            ->media()
            ->where('collection_name', 'split')
            ->orderBy('order_column', 'asc')
            ->get()
            ->keyBy('id');
    }

    public function updated($name, $value): void
    {
        if (preg_match('/^data\.(\d+)\.(\w+)$/', $name, $matches)) {
            $id = $matches[1];
            $property = $matches[2];

            if (isset($this->images[$id])) {
                /** @var Media $media */
                $media = $this->images[$id];

                $media->setCustomProperty($property, $value);
                $media->save();
            }
        }
    }

    public function splitImage(Media $image, array $data): void
    {
        $path = $image->getPath();
        $angle = (int) $data['angle'];
        $x = (int) round($data['x']);
        $y = (int) round($data['y']);

        $segments = app(ImageSplitter::class)->split($path, $angle, $x, $y);

        Storage::disk($image->disk)
            ->put($image->getPathRelativeToRoot(), file_get_contents($segments[0]));
        $image->touch();

        $this->shiftOrderForNextMedia($image->order_column + 1);

        $name = $this->generateUniqueMediaName($image->name);

        $media = $this->record
            ->addMedia($segments[1])
            ->preservingOriginal()
            ->usingName($name)
            ->setFileName("{$name}.png")
            ->setOrder($image->order_column + 1)
            ->withCustomProperties($image->custom_properties)
            ->toMediaCollection('split');

        $this->data[$media->id] = [
            'bubble' => $media->getCustomProperty('bubble', true),
            'crop' => $media->getCustomProperty('crop', true),
        ];

        // Удаляем временные файлы
        File::delete($segments[0]);
        File::delete($segments[1]);
    }

    private function generateUniqueMediaName(string $originalName): string
    {
        $number = 1;
        $baseName = $originalName;

        // Check if the name already has a -N suffix
        if (preg_match('/-(\d+)$/', $originalName, $matches)) {
            $number = (int) $matches[1] + 1;
            $baseName = preg_replace('/-(\d+)$/', '', $originalName);
        }

        $name = "{$baseName}-{$number}";

        // Increment the number until the name is unique
        while ($this->record->media()->where('name', $name)->exists()) {
            $number++;
            $name = "{$baseName}-{$number}";
        }

        return $name;
    }

    private function shiftOrderForNextMedia(int $startOrder): void
    {
        $mediaItems = $this->record->media()
            ->where('order_column', '>=', $startOrder)
            ->where('collection_name', 'split')
            ->orderBy('order_column', 'asc')
            ->get();

        foreach ($mediaItems as $media) {
            $media->update(['order_column' => $media->order_column + 1]);
        }
    }

    public function deleteImageAction(): Action
    {
        return Action::make('deleteImage')
            ->label('Удалить')
            ->color('danger')
            ->requiresConfirmation()
            ->action(function (array $arguments) {
                $image = Media::find($arguments['image_id']);
                $image->delete();
            });
    }

    public function markDoneAction(): Action
    {
        return Action::make('markDone')
            ->label('Готово')
            ->requiresConfirmation()
            ->action(function () {
                dispatch(new QualityImprovementJob(mangaChapterId: $this->record->id));

                Notification::make()
                    ->success()
                    ->title('Картинки отправлены на обработку!')
                    ->send();

                $this->redirect(MangaChapterResource::getUrl('index'));
            });
    }
}
