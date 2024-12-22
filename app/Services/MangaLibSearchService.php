<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;


class MangaLibSearchService
{
    private string $baseUrl = 'https://api2.mangalib.me/api/manga';
    private array $headers = [
        'sec-ch-ua-platform' => '"macOS"',
        'Referer' => 'https://mangalib.me/',
        'sec-ch-ua' => '"Google Chrome";v="131", "Chromium";v="131", "Not_A Brand";v="24"',
        'sec-ch-ua-mobile' => '?0',
        'Site-Id' => '1',
        'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36',
        'DNT' => '1',
        'Content-Type' => 'application/json',
    ];

    /**
     * Searches for manga by query.
     *
     * @param string $query
     * @return array
     */
    public function search(string $query): array
    {
        $response = Http::withHeaders($this->headers)
            // ->withQueryParameters([
            //     'fields[]' => ['rate_avg', 'rate', 'releaseDate'],
            //     'q' => $query,
            //     'site_id[]' => [1],
            // ])
            ->get("{$this->baseUrl}?fields[]=rate_avg&fields[]=rate&fields[]=releaseDate&q={$query}&site_id[]=1");

        if ($response->failed()) {
            throw new \Exception("Failed to fetch manga data: " . $response->status());
        }

        return $response->json('data');
    }
}
