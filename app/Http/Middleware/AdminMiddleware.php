<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
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
        // Check if user is authenticated and is an admin
        if (!Auth::check() || !Auth::user()->is_admin) {
            // If not admin, redirect to dashboard with error message
            return redirect()->route('dashboard')->with('error', 'Access denied. Admin privileges required.');
        }

        return $next($request);
    }
}
