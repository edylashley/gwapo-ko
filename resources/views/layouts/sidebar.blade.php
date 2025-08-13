
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Budget Control')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    @stack('styles')
</head>
<body class="bg-gray-50">
    <!-- Include Sidebar -->
    @include('components.sidebar')
    
    <!-- Main Content Area -->
    <div class="ml-64 min-h-screen">
        <!-- Top Bar (Desktop) -->
        <div class="hidden md:block bg-white border-b border-gray-200 px-6 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">@yield('page-title', 'Dashboard')</h1>
                    <p class="text-sm text-gray-600">@yield('page-description', 'Welcome to Budget Control')</p>
                </div>
                <div class="flex items-center space-x-4">
                    <!-- Notifications -->
                    <button class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M9 11h.01M9 7v1"></path>
                        </svg>
                    </button>
                    
                    <!-- User Info -->
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-emerald-600 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div class="hidden sm:block">
                            <p class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-gray-500">{{ auth()->user()->is_admin ? 'Administrator' : 'User' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Mobile Top Bar -->
        <div class="md:hidden pt-16">
            <!-- Content will be pushed down by fixed mobile header -->
        </div>
        
        <!-- Page Content -->
        <main class="p-6">
            @yield('content')
        </main>
    </div>
    
    @stack('scripts')
</body>
</html>