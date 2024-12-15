<?php

namespace App\Services;

use App\Models\MangaChapter;
use App\Services\FileRegistrationService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;

class ProcessService
{
    private function getPythonPath(): string
    {
        // TODO:
        return '/Users/antonshever/anaconda3/bin/python';
    }

    private function getScriptsPath(string $script = '', array $options = []): string
    {
        $scriptPath = $this->getPythonPath() . ' ' . base_path("scripts/python/$script");

        if (! empty($options)) {
            $formattedOptions = array_map(
                function ($key, $value) {
                    if (is_array($value)) {
                        $value = implode(',', $value);
                    }
                    return sprintf('--%s="%s"', $key, $value);
                },
                array_keys($options),
                $options
            );

            return $scriptPath . ' ' . implode(' ', $formattedOptions);
        }

        return $scriptPath;
    }


    private function parseJson(string $result): ?array
    {
        preg_match('/--json start-- (.*?) --json end--/', $result, $matches);

        $data = null;
        if (! empty($matches[1])) {
            $json = $matches[1];
            $data = json_decode($json, true);
        }

        return $data;
    }

    public function processSplit(MangaChapter $mangaChapter): bool
    {
        $getStoragePath = fn ($folder) => storage_path("app/public/$folder");
        $input = $getStoragePath($mangaChapter->getChapterPath('raw'));
        $output = $getStoragePath($mangaChapter->getChapterPath('split'));

        $process = Process::forever()->run(
            $this->getScriptsPath('split.py', [
                'input' => $input,
                'output' => $output
            ])
        );


        if ($process->successful()) {
            $data = $this->parseJson($process->output());
            if (! empty($data['files'])) {
                app(FileRegistrationService::class)->registerFilesFromList(
                    model: $mangaChapter,
                    directory: $output,
                    files: $data['files'],
                    collectionName: 'split',
                    disk: 'public'
                );

                return true;
            }
        }

        Log::error(json_encode([
            'process' => 'split',
            'chapter' => $mangaChapter->id,
            'output' => $process->output(),
            'errorOutput' => $process->errorOutput(),
        ]));

        return false;
    }

    public function processBubbleMask(MangaChapter $mangaChapter): bool
    {
        $getStoragePath = fn ($folder) => storage_path("app/public/$folder");
        $input = $getStoragePath($mangaChapter->getChapterPath('split'));
        $output = $getStoragePath($mangaChapter->getChapterPath('bubble_mask'));

        $process = Process::forever()->run(
            $this->getScriptsPath('bubble_mask.py', [
                'input' => $input,
                'files' => $mangaChapter
                    ->media()
                    ->where('collection_name', 'split')
                    ->where('custom_properties->bubble', true)
                    ->pluck('file_name')
                    ->toArray(),
                'output' => $output
            ])
        );


        if ($process->successful()) {
            $data = $this->parseJson($process->output());
            if (! empty($data['files'])) {
                app(FileRegistrationService::class)->registerFilesFromList(
                    model: $mangaChapter,
                    directory: $output,
                    files: $data['files'],
                    collectionName: 'bubble_mask',
                    disk: 'public'
                );

                return true;
            }
        }

        Log::error(json_encode([
            'process' => 'bubble_mask',
            'chapter' => $mangaChapter->id,
            'output' => $process->output(),
            'errorOutput' => $process->errorOutput(),
        ]));

        return false;
    }
}
