<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SystemSetting;

class SystemSettingsController extends Controller
{
    /**
     * Get all system settings
     */
    public function index()
    {
        $settings = [
            'app_name' => SystemSetting::get('app_name', 'Budget Control System'),
            'allow_user_registration' => SystemSetting::get('allow_user_registration', true),
            'require_email_verification' => SystemSetting::get('require_email_verification', false),
            'maintenance_mode' => SystemSetting::get('maintenance_mode', false),
        ];

        
        // If request wants JSON, return JSON
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json($settings);
        }

        // Otherwise return the view
        return view('admin.system-settings', compact('settings'));
    }

    /**
     * Update system settings
     */
    public function update(Request $request)
    {
        $request->validate([
            'allow_user_registration' => 'boolean',
            'require_email_verification' => 'boolean',
            'maintenance_mode' => 'boolean',
        ]);

        // Update each setting
        SystemSetting::set('allow_user_registration', $request->boolean('allow_user_registration'), 'boolean', 'Allow new users to register accounts');
        SystemSetting::set('require_email_verification', $request->boolean('require_email_verification'), 'boolean', 'Require email verification for new accounts');
        SystemSetting::set('maintenance_mode', $request->boolean('maintenance_mode'), 'boolean', 'Enable maintenance mode for non-admin users');

        return response()->json([
            'success' => true,
            'message' => 'System settings updated successfully!'
        ]);
    }

    /**
     * Clear application cache
     */
    public function clearCache()
    {
        try {
            \Artisan::call('cache:clear');
            \Artisan::call('config:clear');
            \Artisan::call('view:clear');

            return response()->json([
                'success' => true,
                'message' => 'Application cache cleared successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error clearing cache: ' . $e->getMessage()
            ], 500);
        }
    }


}