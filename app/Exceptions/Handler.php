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

    /**
     * Render an exception into an HTTP response.
     *
     * This method returns a consistent JSON structure for API requests:
     * {
     *   "error": "Message",
     *   "code": 404,
     *   // optional: "errors": { field: [messages] } for validation
     *   // optional: "exception": { debug info } when APP_DEBUG=true
     * }
     */
    public function render($request, Throwable $exception)
    {
        // If the request expects JSON (API call), return standardized JSON responses.
        if ($this->isApiRequest($request)) {
            return $this->apiExceptionResponse($request, $exception);
        }

        // Fallback to the default HTML / web rendering.
        return parent::render($request, $exception);
    }

    /**
     * Decide if this request should receive JSON responses.
     */
    protected function isApiRequest(Request $request): bool
    {
        // Consider it an API request if:
        // - The request explicitly expects JSON, OR
        // - The Accept header prefers JSON, OR
        // - The path starts with "api/"
        return $request->expectsJson()
            || $request->wantsJson()
            || str_starts_with($request->path(), 'api/');
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