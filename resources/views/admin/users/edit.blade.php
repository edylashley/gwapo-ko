<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit User - {{ $user->name }} - Budget Control</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
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
            background: white;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.1);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.18);
            transition: transform 0.3s, box-shadow 0.3s;
            opacity: 0;
            transform: translateY(40px);
            animation: fadeInUp 0.8s cubic-bezier(0.23, 1, 0.32, 1) forwards;
        }
        .glass-card:hover {
            transform: translateY(-4px) scale(1.03);
            box-shadow: 0 12px 32px 0 rgba(0, 198, 255, 0.1);
        }
        .card-delay-1 { animation-delay: 0.15s; }
    </style>
</head>
<body style="background: #064e3b;" class="min-h-screen px-4 py-10" style="background: linear-gradient(135deg, #064e3b, #065f46, #10b981, #059669);">
    <!-- Navigation -->
    @include('components.navigation', ['pageTitle' => 'Edit User'])

    <!-- Main Content -->
    <div class="main-content px-4 pt-10 pb-10 transition-all duration-300" id="mainContent">
        <div class="max-w-5xl mx-auto">
            
            <!-- Header Section -->
            <div class="glass-card overflow-hidden mb-8">
                <div class="bg-gradient-to-r from-emerald-600 via-green-600 to-emerald-700 px-8 py-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-gray-600 bg-opacity-20 rounded-full flex items-center justify-center text-green-600 font-bold text-lg mr-4 shadow-lg">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div>
                                <h1 class="text-3xl font-bold text-black">Edit User</h1>
                                <p class="text-emerald-100 mt-2">Update {{ $user->name }}'s information</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-4">
                            <a href="{{ route('users.show', $user) }}" 
                               class="bg-green-500 text-black px-6 py-3 rounded-lg font-semibold hover:bg-opacity-30 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                                <svg class="w-5 h-5 inline-block mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                </svg>
                                View User
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

            <!-- Error Messages -->
            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-800 px-6 py-4 rounded-lg mb-6 shadow-sm">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <h4 class="font-semibold mb-2">Please correct the following errors:</h4>
                            <ul class="list-disc list-inside space-y-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Edit User Form -->
            <div class="glass-card overflow-hidden card-delay-1">
                <form action="{{ route('users.update', $user) }}" method="POST" class="p-8">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Full Name -->
                        <div class="md:col-span-2">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                Full Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name', $user->name) }}"
                                   required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors duration-200"
                                   placeholder="Enter full name">
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                Email Address <span class="text-red-500">*</span>
                            </label>
                            <input type="email" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email', $user->email) }}"
                                   required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors duration-200"
                                   placeholder="Enter email address">
                        </div>

                        <!-- Username -->
                        <div>
                            <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                                Username <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="username" 
                                   name="username" 
                                   value="{{ old('username', $user->username) }}"
                                   required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors duration-200"
                                   placeholder="Enter username">
                        </div>

                        <!-- Password -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                New Password
                            </label>
                            <input type="password" 
                                   id="password" 
                                   name="password" 
                                   minlength="6"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors duration-200"
                                   placeholder="Leave blank to keep current password">
                            <p class="text-sm text-gray-500 mt-1">Leave blank to keep the current password</p>
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                                Confirm New Password
                            </label>
                            <input type="password" 
                                   id="password_confirmation" 
                                   name="password_confirmation" 
                                   minlength="6"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors duration-200"
                                   placeholder="Confirm new password">
                        </div>

                        <!-- Admin Privileges -->
                        @if($user->id !== auth()->id())
                            <div class="md:col-span-2">
                                <div class="flex items-center">
                                    <input type="checkbox" 
                                           id="is_admin" 
                                           name="is_admin" 
                                           value="1"
                                           {{ old('is_admin', $user->is_admin) ? 'checked' : '' }}
                                           class="w-4 h-4 text-emerald-600 bg-gray-100 border-gray-300 rounded focus:ring-emerald-500 focus:ring-2">
                                    <label for="is_admin" class="ml-3 text-sm font-medium text-gray-700">
                                        Grant Administrator Privileges
                                    </label>
                                </div>
                                <p class="mt-2 text-sm text-gray-500">
                                    Administrators can manage users, projects, and system settings.
                                </p>
                            </div>
                        @else
                            <div class="md:col-span-2">
                                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-yellow-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        <div>
                                            <p class="text-sm font-medium text-yellow-800">
                                                You cannot modify your own admin privileges
                                            </p>
                                            <p class="text-sm text-yellow-700 mt-1">
                                                Ask another administrator to change your role if needed.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="is_admin" value="{{ $user->is_admin ? '1' : '0' }}">
                            </div>
                        @endif
                    </div>

                    <!-- Form Actions -->
                    <div class="mt-8 flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                        <a href="{{ route('users.show', $user) }}" 
                           class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-400 transition-colors duration-200">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="px-6 py-3 bg-green-600 text-black font-semibold rounded-lg hover:from-emerald-700 hover:to-green-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                            <svg class="w-5 h-5 inline-block mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            Update User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<script>
// Password confirmation validation
document.getElementById('password_confirmation').addEventListener('input', function() {
    const password = document.getElementById('password').value;
    const confirmPassword = this.value;
    
    if (password && confirmPassword && password !== confirmPassword) {
        this.setCustomValidity('Passwords do not match');
    } else {
        this.setCustomValidity('');
    }
});

document.getElementById('password').addEventListener('input', function() {
    const confirmPassword = document.getElementById('password_confirmation');
    if (confirmPassword.value) {
        confirmPassword.dispatchEvent(new Event('input'));
    }
});
</script>
</body>
</html>