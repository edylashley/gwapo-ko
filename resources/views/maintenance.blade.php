<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Maintenance - Budget Control System</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body style="background: #064e3b;" class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full mx-auto">
        <div class="bg-white rounded-2xl shadow-xl p-8 text-center">
            <!-- Maintenance Icon -->
            <div class="w-20 h-20 mx-auto mb-6 bg-orange-100 rounded-full flex items-center justify-center">
                <svg class="w-10 h-10 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/>
                </svg>
            </div>

            <!-- Title -->
            <h1 class="text-2xl font-bold text-gray-800 mb-4">
                🔧 System Maintenance
            </h1>

            <!-- Message -->
            <p class="text-gray-600 mb-6 leading-relaxed">
                We're currently performing scheduled maintenance to improve your experience. 
                The system will be back online shortly.
            </p>

            <!-- Status -->
            <div class="bg-orange-50 border border-orange-200 rounded-lg p-4 mb-6">
                <div class="flex items-center justify-center">
                    <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-orange-600 mr-3"></div>
                    <span class="text-orange-800 font-medium">Maintenance in Progress</span>
                </div>
            </div>

            <!-- Contact Info -->
            <div class="text-sm text-gray-500">
                <p>Need immediate assistance?</p>
                <p class="font-medium text-gray-700 mt-1">Contact your system administrator</p>
            </div>

            <!-- Auto Refresh -->
            <div class="mt-6 pt-6 border-t border-gray-200">
                <button onclick="location.reload()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                    Check Again
                </button>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-6 text-sm text-gray-500">
            <p>Budget Control System</p>
        </div>
    </div>

    <!-- Auto refresh every 30 seconds -->
    <script>
        setTimeout(() => {
            location.reload();
        }, 30000);
    </script>
</body>
</html>
