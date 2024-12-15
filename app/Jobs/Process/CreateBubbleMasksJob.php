<?php

namespace App\Jobs\Process;

use App\Enums\MangaChapterStatus;
use App\Models\Manga;
use App\Models\MangaChapter;
use App\Services\Parsers\MangalibParser;
use App\Services\ProcessService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CreateBubbleMasksJob implements ShouldQueue
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
        $mangaChapter->update(['status' => MangaChapterStatus::MaskCreation]);

        $success = app(ProcessService::class)->processBubbleMask($mangaChapter);

        if ($success) {
            $mangaChapter->update(['status' => MangaChapterStatus::MaskVerification]);
        }
    }
}
