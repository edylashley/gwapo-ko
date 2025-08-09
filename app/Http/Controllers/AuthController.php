<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use App\Models\User;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        $login = $request->input('login');
        $password = $request->input('password');
        $remember = $request->has('remember');

        // Find user by email or username
        $user = User::where('email', $login)
            ->orWhere('username', $login)
            ->first();

        if (!$user) {
            return back()->with('error', 'No user found for this login: ' . $login);
        }

        if (!Hash::check($password, $user->password)) {
            return back()->with('error', 'Password does not match for: ' . $login);
        }

        // Check if user account is active
        if (!($user->is_active ?? true)) {
            return back()->with('error', 'Your account has been deactivated. Please contact an administrator.');
        }

        Auth::login($user, $remember);
        $request->session()->regenerate();
        return redirect()->intended('/dashboard');
    }

    public function logout(Request $request)
    {
        // Clear remember token from database
        $user = Auth::user();
        if ($user) {
            $user->remember_token = null;
            $user->save();
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Clear all cookies
        $response = redirect('/login');

        // Clear session cookie
        $sessionCookie = config('session.cookie');
        $response->withCookie(cookie()->forget($sessionCookie));

        // Clear remember cookie if it exists
        $rememberCookie = Auth::getRecallerName();
        $response->withCookie(cookie()->forget($rememberCookie));

        return $response;
    }

    public function showForgotPasswordForm()
    {
        return view('forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with(['status' => __($status)])
            : back()->withErrors(['email' => __($status)]);
    }

    public function showResetForm($token)
    {
        $email = request('email');
        return view('reset-password', ['token' => $token, 'email' => $email]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);

        $user = Auth::user();

        // Check if current password is correct
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect.'
            ], 400);
        }

        // Update password
        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully!'
        ]);
    }
}
