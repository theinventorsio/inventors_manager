<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class LocalMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        # Check if IP it's a LAN IP
//        if (filter_var($request->ip(), FILTER_VALIDATE_IP,
//            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
//            return response('Unauthorized', 403);
//        }

        # Check if IP it's local
        if (!in_array($request->ip(), ['localhost', '127.0.0.1', '::1'])) {
            return response('Unauthorized - only local.', 405);
        }

        return $next($request);
    }
}
