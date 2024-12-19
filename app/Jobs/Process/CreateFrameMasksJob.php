<?php

namespace App\Jobs\Process;

use App\Enums\MangaChapterStatus;
use App\Models\Manga;
use App\Models\MangaChapter;
use App\Services\Parsers\MangalibParser;
use App\Services\ProcessService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CreateFrameMasksJob implements ShouldQueue
{
    use Queueable;

    public $timeout = 3600;

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
        $mangaChapter->update(['status' => MangaChapterStatus::FrameMaskCreation]);

        $success = app(ProcessService::class)->processFrameMask($mangaChapter);

        if ($success) {
            $mangaChapter->update(['status' => MangaChapterStatus::FrameMaskVerification]);
        }
    }
}
