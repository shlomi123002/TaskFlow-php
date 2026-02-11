<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;

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

    public function render($request, Throwable $exception)
    {
        if ($request->expectsJson()) {

            // 401 - לא מחובר
            if ($exception instanceof AuthenticationException) {
                return response()->json([
                    'error' => 'Unauthenticated',
                    'code' => 401,
                ], 401);
            }

            // 404 - מודל לא נמצא (firstOrFail)
            if ($exception instanceof ModelNotFoundException) {
                return response()->json([
                    'error' => 'Resource not found',
                    'code' => 404,
                ], 404);
            }

            // 404 - Route לא קיים
            if ($exception instanceof NotFoundHttpException) {
                return response()->json([
                    'error' => 'Route not found',
                    'code' => 404,
                ], 404);
            }

            // 405 - Method לא מותר (GET במקום POST וכו')
            if ($exception instanceof MethodNotAllowedHttpException) {
                return response()->json([
                    'error' => 'Method not allowed',
                    'code' => 405,
                ], 405);
            }

            // שגיאות HTTP כלליות
            if ($exception instanceof HttpException) {
                return response()->json([
                    'error' => $exception->getMessage() ?: 'HTTP error',
                    'code' => $exception->getStatusCode(),
                ], $exception->getStatusCode());
            }

            // 500 - כל מה שלא נתפס
            return response()->json([
                'error' => 'Internal server error',
                'code' => 500,
            ], 500);
        }

        return parent::render($request, $exception);
    }
}