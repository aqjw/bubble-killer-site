<?php

namespace App\Models;

use App\Enums\MangaChapterStatus;
use App\Jobs\Process\ParseImagesJob;
use App\Support\MangaChapterPathGenerator;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MangaChapter extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'manga_id',
        'volume',
        'number',
        'status',
    ];

    protected $casts = [
        'status' => MangaChapterStatus::class
    ];

    protected $attributes = [
        'status' => MangaChapterStatus::Pending
    ];

    protected static function booted()
    {
        self::created(function (self $record) {
            dispatch(new ParseImagesJob(mangaChapterId: $record->id));
        });
    }

    public function registerMediaCollections(): void
    {
        // TODO:
        $collections = ['raw', 'split', 'bubble_mask', 'clear', 'frame_mask', 'done'];

        foreach ($collections as $collection) {
            $this->addMediaCollection($collection)->useDisk('public');
        }
    }

    public function getChapterPath(string $collection): string
    {
        return implode('/', [
            'manga',
            $this->manga_id,
            "{$this->volume}-{$this->number}",
            $collection
        ]);
    }

    public function manga(): BelongsTo
    {
        return $this->belongsTo(Manga::class);
    }
}
