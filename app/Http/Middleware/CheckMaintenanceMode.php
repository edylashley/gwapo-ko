<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\SystemSetting;

class CheckMaintenanceMode
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            // Always allow access to authentication routes
            $authRoutes = [
                'login', 'logout', 'password.request', 
                'password.email', 'password.reset', 'password.update',
                'verification.notice', 'verification.verify', 'verification.send'
            ];
            
            // Get current route name safely
            $routeName = $request->route() ? $request->route()->getName() : null;
            
            // Bypass maintenance mode for auth routes and admin users
            if (in_array($routeName, $authRoutes) || 
                (auth()->check() && auth()->user()->is_admin)) {
                return $next($request);
            }

            // Check if maintenance mode is enabled in database
            $maintenanceMode = SystemSetting::get('maintenance_mode', false);
            
            // If maintenance mode is enabled, show maintenance page
            if ($maintenanceMode === true || $maintenanceMode === 'true' || $maintenanceMode === '1') {
                return response()->view('maintenance', [], 503);
            }

            return $next($request);
            
        } catch (\Exception $e) {
            // Log the error and allow access if there's an issue
            \Log::error('Maintenance mode error: ' . $e->getMessage());
            return $next($request);
        }
    }
}