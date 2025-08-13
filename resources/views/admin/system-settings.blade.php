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

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.15);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.18);
            transition: transform 0.3s, box-shadow 0.3s, background 0.3s;
            opacity: 0;
            transform: translateY(40px);
            animation: fadeInUp 0.8s cubic-bezier(0.23, 1, 0.32, 1) forwards;
        }
        
        .glass-card.card-delay-1 { animation-delay: 0.1s; }
        .glass-card.card-delay-2 { animation-delay: 0.25s; }
        .glass-card.card-delay-3 { animation-delay: 0.4s; }
        .glass-card.card-delay-4 { animation-delay: 0.55s; }
        
        .glass-card:hover {
            transform: translateY(-4px) scale(1.03);
            box-shadow: 0 12px 32px 0 #00c6ff55;
        }

        body {
            margin: 0 !important;
            padding: 0 !important;
        }

        /* Toggle Switch Styles */
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 44px;
            height: 24px;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        
        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 24px;
        }

        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked + .toggle-slider {
            background-color: #10b981;
        }

        input:focus + .toggle-slider {
            box-shadow: 0 0 1px #10b981;
        }

        input:checked + .toggle-slider:before {
            transform: translateX(20px);
        }

        /* Tooltip styles */
        .tooltip {
            position: relative;
            display: inline-block;
        }

        .tooltip .tooltiptext {
            visibility: hidden;
            width: 300px;
            background-color: #333;
            color: #fff;
            text-align: left;
            border-radius: 6px;
            padding: 10px;
            position: absolute;
            z-index: 1;
            bottom: 125%;
            left: 50%;
            margin-left: -150px;
            opacity: 0;
            transition: opacity 0.3s;
            font-size: 14px;
            line-height: 1.4;
        }

        .tooltip .tooltiptext::after {
            content: "";
            position: absolute;
            top: 100%;
            left: 50%;
            margin-left: -5px;
            border-width: 5px;
            border-style: solid;
            border-color: #333 transparent transparent transparent;
        }

        .tooltip:hover .tooltiptext {
            visibility: visible;
            opacity: 1;
        }
    </style>
