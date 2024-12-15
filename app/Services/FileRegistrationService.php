<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class FileRegistrationService
{
    /**
     * Register files from a given list and directory in the media library.
     *
     * @param Model $model The model to associate the media with.
     * @param string $directory The directory containing the files.
     * @param array $files List of file names to register.
     * @param string $collectionName The media collection name.
     * @param string $disk The storage disk name.
     * @return void
     */
    public function registerFilesFromList(Model $model, string $directory, array $files, string $collectionName = 'default', string $disk = 'public'): void
    {
        foreach ($files as $fileName) {
            $filePath = $directory . DIRECTORY_SEPARATOR . $fileName;

            if (! File::exists($filePath)) {
                continue;
            }

            if (Media::where('file_name', $fileName)->where('collection_name', $collectionName)->exists()) {
                continue;
            }

            Media::create([
                'model_type' => get_class($model),
                'model_id' => $model->id,
                'collection_name' => $collectionName,
                'name' => pathinfo($fileName, PATHINFO_FILENAME),
                'file_name' => $fileName,
                'mime_type' => File::mimeType($filePath),
                'disk' => $disk,
                'size' => File::size($filePath),
                'manipulations' => [],
                'custom_properties' => [],
                'generated_conversions' => [],
                'responsive_images' => [],
                'order_column' => null,
            ]);
        }
    }
}
