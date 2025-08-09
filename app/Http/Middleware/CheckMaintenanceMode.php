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
        // Check if maintenance mode is enabled
        $maintenanceMode = SystemSetting::get('maintenance_mode', false);

        if ($maintenanceMode) {
            // Allow admin users to bypass maintenance mode
            if (auth()->check() && auth()->user()->is_admin) {
                return $next($request);
            }

            // Show maintenance page for non-admin users
            return response()->view('maintenance', [], 503);
        }

        return $next($request);
    }
}
