<?php

namespace App\Http\Middleware;

use App\Traits\APIResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserMustHaveMoney
{
    use APIResponse;

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()->balance > 0) {
            return $next($request);
        }

        return $this->responseWithCustomError(
            'Insufficient funds',
            'You do not have enough money to perform this action.',
            Response::HTTP_PAYMENT_REQUIRED
        );
    }
}
