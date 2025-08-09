<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\SystemSetting;

class CheckRegistrationEnabled
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user registration is enabled
        $registrationEnabled = SystemSetting::get('allow_user_registration', true);

        if (!$registrationEnabled) {
            // If it's an AJAX request, return JSON response
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'User registration is currently disabled.'
                ], 403);
            }

            // Otherwise redirect to login with error message
            return redirect()->route('login')->with('error', 'User registration is currently disabled.');
        }

        return $next($request);
    }
}
