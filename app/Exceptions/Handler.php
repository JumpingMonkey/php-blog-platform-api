<?php

namespace App\Exceptions;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
        $this->renderable(function (ValidationException $e) {
            return new JsonResponse([
                'message' => 'The given data was invalid.',
                'errors' => $e->errors(),
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  Request  $request
     * @param  Throwable  $e
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $e)
    {
        if ($e instanceof ModelNotFoundException || 
            ($e instanceof NotFoundHttpException && $e->getPrevious() instanceof ModelNotFoundException)) {
            return new JsonResponse([
                'message' => 'Resource not found.',
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        if ($request->expectsJson()) {
            if (config('app.debug') && !app()->environment('testing')) {
                return new JsonResponse([
                    'message' => 'An error occurred while processing your request.',
                    'error' => $e->getMessage(),
                    'exception' => get_class($e),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTrace()
                ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
            }
            
            return new JsonResponse([
                'message' => 'An error occurred while processing your request.',
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        return parent::render($request, $e);
    }
}
