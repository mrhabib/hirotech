<?php

namespace App\Exceptions;

use App\Enums\HttpStatusCode;
use App\Http\Resources\ApiResponseResource;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * Convert an authentication exception into a response.
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        $isApiRequest = $request->expectsJson()
            || $request->is('api/*')
            || str_starts_with($request->path(), 'api/')
            || str_starts_with($request->getPathInfo(), '/api')
            || $request->segment(1) === 'api'
            || ($request->route() && str_starts_with($request->route()->getName() ?? '', 'api.'));

        if ($isApiRequest) {
            return new ApiResponseResource(
                null,
                'Unauthenticated. Please login to access this resource.',
                HttpStatusCode::UNAUTHORIZED
            );
        }

        try {
            return parent::unauthenticated($request, $exception);
        } catch (\Symfony\Component\Routing\Exception\RouteNotFoundException $e) {
            return new ApiResponseResource(
                null,
                'Unauthenticated. Please login to access this resource.',
                HttpStatusCode::UNAUTHORIZED
            );
        }
    }

    public function render($request, Throwable $e)
    {
        $isApiRequest = $request->expectsJson()
            || $request->is('api/*')
            || str_starts_with($request->path(), 'api/')
            || str_starts_with($request->getPathInfo(), '/api')
            || $request->segment(1) === 'api'
            || ($request->route() && str_starts_with($request->route()->getName() ?? '', 'api.'));

        if ($isApiRequest) {
            if ($e instanceof AuthenticationException) {
                return new ApiResponseResource(
                    null,
                    'Unauthenticated. Please login to access this resource.',
                    HttpStatusCode::UNAUTHORIZED
                );
            }

            if ($e instanceof ValidationException) {
                return new ApiResponseResource(
                    null,
                    'Validation failed',
                    HttpStatusCode::UNPROCESSABLE_ENTITY,
                    ['errors' => $e->errors()]
                );
            }

            if ($e instanceof EventNotFoundException) {
                return $e->render();
            }

            if ($e instanceof \App\Exceptions\ReservationActivationException) {
                return $e->render();
            }

            if ($e instanceof \App\Exceptions\ReservationCancellationException) {
                return $e->render();
            }

            if ($e instanceof \App\Exceptions\CapacityExceededException) {
                return $e->render();
            }

            if ($e instanceof \App\Exceptions\UserAlreadyReservedException) {
                return $e->render();
            }

            return new ApiResponseResource(
                null,
                $e->getMessage() ?: 'Something went wrong',
                HttpStatusCode::INTERNAL_SERVER_ERROR
            );
        }

        return parent::render($request, $e);
    }
}
