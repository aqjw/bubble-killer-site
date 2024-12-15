<?php

namespace App\Support;

use App\Models\MangaChapter;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;

class MangaChapterPathGenerator implements PathGenerator
{
    public function getPath(Media $media): string
    {
        /** @var  MangaChapter $model */
        $model = $media->model;
        return $model->getChapterPath($media->collection_name) . '/';
    }

    public function getPathForConversions(Media $media): string
    {
        return $this->getPath($media);
    }

    public function getPathForResponsiveImages(Media $media): string
    {
        return $this->getPath($media);
    }
}
