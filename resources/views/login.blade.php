<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Budget Control Login</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #064e3b, #065f46, #10b981, #059669);
            min-height: 100vh;
        }
        .glass {
            background: rgba(255, 255, 255, 0.15);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.18);
        }
        .techy-input:focus {
            border-color: #10b981;
            box-shadow: 0 0 0 2px #10b98155;
            transition: box-shadow 0.3s, border-color 0.3s;
        }
        .techy-btn {
            background: linear-gradient(90deg, #10b981, #059669);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .techy-btn:hover {
            transform: translateY(-2px) scale(1.03);
            box-shadow: 0 4px 20px #10b98155;
        }
        .logo-anim {
            animation: float 2.5s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0);}
            50% { transform: translateY(-10px);}
        }
        .fade-in-up {
            opacity: 0;
            transform: translateY(40px);
            animation: fadeInUp 2s cubic-bezier(0.23, 1, 0.32, 1) 0.1s forwards;
        }
        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: none;
            }
        }
    </style>
</head>
<body style="background: #064e3b;" class="flex items-center justify-center min-h-screen">
    <div class="glass w-full max-w-md p-8 mx-auto mt-10 fade-in-up">
        <div class="flex justify-center mb-6">
            <div class="logo-anim">
                @include('components.logo', ['size' => 'xl', 'background' => true, 'class' => 'shadow-xl'])
            </div>
        </div>
        <h1 class="text-3xl font-bold text-center text-white mb-2" style="font-family: 'Orbitron', sans-serif;">Budget Control</h1>
        <p class="text-center text-green-100 mb-6">Login</p>
        @if(session('error'))
            <div class="alert alert-danger text-red-700 bg-red-100 p-2 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        @if(
            $errors->any())
            <div class="alert alert-danger text-red-700 bg-red-100 p-2 rounded mb-4">
                @foreach(
                    $errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif
        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-4">
                <label class="block text-green-100 mb-1" for="login">Username or Email</label>
                <input
                    id="login"
                    type="text"
                    name="login"
                    class="techy-input w-full px-4 py-3 rounded-lg bg-white bg-opacity-100 border border-green-200 focus:outline-none"
                    placeholder="Enter your username or email"
                    required
                    autocomplete="username"
                >
            </div>
            <div class="mb-4">
                <label class="block text-green-100 mb-1" for="password">Password</label>
                <input
                    id="password"
                    type="password"
                    name="password"
                    class="techy-input w-full px-4 py-3 rounded-lg bg-white bg-opacity-100 border border-green-200 focus:outline-none"
                    placeholder="Enter your password"
                    required
                    autocomplete="current-password"
                >
            </div>
            <div class="flex items-center mb-6">
                <input type="checkbox" name="remember" id="remember" class="mr-2">
                <label for="remember" class="text-green-100">Remember Me</label>
            </div>
            <button id="login-btn" type="submit" class="techy-btn w-full py-3 text-white font-bold rounded-lg shadow-lg text-lg flex items-center justify-center">
                <svg id="spinner" class="animate-spin h-5 w-5 mr-2 hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                </svg>
                <span id="login-btn-text">Login</span>
            </button>
        </form>
        <div class="text-center mt-4 space-y-2">
            <div>
                <a href="{{ route('password.request') }}" class="text-green-200 hover:underline">Forgot Password?</a>
            </div>
            <div class="border-t border-green-300 border-opacity-30 pt-4 mt-4">
                <p class="text-green-100 mb-2">Don't have an account?</p>
                <a href="{{ route('signup') }}" class="inline-block bg-white bg-opacity-20 hover:bg-opacity-30 text-white font-semibold py-2 px-6 rounded-lg transition-all duration-300 border border-white border-opacity-30">
                    Create Account
                </a>
            </div>
        </div>
        <script>
            document.querySelector('form').addEventListener('submit', function() {
                document.getElementById('spinner').classList.remove('hidden');
                document.getElementById('login-btn-text').textContent = 'Logging in...';
                document.getElementById('login-btn').setAttribute('disabled', 'disabled');
            });
        </script>
    </div>
</body>
</html>
