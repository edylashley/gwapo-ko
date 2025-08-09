<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password</title>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .techy-btn {
            background: linear-gradient(90deg, #10b981, #059669);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .techy-btn:hover {
            transform: translateY(-2px) scale(1.03);
            box-shadow: 0 4px 20px #10b98155;
        }
        .techy-input:focus {
            border-color: #10b981;
            box-shadow: 0 0 0 2px #10b98155;
            transition: box-shadow 0.3s, border-color 0.3s;
        }
        .glass {
            background: rgba(255, 255, 255, 0.15);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.18);
        }
    </style>
</head>
<body style="background: #064e3b;" class="flex items-center justify-center min-h-screen bg-gradient-to-br from-green-900 to-green-400">
    <div class="glass w-full max-w-md p-8 mx-auto mt-10">
        <h1 class="text-3xl font-bold text-center text-white mb-2" style="font-family: 'Orbitron', sans-serif;">Forgot Password</h1>
        <p class="text-center text-green-100 mb-6">Enter your email to receive a password reset link.</p>
        @if (session('status'))
            <div class="mb-4 font-medium text-white">
                {{ session('status') }}
            </div>
        @endif
        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <div class="mb-4">
                <label class="block text-green-100 mb-1" for="email">Email</label>
                <input id="email" type="email" name="email" class="techy-input w-full px-4 py-3 rounded-lg bg-white bg-opacity-70 border border-green-200 focus:outline-none" required autofocus>
                @error('email')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>
            <button type="submit" class="techy-btn w-full py-3 text-white font-bold rounded-lg shadow-lg text-lg">Send Reset Link</button>
        </form>
        <div class="text-center mt-4">
            <a href="{{ route('login') }}" class="text-green-200 hover:underline">Back to Login</a>
        </div>
    </div>
</body>
</html> 