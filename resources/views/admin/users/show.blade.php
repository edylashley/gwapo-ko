<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Details - {{ $user->name }} - Budget Control</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body style="background: #064e3b;" class="min-h-screen px-4 py-10" style="background: linear-gradient(135deg, #064e3b, #065f46, #10b981, #059669);">
    <!-- Navigation -->
    @include('components.navigation', ['pageTitle' => 'User Details'])

    <!-- Main Content -->
    <div class="pt-20">
        <div class="max-w-4xl mx-auto">
            
            <!-- Header Section -->
            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 mb-8 overflow-hidden">
                <div class="bg-gradient-to-r from-emerald-600 via-green-600 to-emerald-700 px-8 py-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center text-green-600 font-bold text-2xl mr-6 shadow-lg">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div>
                                <h1 class="text-3xl font-bold text-black">{{ $user->name }}</h1>
                                <p class="text-emerald-100 mt-1">Name</p>
                                <div class="flex items-center mt-2">
                                    @if($user->is_admin)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-white bg-opacity-20 text-white">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M9.383 3.076A1 1 0 0110 4v12a1 1 0 01-1.707.707L4.586 13H2a1 1 0 01-1-1V8a1 1 0 011-1h2.586l3.707-3.707a1 1 0 011.09-.217zM15.657 6.343a1 1 0 011.414 0A9.972 9.972 0 0119 12a9.972 9.972 0 01-1.929 5.657 1 1 0 11-1.414-1.414A7.971 7.971 0 0017 12a7.971 7.971 0 00-1.343-4.243 1 1 0 010-1.414z" clip-rule="evenodd"/>
                                            </svg>
                                            Administrator
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-white bg-opacity-20 text-gray">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                            </svg>
                                            User
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center space-x-4">
                            <a href="{{ route('users.edit', $user) }}" 
                               class="bg-green-500 text-emerald-700 px-6 py-3 rounded-lg font-semibold hover:bg-emerald-50 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                                <svg class="w-5 h-5 inline-block mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                                </svg>
                                Edit User
                            </a>
                            <a href="{{ route('users.index') }}" 
                               class="bg-gray-700 bg-opacity-20 text-black px-6 py-3 rounded-lg font-semibold hover:bg-opacity-30 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                                <svg class="w-5 h-5 inline-block mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"/>
                                </svg>
                                Back to Users
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Information Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                
                <!-- Basic Information -->
                <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                            </svg>
                            Basic Information
                        </h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Full Name</label>
                            <p class="text-lg text-gray-900 mt-1">{{ $user->name }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Username</label>
                            <p class="text-lg text-gray-900 mt-1">{{ $user->username }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Email Address</label>
                            <p class="text-lg text-gray-900 mt-1">{{ $user->email }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Role</label>
                            <div class="mt-2">
                                @if($user->is_admin)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-emerald-100 text-emerald-800">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M9.383 3.076A1 1 0 0110 4v12a1 1 0 01-1.707.707L4.586 13H2a1 1 0 01-1-1V8a1 1 0 011-1h2.586l3.707-3.707a1 1 0 011.09-.217zM15.657 6.343a1 1 0 011.414 0A9.972 9.972 0 0119 12a9.972 9.972 0 01-1.929 5.657 1 1 0 11-1.414-1.414A7.971 7.971 0 0017 12a7.971 7.971 0 00-1.343-4.243 1 1 0 010-1.414z" clip-rule="evenodd"/>
                                        </svg>
                                        Administrator
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                        </svg>
                                        User
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Account Information -->
                <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                            </svg>
                            Account Information
                        </h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Account Created</label>
                            <p class="text-lg text-gray-900 mt-1">{{ $user->created_at->format('F j, Y \a\t g:i A') }}</p>
                            <p class="text-sm text-gray-500 mt-1">{{ $user->created_at->diffForHumans() }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Last Updated</label>
                            <p class="text-lg text-gray-900 mt-1">{{ $user->updated_at->format('F j, Y \a\t g:i A') }}</p>
                            <p class="text-sm text-gray-500 mt-1">{{ $user->updated_at->diffForHumans() }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Account Status</label>
                            <div class="mt-2">
                                @if($user->is_active ?? true)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm4.293-5.707a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 15.586l3.293-3.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                        Inactive
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">User ID</label>
                            <p class="text-lg text-gray-900 mt-1 font-mono">#{{ $user->id }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Admin Privileges Card (if admin) -->
            @if($user->is_admin)
                <div class="mt-8 bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                    <div class="bg-emerald-50 px-6 py-4 border-b border-emerald-200">
                        <h3 class="text-lg font-semibold text-emerald-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Administrator Privileges
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-emerald-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-gray-700">User Management</span>
                            </div>
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-emerald-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-gray-700">Project Management</span>
                            </div>
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-emerald-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-gray-700">System Settings</span>
                            </div>
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-emerald-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-gray-700">System Logs</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Danger Zone (if not current user) -->
            @if($user->id !== auth()->id())
                <div class="mt-8 bg-white rounded-2xl shadow-xl border border-red-200 overflow-hidden">
                    <div class="bg-red-50 px-6 py-4 border-b border-red-200">
                        <h3 class="text-lg font-semibold text-red-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            Danger Zone
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-sm font-medium text-gray-900">Delete User Account</h4>
                                <p class="text-sm text-gray-500 mt-1">Permanently delete this user account. This action cannot be undone.</p>
                            </div>
                            <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline-block" 
                                  onsubmit="return confirm('Are you sure you want to delete {{ $user->name }}? This action cannot be undone.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors duration-200">
                                    Delete User
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</body>
</html>
