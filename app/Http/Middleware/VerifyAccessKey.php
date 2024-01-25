<?php

namespace App\Http\Middleware;

use Closure;
use Essa\APIToolKit\Api\ApiResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyAccessKey
{
    use ApiResponse;

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Request must have x-access-key header, and it must be equal to the value of ACCESS_KEY
        // if ($request->hasHeader('x-access-key') && $request->header('x-access-key') === config('app.access_key')) {
            return $next($request);
        // }

        // return $this->responseUnAuthorized('Invalid access key');
    }
}
