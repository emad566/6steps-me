<?php

namespace App\Exceptions;

use App\Http\Controllers\API\BaseApiController;
use Illuminate\Auth\AuthenticationException;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Throwable;

class Handler extends ExceptionHandler
{

    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            $baseApiController = new BaseApiController();
            return $baseApiController->sendResponse(false, [], "Unauthorized", [], 401);
        }

        // Customize the redirect route here
        return redirect()->guest('login');
    }

    public function render($request, Throwable $exception)
    {
        if ($exception instanceof MethodNotAllowedHttpException) {
            $baseApiController = new BaseApiController();
            return $baseApiController->sendResponse(false, [], 'The url ' . $request->url() . ' could not be found.', [], 404);
        }

        if ($exception instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
            $baseApiController = new BaseApiController();
            return $baseApiController->sendResponse(false, [], 'The url ' . $request->url() . ' could not be found.', [], 404);
        }

        return parent::render($request, $exception);
    }
}
