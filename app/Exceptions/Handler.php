<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
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

        $this->renderable(function (NotFoundHttpException $e) {
            return new JsonResponse([
                'message' => 'Resource not found.',
            ], JsonResponse::HTTP_NOT_FOUND);
        });

        $this->renderable(function (Throwable $e) {
            if (request()->expectsJson()) {
                return new JsonResponse([
                    'message' => 'An error occurred while processing your request.',
                    'error' => $e->getMessage()
                ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
            }
        });
    }
}
