<?php

namespace App\Jobs\Process;

use App\Enums\MangaChapterStatus;
use App\Models\Manga;
use App\Models\MangaChapter;
use App\Services\Parsers\MangalibParser;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ParseImagesJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $mangaChapterId,
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $mangaChapter = MangaChapter::find($this->mangaChapterId);
        $mangaChapter->update(['status' => MangaChapterStatus::ImageParsing]);
        $success = false;

        if ($mangaChapter->manga->slug_mangalib) {
            $success = rescue(function () use ($mangaChapter) {
                app(MangalibParser::class)->handle($mangaChapter);
                return true;
            }, rescue: false);
        }

        if ($success) {
            dispatch(new SplitImagesJob(mangaChapterId: $mangaChapter->id));
        }
    }
}
