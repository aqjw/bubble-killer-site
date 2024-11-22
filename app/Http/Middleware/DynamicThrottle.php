<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class DynamicThrottle
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Получаем ключ для пользователя или гостя
        $key = $request->user()
            ? 'user:' . $request->user()->id
            : 'guest:' . $request->ip();

        // Устанавливаем лимиты
        RateLimiter::for($key, function () use ($request, $key) {
            if ($request->user()) {
                return Limit::perHour(60)->by($key);
            }

            return Limit::perHour(10)->by($key);
        });

        // Проверяем лимит
        if (RateLimiter::tooManyAttempts($key, $request->user() ? 60 : 10)) {
            return back()->withErrors([
                'files' => ['Too many requests. Please try again later.'],
            ]);
        }

        RateLimiter::hit($key);

        return $next($request);
    }
}
