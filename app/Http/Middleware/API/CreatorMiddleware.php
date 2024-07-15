<?php

namespace App\Http\Middleware\API;

use App\Http\Controllers\API\BaseApiController;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CreatorMiddleware extends BaseApiController
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth('creator')->check()) {
            return $next($request);
        } else {
            return $this->sendResponse(false, [], "You are not creator user", null, 400);
        }
    }
}
