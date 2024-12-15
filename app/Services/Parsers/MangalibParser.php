<?php

namespace App\Services\Parsers;

use App\Models\MangaChapter;
use Illuminate\Support\Facades\Http;

class MangalibParser
{
    private string $baseUrl = 'https://api.mangalib.me/api/manga';
    private string $imgServerUrl = 'https://img33.imgslib.link/';
    private array $headers = [
        // TODO: random User-Agent
        'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36',
        'Accept' => '*/*',
    ];

    public function handle(MangaChapter $mangaChapter): void
    {
        $titleSlug = $mangaChapter->manga->slug_mangalib;
        $volume = $mangaChapter->volume;
        $chapterNumber = $mangaChapter->number;

        $images = $this->fetchChapterImages($titleSlug, $volume, $chapterNumber);

        $this->downloadImages($mangaChapter, $images);
    }

    private function fetchChapterImages(string $titleSlug, int $volume, int $chapterNumber): array
    {
        $response = Http::withHeaders($this->headers)->get("{$this->baseUrl}/$titleSlug/chapter", [
            'number' => $chapterNumber,
            'volume' => $volume,
        ]);

        if ($response->failed()) {
            throw new \Exception("Failed to fetch images for $titleSlug: {$response->status()}");
        }

        $content = $response->json('data.pages', []);
        return collect($content)->map(fn ($image) => $this->imgServerUrl . $image['url'])->toArray();
    }

    private function downloadImages(MangaChapter $mangaChapter, array $images): void
    {
        foreach ($images as $index => $imageUrl) {
            $filename = sprintf('%03d', $index + 1);
            $response = Http::withHeaders($this->headers)->get($imageUrl);

            if ($response->ok()) {
                $mangaChapter
                    ->addMediaFromString($response->body())
                    ->usingName($filename)
                    ->usingFileName("$filename.jpg")
                    ->toMediaCollection('raw');
            } else {
                throw new \Exception("Failed to download image: $imageUrl");
            }
        }
    }
}
