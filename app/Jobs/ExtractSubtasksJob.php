<?php

namespace App\Jobs;

use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ExtractSubtasksJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @param string $parentId
     */
    public function __construct(
        protected string $parentId
    ) {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $task = Task::findOrFail($this->parentId);

        // Проверка, что задача является архивной и содержит ZIP-файл
        if ($task->type === 'multiple') {
            $zipPath = "uploads/{$this->parentId}/original.zip";
            $taskService = app(TaskService::class);
            $taskService->extractAndCreateSubtasks($zipPath, $this->parentId, $task->cleaning_model);
        }
    }
}
