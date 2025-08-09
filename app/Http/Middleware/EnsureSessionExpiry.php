<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class EnsureSessionExpiry
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only check for authenticated users
        if (Auth::check()) {
            // Check if user logged in without "remember me"
            $rememberToken = Auth::user()->remember_token;
            $hasRememberCookie = $request->hasCookie(Auth::getRecallerName());

            // If no remember token and no remember cookie, this is a session-only login
            if (empty($rememberToken) && !$hasRememberCookie) {
                // Check if session has been inactive for too long
                $lastActivity = Session::get('last_activity', time());
                $sessionLifetime = config('session.lifetime') * 60; // Convert minutes to seconds

                if (time() - $lastActivity > $sessionLifetime) {
                    Auth::logout();
                    Session::invalidate();
                    Session::regenerateToken();

                    return redirect('/login')->with('error', 'Your session has expired. Please login again.');
                }

                // Update last activity
                Session::put('last_activity', time());
            }
        }

        return $next($request);
    }
}
