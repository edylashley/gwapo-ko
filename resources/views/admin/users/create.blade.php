<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create New User - Budget Control</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body style="background: #064e3b;" class="min-h-screen px-4 py-10" style="background: linear-gradient(135deg, #064e3b, #065f46, #10b981, #059669);">
    <!-- Navigation -->
    @include('components.navigation', ['pageTitle' => 'Create New User'])

    <!-- Main Content -->
    <div class="pt-20">
        <div class="max-w-4xl mx-auto">
            
            <!-- Header Section -->
            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 mb-8 overflow-hidden">
                <div class="bg-gradient-to-r from-emerald-600 via-green-600 to-emerald-700 px-8 py-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-3xl font-bold text-black">Create New User</h1>
                            <p class="text-emerald-100 mt-2">Add a new user to the system</p>
                        </div>
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

            <!-- Create User Form -->
            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                <form action="{{ route('users.store') }}" method="POST" class="p-8">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Full Name -->
                        <div class="md:col-span-2">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                Full Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name') }}"
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
                                   value="{{ old('email') }}"
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
                                   value="{{ old('username') }}"
                                   required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors duration-200"
                                   placeholder="Enter username">
                        </div>

                        <!-- Password -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                Password <span class="text-red-500">*</span>
                            </label>
                            <input type="password" 
                                   id="password" 
                                   name="password" 
                                   required
                                   minlength="6"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors duration-200"
                                   placeholder="Enter password (min. 6 characters)">
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                                Confirm Password <span class="text-red-500">*</span>
                            </label>
                            <input type="password" 
                                   id="password_confirmation" 
                                   name="password_confirmation" 
                                   required
                                   minlength="6"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors duration-200"
                                   placeholder="Confirm password">
                        </div>

                        <!-- Admin Privileges -->
                        <div class="md:col-span-2">
                            <div class="flex items-center">
                                <input type="checkbox" 
                                       id="is_admin" 
                                       name="is_admin" 
                                       value="1"
                                       {{ old('is_admin') ? 'checked' : '' }}
                                       class="w-4 h-4 text-emerald-600 bg-gray-100 border-gray-300 rounded focus:ring-emerald-500 focus:ring-2">
                                <label for="is_admin" class="ml-3 text-sm font-medium text-gray-700">
                                    Grant Administrator Privileges
                                </label>
                            </div>
                            <p class="mt-2 text-sm text-gray-500">
                                Administrators can manage users, projects, and system settings.
                            </p>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="mt-8 flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                        <a href="{{ route('users.index') }}" 
                           class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-400 transition-colors duration-200">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="px-6 py-3 bg-green-600 text-black font-semibold rounded-lg hover:from-emerald-700 hover:to-green-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                            <svg class="w-5 h-5 inline-block mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                            </svg>
                            Create User
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
    
    if (password !== confirmPassword) {
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