</head>
<body style="background: #064e3b;;" class="min-h-screen">
    <!-- Navigation -->
    @include('components.navigation', ['pageTitle' => 'System Settings'])

    <!-- Main Content -->
    <div class="main-content px-4 pt-6 pb-10 mt-24 transition-all duration-300 ml-64" id="mainContent">
            <div class="max-w-7xl  mx-auto">
                
                <!-- Header Section -->
            <div class="glass-card card-delay-1 overflow-hidden mb-8">
                <div class="bg-gradient-to-r from-emerald-600 via-green-600 to-emerald-700 px-10 py-8">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-3xl font-bold text-white flex items-center">
                                <span class="mr-3"></span>System Settings
                            </h1>
                            <p class="text-emerald-100 mt-2">Configure application-wide settings and preferences</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Settings Form -->
            <div class="glass-card card-delay-2 p-10">
                <form id="systemSettingsForm" class="space-y-2">
                    @csrf
                    
                    <!-- Application Settings -->
                        <h3 class="text-2xl font-extrabold text-white mb-8 tracking-wide text-center drop-shadow-lg">Application Settings</h3>
                        <h1 class="text-4xl font-extrabold text-green-400 mb-10 tracking-wide text-center"
                            style="text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);"
                            >Budget Control System
                        </h1>

                    <!-- User Registration Settings -->
                    <div class="space-y-4">
                        <h3 class="text-xl font-bold text-white mb-4 mt-8"
                            style="text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);"
                            >User Registration</h3>

                        <div class="flex items-center justify-between">
                            <label for="allow_user_registration" class="text-lg text-white">
                                Allow new users to register accounts
                            </label>
                            <label class="toggle-switch switch-green-700">
                                <input type="checkbox"
                                       id="allow_user_registration"
                                       name="allow_user_registration">
                                <span class="toggle-slider"></span>
                            </label>
                        </div>

                        <div class="flex items-center justify-between">
                            <label for="require_email_verification" class="text-lg text-white">
                                Require email verification for new accounts
                            </label>
                            <label class="toggle-switch switch-red-700">
                                <input type="checkbox"
                                       id="require_email_verification"
                                       name="require_email_verification">
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                    </div>

                    <!-- System Maintenance -->
                    <div class="space-y-4">
                        <h3 class="text-xl font-bold text-white mb-4 mt-8"
                            style="text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);"
                            >System Maintenance</h3>

                        <div class="flex items-center justify-between">
                            <label for="maintenance_mode" class="text-lg text-white">
                                Enable maintenance mode (non-admin users will be blocked)
                            </label>
                            <label class="toggle-switch switch-green-700">
                                <input type="checkbox"
                                       id="maintenance_mode"
                                       name="maintenance_mode">
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-between items-center pt-6 border-t border-white border-opacity-20">
                        <div class="flex items-center space-x-2">
                            <button type="button"
                                    id="clearCacheBtn"
                                    class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-1 rounded-lg font-medium transition-colors">
                                 Clear Cache
                            </button>
                            <div class="tooltip flex items-center">
                                <button type="button" class="text-white hover:text-yellow-300 transition-colors flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
                                    </svg>
                                </button>
                                <span class="tooltiptext">
                                    <strong>Clear Cache</strong><br>
                                    This will clear all cached data including:<br>
                                    • Application cache<br>
                                    • Route cache<br>
                                    • Configuration cache<br>
                                    • View cache<br><br>
                                    Use this when you experience issues or after making configuration changes.
                                </span>
                            </div>
                        </div>
                        
                        <div class="space-x-3">
                            <a href="/dashboard" 
                               class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-1 rounded-lg font-medium transition-colors inline-block">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-1 rounded-lg font-medium transition-colors">
                                 Save Settings
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Notification Container -->
    <div id="notification" class="fixed inset-0 flex items-center justify-center z-50 pointer-events-none opacity-0 transition-opacity duration-300">
        <div class="absolute inset-0 bg-black bg-opacity-50 backdrop-filter backdrop-blur-sm"></div>
        <div class="bg-white bg-opacity-90 rounded-xl shadow-2xl p-6 max-w-md w-full mx-4 relative z-10 transform transition-all duration-300 scale-95 opacity-0">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg id="notificationIcon" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 id="notificationTitle" class="text-lg font-medium text-gray-900">Success</h3>
                    <div class="mt-1">
                        <p id="notificationMessage" class="text-sm text-gray-600"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Show notification with animation
        function showNotification(message, type = 'success') {
            const notification = document.getElementById('notification');
            const notificationBox = notification.querySelector('div[class*="bg-white"]');
            const icon = document.getElementById('notificationIcon');
            const title = document.getElementById('notificationTitle');
            const messageEl = document.getElementById('notificationMessage');
            
            // Set notification content
            messageEl.textContent = message;
            
            // Set styles based on notification type
            if (type === 'success') {
                title.textContent = 'Success';
                title.className = 'text-lg font-medium text-green-800';
                messageEl.className = 'text-sm text-green-700';
                icon.className = 'h-8 w-8 text-green-600';
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />';
            } else {
                title.textContent = 'Error';
                title.className = 'text-lg font-medium text-red-800';
                messageEl.className = 'text-sm text-red-700';
                icon.className = 'h-8 w-8 text-red-600';
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />';
            }
            
            // Show notification with animation
            notification.classList.remove('opacity-0');
            notification.classList.add('opacity-100', 'pointer-events-auto');
            setTimeout(() => {
                notificationBox.classList.remove('scale-95', 'opacity-0');
                notificationBox.classList.add('scale-100', 'opacity-100');
            }, 10);
            
            // Auto-hide after 5 seconds
            setTimeout(() => {
                hideNotification();
            }, 1000);
        }
        
        // Hide notification with animation
        function hideNotification() {
            const notification = document.getElementById('notification');
            const notificationBox = notification.querySelector('div[class*="bg-white"]');
            
            notificationBox.classList.remove('scale-100', 'opacity-100');
            notificationBox.classList.add('scale-95', 'opacity-0');
            
            setTimeout(() => {
                notification.classList.remove('opacity-100', 'pointer-events-auto');
                notification.classList.add('opacity-0');
            }, 300);
        }
        
        // Close notification when clicking outside
        document.getElementById('notification').addEventListener('click', (e) => {
            if (e.target === document.getElementById('notification')) {
                hideNotification();
            }
        });

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
        
        // Hide notification with animation
        function hideNotification() {
            const notification = document.getElementById('notification');
            const notificationBox = notification.querySelector('div[class*="bg-white"]');
            
            notificationBox.classList.remove('scale-100', 'opacity-100');
            notificationBox.classList.add('scale-95', 'opacity-0');
            
            setTimeout(() => {
                notification.classList.remove('opacity-100', 'pointer-events-auto');
                notification.classList.add('opacity-0');
            }, 300);
        }
        
        // Close notification when clicking outside
        document.getElementById('notification').addEventListener('click', (e) => {
            if (e.target === document.getElementById('notification')) {
                hideNotification();
            }
        });

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

        // Show notification function is already defined at the top of the file
    </script>
</body>
</html>