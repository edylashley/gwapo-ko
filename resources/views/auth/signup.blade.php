<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Budget Control System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@700&display=swap" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #064e3b, #065f46, #10b981, #059669);
            min-height: 100vh;
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

        .input-field {
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(255, 255, 255, 0.3);
            transition: all 0.3s ease;
        }

        .input-field:focus {
            background: rgba(255, 255, 255, 1);
            border-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
            outline: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, #059669, #10b981);
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(16, 185, 129, 0.3);
            background: linear-gradient(135deg, #047857, #059669);
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

        .card-delay-1 { animation-delay: 0.1s; }
        .card-delay-2 { animation-delay: 0.3s; }
        .card-delay-3 { animation-delay: 0.5s; }
    </style>
</head>
<body style="background: #064e3b;" class="flex items-center justify-center min-h-screen p-4">
    <div class="w-full max-w-md">
        <!-- Logo/Header -->
        <div class="text-center mb-8">
            <div class="flex justify-center mb-4">
                @include('components.logo', ['size' => 'xl', 'background' => true, 'class' => 'shadow-xl'])
            </div>
            <h1 class="text-4xl font-bold text-white mb-2">Budget Control</h1>
            <p class="text-white text-opacity-80">Create your account</p>
        </div>

        <!-- Signup Form -->
        <div class="glass-card rounded-2xl shadow-2xl p-8">
            <form method="POST" action="{{ route('signup.store') }}" class="space-y-6">
                @csrf
                
                <!-- Full Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-white mb-2">Full Name</label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           value="{{ old('name') }}"
                           required 
                           class="input-field w-full px-4 py-3 rounded-lg focus:outline-none focus:ring-0"
                           placeholder="Enter your full name">
                    @error('name')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-white mb-2">Email Address</label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           value="{{ old('email') }}"
                           required 
                           class="input-field w-full px-4 py-3 rounded-lg focus:outline-none focus:ring-0"
                           placeholder="Enter your email">
                    @error('email')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Username -->
                <div>
                    <label for="username" class="block text-sm font-medium text-white  mb-2">Username</label>
                    <input type="text" 
                           id="username" 
                           name="username" 
                           value="{{ old('username') }}"
                           required 
                           class="input-field w-full px-4 py-3 rounded-lg focus:outline-none focus:ring-0"
                           placeholder="Choose a username">
                    @error('username')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-white  mb-2">Password</label>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           required 
                           class="input-field w-full px-4 py-3 rounded-lg focus:outline-none focus:ring-0"
                           placeholder="Create a password">
                    @error('password')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-white  mb-2">Confirm Password</label>
                    <input type="password" 
                           id="password_confirmation" 
                           name="password_confirmation" 
                           required 
                           class="input-field w-full px-4 py-3 rounded-lg focus:outline-none focus:ring-0"
                           placeholder="Confirm your password">
                </div>

                <!-- Submit Button -->
                <button type="submit"
                        id="signupBtn"
                        class="btn-primary w-full py-3 px-4 rounded-lg text-white font-semibold text-lg">
                    <span id="signupBtnText">Create Account</span>
                    <div id="signupSpinner" class="hidden inline-block ml-2">
                        <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"></div>
                    </div>
                </button>

                <!-- Login Link -->
                <div class="text-center pt-4">
                    <p class="text-white text-opacity-80">
                        Already have an account?
                        <a href="{{ route('login') }}" class="text-white hover:text-green-100 font-medium transition-colors duration-200">
                            Sign in here
                        </a>
                    </p>
                </div>
            </form>
        </div>

        <!-- Footer -->
        <div class="text-center mt-8 card-delay-3">
            <p class="text-green-100 text-opacity-60 text-sm">
                © {{ date('Y') }} Budget Control System. All rights reserved.
            </p>
        </div>
    </div>

    <script>
        // Add form submission debugging
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const signupBtn = document.getElementById('signupBtn');
            const signupBtnText = document.getElementById('signupBtnText');
            const signupSpinner = document.getElementById('signupSpinner');

            form.addEventListener('submit', function(e) {
                console.log('Form submitted');

                // Show loading state
                signupBtnText.textContent = 'Creating Account...';
                signupSpinner.classList.remove('hidden');
                signupBtn.disabled = true;

                // Let the form submit normally
                // Don't prevent default - let Laravel handle it
            });
        });
    </script>
</body>
</html>
