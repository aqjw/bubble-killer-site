<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function __invoke(): Response
    {
        $models = config('app.cleaner_models');

        return Inertia::render('Home', ['models' => $models]);
    }
}
