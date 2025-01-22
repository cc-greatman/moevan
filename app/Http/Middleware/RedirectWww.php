<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectWww
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();

        if (substr($host, 0, 4) === 'www.') {
            $host = substr($host, 4);
            return redirect()->to($request->getScheme().'://'.$host.$request->getRequestUri(), 301);
        }

        return $next($request);
    }
}
