<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
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

    public function render($request, Throwable $exception)
    {
        // Return structured JSON error for ALL exceptions across the project
        return $this->apiExceptionResponse($request, $exception);
    }

    /**
     * Build a standardized JSON response for known exceptions.
     */
    protected function apiExceptionResponse(Request $request, Throwable $e): JsonResponse
    {
        $status = 500;
        $message = 'Internal server error';
        $payloadErrors = null;

        // Validation exception (422) - include field errors
        if ($e instanceof ValidationException) {
            $status = 422;
            $message = 'Validation failed';
            $payloadErrors = $e->errors();
        }
        // Authentication (401)
        elseif ($e instanceof AuthenticationException) {
            $status = 401;
            $message = 'Unauthenticated';
        }
        // Authorization (403)
        elseif ($e instanceof AuthorizationException) {
            $status = 403;
            $message = 'Forbidden';
        }
        // Eloquent model not found (404)
        elseif ($e instanceof ModelNotFoundException) {
            $status = 404;
            $model = class_basename($e->getModel());
            $message = ($model ? $model . ' not found' : 'Resource not found');
        }
        // Route not found (404)
        elseif ($e instanceof NotFoundHttpException) {
            $status = 404;
            $message = 'Resource not found';
        }
        // Method not allowed (405)
        elseif ($e instanceof MethodNotAllowedHttpException) {
            $status = 405;
            $message = 'Method not allowed';
        }
        // Throttle / Too many requests (429)
        elseif ($e instanceof ThrottleRequestsException || $e instanceof TooManyRequestsHttpException) {
            $status = 429;
            $message = 'Too many requests';
        }
        // Generic HttpExceptionInterface instances (use provided status and message)
        elseif ($e instanceof HttpExceptionInterface) {
            $status = $e->getStatusCode();
            $message = $e->getMessage() ?: ($status ? JsonResponse::$statusTexts[$status] ?? 'HTTP error' : 'HTTP error');
        }
        // Fallback: 500 Internal Server Error (message already set)
        else {
            $status = 500;
            $message = 'Internal server error';
        }

        $payload = [
            'error' => $message,
            'code'  => $status,
        ];

        if (!is_null($payloadErrors)) {
            $payload['errors'] = $payloadErrors;
        }

        // In debug mode include limited exception info (do not include args)
        if (config('app.debug')) {
            $payload['exception'] = [
                'type' => get_class($e),
                'message' => $e->getMessage(),
                'trace' => collect($e->getTrace())->map(function ($frame) {
                    return Arr::except($frame, ['args']);
                })->all(),
            ];
        }

        return response()->json($payload, $status);
    }
}