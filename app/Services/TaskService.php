<?php

namespace App\Services;

use App\Enums\TaskStatus;
use App\Jobs\ExtractSubtasksJob;
use App\Models\Task;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ZipArchive;

class TaskService
{
    public function create(UploadedFile $file, string $modelName): Task
    {
        $isZip = $file->getClientOriginalExtension() === 'zip';
        $type = $isZip ? 'multiple' : 'single';

        // Создаем основную задачу
        $task = Task::create([
            'type' => $type,
            'user_id' => auth()->id(),
            'status' => TaskStatus::Pending,
            'cleaning_model' => $modelName,
            'original_filename' => $file->getClientOriginalName(),
        ]);

        // Сохраняем файл и обрабатываем архив, если это ZIP
        if ($isZip) {
            $file->storeAs("uploads/{$task->id}", 'original.zip', 'spaces');
            ExtractSubtasksJob::dispatch($task->id);
        } else {
            $file->storeAs("uploads/{$task->id}", 'original.png', 'spaces');
            $segmentationService = app(SegmentationService::class);
            rescue(fn () => $segmentationService->send($task));
        }

        return $task;
    }

    public function extractAndCreateSubtasks(string $zipPath, string $parentId, string $modelName): void
    {
        $task = Task::find($parentId);
        $segmentationService = app(SegmentationService::class);
        $zip = new ZipArchive;
        $localTaskDir = "uploads/{$parentId}";

        // Создаем директорию для задания и загружаем ZIP напрямую в локальный путь
        Storage::disk('public')->makeDirectory($localTaskDir);
        Storage::disk('public')->put("{$localTaskDir}/original.zip", Storage::disk('spaces')->get($zipPath));


        $localZipPath = storage_path("app/public/{$localTaskDir}/original.zip");

        // Проверяем, что ZIP-файл скачан
        if (! file_exists($localZipPath)) {
            Log::error("Failed to download ZIP file from Spaces");
            return;
        }

        // Открываем архив
        if ($zip->open($localZipPath) !== true) {
            Log::error("Failed to open ZIP file: {$localZipPath}");
            return;
        }

        Log::info("ZIP file opened successfully: {$localZipPath}");

        // Перебираем файлы архива и обрабатываем изображения
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $filename = $zip->getNameIndex($i);

            if (! preg_match('/\.(jpg|jpeg|png)$/i', $filename) || str_starts_with($filename, '__MACOSX')) {
                Log::info("Skipping non-image or system file: {$filename}");
                continue;
            }

            $subtask = Task::create([
                'user_id' => $task->user_id ?? null,
                'parent_id' => $parentId,
                'type' => 'single',
                'status' => TaskStatus::Pending,
                'cleaning_model' => $modelName,
                'original_filename' => $filename,
            ]);
            Log::info("Subtask created with ID: {$subtask->id}");

            // Извлекаем файл и проверяем его существование
            $subtaskDir = "uploads/{$subtask->id}";
            Storage::makeDirectory($subtaskDir);
            $zip->extractTo(storage_path("app/{$subtaskDir}"), $filename);

            $extractedPath = storage_path("app/{$subtaskDir}/{$filename}");
            if (! file_exists($extractedPath)) {
                Log::error("Failed to extract file: {$extractedPath}");
                continue;
            }

            // Загружаем в Spaces и удаляем локальный файл
            Storage::disk('spaces')->put("{$subtaskDir}/original.png", file_get_contents($extractedPath));
            Log::info("File uploaded to Spaces: {$subtaskDir}/original.png");
            Storage::delete("{$subtaskDir}/{$filename}");

            // Отправляем подзадачу на обработку
            rescue(fn () => $segmentationService->send($subtask));
        }

        // Закрываем архив и удаляем локальный ZIP
        $zip->close();
        Storage::delete($localZipPath);
        Log::info("ZIP file deleted from local storage: {$localZipPath}");
    }

    public function get(User $user, int $limit = 10): LengthAwarePaginator
    {
        return $user
            ->tasks()
            ->with('subtasks')
            ->whereNull('parent_id')
            ->latest()
            ->paginate($limit);
    }
}
