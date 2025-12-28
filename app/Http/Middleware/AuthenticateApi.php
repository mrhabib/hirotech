<?php

namespace App\Http\Middleware;

use App\Enums\HttpStatusCode;
use App\Http\Resources\ApiResponseResource;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticateApi
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  ...$guards
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        if (empty($guards)) {
            $guards = [config('auth.defaults.guard')];
        }

        $authenticated = false;
        $authenticatedGuard = null;

        foreach ($guards as $guard) {
            $guard = $guard ?: config('auth.defaults.guard');

            if (Auth::guard($guard)->check()) {
                $authenticated = true;
                $authenticatedGuard = $guard;
                break;
            }
        }

        if (!$authenticated) {
            return new ApiResponseResource(
                null,
                'Unauthenticated. Please login to access this resource.',
                HttpStatusCode::UNAUTHORIZED
            );
        }

        if ($authenticatedGuard) {
            Auth::shouldUse($authenticatedGuard);
        }

        return $next($request);
    }
}

