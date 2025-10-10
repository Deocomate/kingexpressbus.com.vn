<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceHttpsAndWww
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only apply in production
        if (! app()->environment('local')) {
            $host = $request->getHost();
            $scheme = $request->getScheme();

            // Check if we need to redirect to HTTPS
            $needsHttps = $scheme !== 'https';

            // Check if we need to redirect to www (if domain doesn't start with www)
            $needsWww = ! str_starts_with($host, 'www.') && ! filter_var($host, FILTER_VALIDATE_IP);

            if ($needsHttps || $needsWww) {
                $newHost = $needsWww ? 'www.' . $host : $host;
                $newUrl = 'https://' . $newHost . $request->getRequestUri();

                return redirect($newUrl, 301);
            }
        }

        return $next($request);
    }
}
