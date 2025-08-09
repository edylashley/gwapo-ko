<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\SystemSetting;

class CheckEmailVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if email verification is required
        $requireVerification = SystemSetting::get('require_email_verification', false);

        if ($requireVerification && auth()->check()) {
            $user = auth()->user();

            // Skip verification for admin users
            if ($user->is_admin) {
                return $next($request);
            }

            // Check if user's email is verified
            if (!$user->hasVerifiedEmail()) {
                // Allow access to verification routes
                $allowedRoutes = [
                    'verification.notice',
                    'verification.verify',
                    'verification.send',
                    'logout'
                ];

                if (!in_array($request->route()->getName(), $allowedRoutes)) {
                    return redirect()->route('verification.notice');
                }
            }
        }

        return $next($request);
    }
}
