<?php

namespace App\Http\Middleware;

use App\Helpers\Transformer;
use Closure;

class GuestMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!auth()->guest()) {
            return Transformer::fail('Only for guest user.', null, 403);
        }

        return $next($request);
    }
}
