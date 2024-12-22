<?php

namespace App\Console\Commands;

use App\Models\Manga;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class FetchManga extends Command
{
    protected $signature = 'app:fetch-manga';

    protected $description = 'Fetch manga data from Mangalib API and save to database';

    public function handle()
    {
        $baseUrl = 'https://api2.mangalib.me/api/manga';
        $params = [
            'caution[]' => [3, 2],
            'chap_count_min' => 50,
            'fields[]' => ['rate', 'rate_avg', 'userBookmark'],
            'format[]' => [7],
            'scanlate_status[]' => [1, 2, 3],
            'site_id[]' => [1],
            'types[]' => [5],
            'year_min' => 2015,
        ];
        $headers = [
            'sec-ch-ua-platform' => '"macOS"',
            'Referer' => 'https://mangalib.me/',
            'sec-ch-ua' => '"Google Chrome";v="131", "Chromium";v="131", "Not_A Brand";v="24"',
            'sec-ch-ua-mobile' => '?0',
            'Site-Id' => '1',
            'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36',
            'DNT' => '1',
            'Content-Type' => 'application/json',
        ];

        $nextPage = $this->buildUrl($baseUrl, $params);
        $page = 1;

        while ($nextPage && $page <= 20) {
            $response = Http::withHeaders($headers)->get($nextPage);

            if ($response->failed()) {
                $this->error("Failed to fetch data: {$response->status()}.");
                break;
            }

            $data = $response->json('data', []);
            foreach ($data as $item) {
                Manga::updateOrCreate(
                    ['slug_mangalib' => $item['slug_url']],
                    ['title' => $item['rus_name']]
                );
            }

            $this->info("Saved " . count($data) . " records to the database. Page: $page");
            $nextPage = $response->json('links.next');
            $page++;
        }

        $this->info('Data fetching complete.');
    }

    private function buildUrl(string $baseUrl, array $params): string
    {
        return $baseUrl . '?' . http_build_query($params);
    }
}
