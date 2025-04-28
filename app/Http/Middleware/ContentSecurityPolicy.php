<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ContentSecurityPolicy
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Generate a random nonce
        $nonce = base64_encode(random_bytes(16));

        // Attach the nonce to the request (so you can use it in views)
        $request->attributes->set('csp_nonce', $nonce);

        // Set CSP header
        $csp = "script-src 'strict-dynamic' 'nonce-{$nonce}' 'unsafe-inline' http: https:; ";
        $csp .= "object-src 'none'; ";
        $csp .= "base-uri 'none'; ";
        $csp .= "require-trusted-types-for 'script'; ";
        $csp .= "report-uri https://moevan.com/csp-report;";

        $response->headers->set('Content-Security-Policy', $csp);

        return $response;
    }
}
