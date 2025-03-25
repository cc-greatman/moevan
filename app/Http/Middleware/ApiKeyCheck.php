<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiKeyCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->header('Authorization');

        if (!$apiKey || !str_starts_with($apiKey, 'Bearer ')) {
            return response()->json(['status' => false, 'code' => 401, 'data' => ['message' => 'API key not found or invalid format'],], 401);
        }

        // Extract the API key (remove "Bearer " prefix)
        $apiKey = str_replace('Bearer ', '', $apiKey);

        // Validate API key
        $user = User::where('api_key', $apiKey)->first();

        if (!$user) {
            return response()->json(['status' => false, 'code' => 401, 'data' => ['message' => 'Unauthorized Access: API key not found or does not exist'],], 401);
        }

        // Attach user to request for further use
        $request->merge(['authenticated_user' => $user]);
        return $next($request);
    }
}
