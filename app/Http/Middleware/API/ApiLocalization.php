<?php

namespace App\Http\Middleware\API;

use App\Http\Controllers\API\BaseApiController;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiLocalization extends BaseApiController
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->header('Accept-Language');
        if (!$locale) {
            $locale = config('app.locale');
        }

        if (!array_key_exists($locale, config('app.supported_languages'))) {
            app()->setLocale(config('app.locale'));
        }

        app()->setLocale($locale);

        $response = $next($request);

        $response->headers->set('Accept-Language', $locale);

        return $response;
    }
}
