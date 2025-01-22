<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EnsureSingleLoginSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $id = Auth::guard('web')->id();
            $user = User::findOrFail($id);
            $currentSessionId = session()->getId();

            // Check if the user's session ID is stored in the database
            if ($user->session_id === null) {
                // If the session ID is null, it's likely a session expiration or a fresh login
                $user->session_id = $currentSessionId;
                $user->save();
            } elseif ($user->session_id !== $currentSessionId) {
                Log::warning('Session mismatch detected. Logging out user.');

                // If the session IDs don't match, it's likely a session conflict
                Auth::logout();
                return redirect('/login')->withErrors(['message' => 'Your session has been terminated because your account was logged in from another device.']);
            }
        }

        return $next($request);
    }
}
