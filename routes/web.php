<?php

use App\Enums\MangaChapterStatus;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\WebhookController;
use App\Http\Middleware\DynamicThrottle;
use App\Models\Manga;
use App\Models\MangaChapter;
use App\Services\MangaLibSearchService;
use App\Services\ProcessService;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

Route::get('/frame', function () {
    $manga = Manga::first();

    // Get files from a specific folder
    $folderPath = '/Users/antonshever/Desktop/bubble-frame';
    $files = collect(File::files($folderPath))->map(function ($file) {
        return $file->getPathname();
    });

    $files->chunk(20)->each(function ($chunkedFiles, $index) use ($manga) {
        $chapter = $manga->chapters()->create([
            'volume' => 1,
            'number' => $index + 1,
            'status' => MangaChapterStatus::ImageFiltering,
        ]);

        foreach ($chunkedFiles as $key => $file) {
            $filename = sprintf('%03d', $key + 1);

            $media = $chapter
                ->addMedia($file)
                ->usingName($filename)
                ->usingFileName("{$filename}.jpg")
                ->toMediaCollection('split');

            $media->custom_properties = [
                'bubble' => true,
                'crop' => true,
            ];
            $media->save();
        }
        // dd(1);
    });

    dd('done');
});


Route::get('/test', function () {
    // DB::transaction(function () {
    //     Media::query()
    //         ->where('collection_name', 'split')
    //         ->get()
    //         ->each(function ($media) {
    //             $newMedia = $media->replicate();
    //             $newMedia->uuid = Str::uuid()->toString();
    //             $newMedia->collection_name = 'clear';
    //             $newMedia->save();
    //         });
    // });
    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
    // DB::transaction(function () {
    //     DB::table('media')
    //         ->where('file_name', 'like', '%.jpg')
    //         ->update([
    //             'file_name' => DB::raw("REPLACE(file_name, '.jpg', '.png')")
    //         ]);

    //     // Обновить MIME-тип
    //     DB::table('media')
    //         ->where('mime_type', 'image/jpeg')
    //         ->update([
    //             'mime_type' => 'image/png'
    //         ]);
    // });
    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
    // $mangalibSearch = app(MangaLibSearchService::class);
    // $result = $mangalibSearch->search('образование');
    // dd($result);
    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
    // $mangaChapter = MangaChapter::find(18);
    // $success = app(ProcessService::class)->processSplit($mangaChapter);
    // $success = app(ProcessService::class)->processImproveQuality($mangaChapter);
});


Route::get('/', HomeController::class)->name('home');
Route::post('upload', UploadController::class)
    ->name('upload')
    ->middleware(DynamicThrottle::class);

Route::prefix('tasks')->as('tasks.')->group(function () {
    Route::get('', [TaskController::class, 'index'])->name('index')->middleware('auth');
    Route::get('{task}', [TaskController::class, 'show'])->name('show');
});

Route::middleware('auth')->group(function () {
    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::post('api/webhook/processing/{task}', [WebhookController::class, 'processing'])->name('api.webhook.processing');

require __DIR__ . '/upi.php';
require __DIR__ . '/auth.php';
