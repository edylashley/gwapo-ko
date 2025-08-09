<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>System Settings - Budget Control</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        /* Reset any default margins/padding */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            margin: 0 !important;
            padding: 0 !important;
        }
    </style>
</head>
<body style="background: linear-gradient(135deg, #064e3b, #065f46, #10b981, #059669);" class="min-h-screen">
    <!-- Navigation -->
    @include('components.navigation', ['pageTitle' => 'System Settings'])

    <!-- Main Content -->
    <div class="main-content px-4 pt-6 pb-10 transition-all duration-300" style="margin-left: 256px;" id="mainContent">
        <div class="max-w-4xl mx-auto">
            
            <!-- Header Section -->
            <div class="bg-white bg-opacity-20 rounded-2xl shadow-xl border border-white border-opacity-30 mb-8 overflow-hidden">
                <div class="bg-gradient-to-r from-emerald-600 via-green-600 to-emerald-700 px-8 py-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-3xl font-bold text-white flex items-center">
                                <span class="mr-3">⚙️</span> System Settings
                            </h1>
                            <p class="text-emerald-100 mt-2">Configure application-wide settings and preferences</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Settings Form -->
            <div class="bg-white bg-opacity-20 rounded-2xl shadow-xl border border-white border-opacity-30 p-4">
                <form id="systemSettingsForm" class="space-y-2">
                    @csrf
                    
                    <!-- Application Settings -->
                    <div class="space-y-4">
                        <h3 class="text-xl font-bold text-white mb-4">Application Settings</h3>
                        
                        <div>
                            <label class="block text-sm font-medium text-white mb-2">Application Name</label>
                            <input type="text" 
                                   id="app_name"
                                   name="app_name" 
                                   value="Budget Control System" 
                                   class="w-full rounded-lg border-gray-300 focus:ring-green-500 focus:border-green-500 text-gray-900 bg-white bg-opacity-90">
                        </div>
                    </div>

                    <!-- User Registration Settings -->
                    <div class="space-y-4">
                        <h3 class="text-xl font-bold text-white mb-4">User Registration</h3>
                        
                        <div class="flex items-center">
                            <input type="checkbox" 
                                   id="allow_user_registration"
                                   name="allow_user_registration" 
                                   class="w-4 h-4 text-green-600 bg-white bg-opacity-90 border-gray-300 rounded focus:ring-green-500">
                            <label for="allow_user_registration" class="ml-3 text-sm text-white">
                                Allow new users to register accounts
                            </label>
                        </div>
                        
                        <div class="flex items-center">
                            <input type="checkbox" 
                                   id="require_email_verification"
                                   name="require_email_verification" 
                                   class="w-4 h-4 text-green-600 bg-white bg-opacity-90 border-gray-300 rounded focus:ring-green-500">
                            <label for="require_email_verification" class="ml-3 text-sm text-white">
                                Require email verification for new accounts
                            </label>
                        </div>
                    </div>

                    <!-- System Maintenance -->
                    <div class="space-y-4">
                        <h3 class="text-xl font-bold text-white mb-4">System Maintenance</h3>
                        
                        <div class="flex items-center">
                            <input type="checkbox" 
                                   id="maintenance_mode"
                                   name="maintenance_mode" 
                                   class="w-4 h-4 text-green-600 bg-white bg-opacity-90 border-gray-300 rounded focus:ring-green-500">
                            <label for="maintenance_mode" class="ml-3 text-sm text-white">
                                Enable maintenance mode (non-admin users will be blocked)
                            </label>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-between items-center pt-6 border-t border-white border-opacity-20">
                        <button type="button" 
                                id="clearCacheBtn"
                                class="bg-yellow-600 hover:bg-yellow-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                             Clear Cache
                        </button>
                        
                        <div class="space-x-3">
                            <a href="/dashboard" 
                               class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-medium transition-colors inline-block">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                                 Save Settings
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Load current settings when page loads
        document.addEventListener('DOMContentLoaded', function() {
            loadSystemSettings();
            
            // Handle form submission
            document.getElementById('systemSettingsForm').addEventListener('submit', function(e) {
                e.preventDefault();
                saveSystemSettings();
            });
            
            // Handle clear cache button
            document.getElementById('clearCacheBtn').addEventListener('click', function() {
                clearApplicationCache();
            });
        });

        // Load system settings from API
        function loadSystemSettings() {
            fetch('/admin/system-settings', {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Failed to load settings');
                }
                return response.json();
            })
            .then(settings => {
                document.getElementById('app_name').value = settings.app_name || 'Budget Control System';
                document.getElementById('allow_user_registration').checked = settings.allow_user_registration !== false;
                document.getElementById('require_email_verification').checked = settings.require_email_verification === true;
                document.getElementById('maintenance_mode').checked = settings.maintenance_mode === true;
            })
            .catch(error => {
                console.error('Error loading settings:', error);
                showNotification('Error loading settings: ' + error.message, 'error');
            });
        }

        // Save system settings to API
        function saveSystemSettings() {
            const formData = {
                app_name: document.getElementById('app_name').value,
                allow_user_registration: document.getElementById('allow_user_registration').checked,
                require_email_verification: document.getElementById('require_email_verification').checked,
                maintenance_mode: document.getElementById('maintenance_mode').checked
            };

            fetch('/admin/system-settings', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify(formData)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Failed to save settings');
                }
                return response.json();
            })
            .then(data => {
                showNotification(data.message || 'Settings saved successfully!', 'success');
            })
            .catch(error => {
                console.error('Error saving settings:', error);
                showNotification('Error saving settings: ' + error.message, 'error');
            });
        }

        // Clear application cache
        function clearApplicationCache() {
            if (!confirm('Are you sure you want to clear the application cache?')) {
                return;
            }

            fetch('/admin/system-settings/clear-cache', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Failed to clear cache');
                }
                return response.json();
            })
            .then(data => {
                showNotification(data.message || 'Cache cleared successfully!', 'success');
            })
            .catch(error => {
                console.error('Error clearing cache:', error);
                showNotification('Error clearing cache: ' + error.message, 'error');
            });
        }

        // Show notification
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg text-white font-medium ${
                type === 'success' ? 'bg-green-600' : 
                type === 'error' ? 'bg-red-600' : 
                'bg-blue-600'
            }`;
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 5000);
        }
    </script>
</body>
</html>
