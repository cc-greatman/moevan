<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class EnsureSubDomain
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();

        if ($host === config('app.admin_domain')) {
            if (!Auth::guard('admin')->check()) { // Check if the user is logged in as an admin
                Log::warning('Unauthorized access attempt to admin subdomain', [
                    'user_id' => Auth::guard('web')->id(),
                    'ip' => $request->ip(),
                    'url' => $request->fullUrl(),
                ]);
                return response()->view('errors.403', [], Response::HTTP_FORBIDDEN);
            }
        } elseif ($host === config('app.user_domain')) {
            if (!Auth::guard('web')->check()) { // Check if the user is logged in as a regular user
                Log::warning('Unauthorized access attempt to user subdomain', [
                    'user_id' => Auth::guard('admin')->id(),
                    'ip' => $request->ip(),
                    'url' => $request->fullUrl(),
                ]);
                return response()->view('errors.403', [], Response::HTTP_FORBIDDEN);
            }
        }

        return $next($request);
    }
}
