<?php

namespace App\Http\Middleware;

use App\Exceptions\ApiException;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->user('api') || !$request->user('api')->is_admin) {
            throw new ApiException('Forbidden.', Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
