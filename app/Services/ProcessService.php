<?php

namespace App\Services;

use App\Models\MangaChapter;
use App\Services\FileRegistrationService;
use function base_path;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ProcessService
{
    private function getPythonPath(): string
    {
        return 'cd ' . base_path("scripts/python") . ' && source venv/bin/activate && python';
    }

    private function getScriptsPath(string $script = '', array $options = []): string
    {
        $scriptPath = $this->getPythonPath() . ' ' . $script;

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
        $storage_path = storage_path('app/public/');
        $input = $storage_path . $mangaChapter->getChapterPath('raw');
        $output = $storage_path . $mangaChapter->getChapterPath('split');

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
                    disk: 'public',
                    properties: [
                        'bubble' => true,
                        'crop' => true,
                    ]
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
        $storage_path = storage_path('app/public/');
        $input = $storage_path . $mangaChapter->getChapterPath('split');
        $output = $storage_path . $mangaChapter->getChapterPath('bubble_mask');

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

    public function processImproveQuality(MangaChapter $mangaChapter): bool
    {
        $storage_path = storage_path('app/public/');
        $input = $storage_path . $mangaChapter->getChapterPath('split');
        $output = $input;

        $process = Process::forever()->run(
            $this->getScriptsPath('improve_quality.py', [
                'input' => $input,
                'output' => $output
            ])
        );

        if ($process->successful()) {
            return true;
        }

        Log::error(json_encode([
            'process' => 'improve_quality',
            'chapter' => $mangaChapter->id,
            'output' => $process->output(),
            'errorOutput' => $process->errorOutput(),
        ]));

        return false;
    }

    public function processRemoveBubbles(MangaChapter $mangaChapter): bool
    {
        $storage_path = storage_path('app/public/');
        $input_images = $storage_path . $mangaChapter->getChapterPath('split');
        $input_masks = $storage_path . $mangaChapter->getChapterPath('bubble_mask');
        $output = $storage_path . $mangaChapter->getChapterPath('clear');

        $hasRemask = $mangaChapter
            ->media()
            ->where('collection_name', 'split')
            ->where('custom_properties->remask', true)
            ->exists();

        $process = Process::forever()->run(
            $this->getScriptsPath('remove_bubbles.py', [
                'input_images' => $input_images,
                'input_masks' => $input_masks,
                'output' => $output,
                'files' => $mangaChapter
                    ->media()
                    ->where('collection_name', 'split')
                    ->where('custom_properties->bubble', true)
                    ->when($hasRemask, fn ($query) => $query->where('custom_properties->remask', true))
                    ->pluck('file_name')
                    ->toArray(),
            ])
        );

        if ($process->successful()) {
            $data = $this->parseJson($process->output());
            if (! empty($data['files'])) {

                if ($hasRemask) {
                    $mangaChapter
                        ->media()
                        ->where('collection_name', 'split')
                        ->where('custom_properties->remask', true)
                        ->get()
                        ->each(function (Media $image) {
                            $image->forgetCustomProperty('remask');
                            $image->save();
                        });
                }

                app(FileRegistrationService::class)->registerFilesFromList(
                    model: $mangaChapter,
                    directory: $output,
                    files: $data['files'],
                    collectionName: 'clear',
                    disk: 'public'
                );

                return true;
            }
        }

        Log::error(json_encode([
            'process' => 'remove_bubbles',
            'chapter' => $mangaChapter->id,
            'output' => $process->output(),
            'errorOutput' => $process->errorOutput(),
        ]));

        return false;
    }

    public function processAutoCrop(MangaChapter $mangaChapter): bool
    {
        $storage_path = storage_path('app/public/');
        $input = $storage_path . $mangaChapter->getChapterPath('clear');
        $output = $storage_path . $mangaChapter->getChapterPath('auto_crop');

        $process = Process::forever()->run(
            $this->getScriptsPath('auto_crop.py', [
                'input' => $input,
                'files' => $mangaChapter
                    ->media()
                    ->where('collection_name', 'split')
                    ->where('custom_properties->crop', true)
                    ->pluck('file_name')
                    ->toArray(),
                'output' => $output
            ])
        );

        // if ($process->successful()) {
        //     $data = $this->parseJson($process->output());
        //     if (! empty($data['files'])) {
        //         app(FileRegistrationService::class)->registerFilesFromList(
        //             model: $mangaChapter,
        //             directory: $output,
        //             files: $data['files'],
        //             collectionName: 'auto_crop',
        //             disk: 'public'
        //         );

        //         return true;
        //     }
        // }

        // Log::error(json_encode([
        dd([
            'process' => 'auto_crop',
            'chapter' => $mangaChapter->id,
            'output' => $process->output(),
            'errorOutput' => $process->errorOutput(),
        ]);
        // ]));

        return false;
    }

    // TODO:
    public function processFrameMask(MangaChapter $mangaChapter): bool
    {
        $storage_path = storage_path('app/public/');
        $input = $storage_path . $mangaChapter->getChapterPath('clear');
        $output = $storage_path . $mangaChapter->getChapterPath('frame_mask');

        $process = Process::forever()->run(
            $this->getScriptsPath('frame_mask.py', [
                'input' => $input,
                'files' => $mangaChapter
                    ->media()
                    ->where('collection_name', 'clear')
                    ->where('custom_properties->crop', true)
                    ->pluck('file_name')
                    ->toArray(),
                'output' => $output
            ])
        );

        // TODO:
        dd([
            'process' => 'frame_mask',
            'chapter' => $mangaChapter->id,
            'output' => $process->output(),
            'errorOutput' => $process->errorOutput(),
        ]);

        if ($process->successful()) {
            $data = $this->parseJson($process->output());
            if (! empty($data['files'])) {
                app(FileRegistrationService::class)->registerFilesFromList(
                    model: $mangaChapter,
                    directory: $output,
                    files: $data['files'],
                    collectionName: 'frame_mask',
                    disk: 'public'
                );

                return true;
            }
        }

        Log::error(json_encode([
            'process' => 'frame_mask',
            'chapter' => $mangaChapter->id,
            'output' => $process->output(),
            'errorOutput' => $process->errorOutput(),
        ]));

        return false;
    }
}
