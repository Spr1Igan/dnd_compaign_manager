<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * @var array<int, string>
     */
    private const SUPPORTED_LOCALES = ['ru', 'en'];

    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->session()->get('locale', config('app.locale', 'ru'));

        if (! in_array($locale, self::SUPPORTED_LOCALES, true)) {
            $locale = 'ru';
        }

        App::setLocale($locale);
        $request->session()->put('locale', $locale);

        return $next($request);
    }
}
