<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;

class SignupController extends Controller
{
    /**
     * Show the signup form
     */
    public function show()
    {
        return view('auth.signup');
    }

    /**
     * Handle signup form submission
     */
    public function store(Request $request)
    {
        // Log the request for debugging
        Log::info('Signup attempt', [
            'data' => $request->except(['password', 'password_confirmation']),
            'ip' => $request->ip()
        ]);

        // Validate the form data
        try {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'username' => ['required', 'string', 'max:255', 'unique:users', 'alpha_dash'],
                'password' => ['required', 'confirmed', Password::min(6)],
            ], [
                'name.required' => 'Full name is required.',
                'email.required' => 'Email address is required.',
                'email.email' => 'Please enter a valid email address.',
                'email.unique' => 'This email is already registered.',
                'username.required' => 'Username is required.',
                'username.unique' => 'This username is already taken.',
                'username.alpha_dash' => 'Username can only contain letters, numbers, dashes, and underscores.',
                'password.required' => 'Password is required.',
                'password.confirmed' => 'Password confirmation does not match.',
                'password.min' => 'Password must be at least 6 characters.',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Signup validation failed', ['errors' => $e->errors()]);
            throw $e;
        }

        try {
            // Create the user (non-admin by default)
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'username' => $validated['username'],
                'password' => Hash::make($validated['password']),
                'is_admin' => false, // Regular users are not admin by default
                'is_active' => true, // New users are active by default
            ]);

            Log::info('User created successfully', ['user_id' => $user->id, 'username' => $user->username]);

            // Log the user in
            Auth::login($user);

            Log::info('User logged in after signup', ['user_id' => $user->id]);

            // Check if email verification is required
            $requireVerification = SystemSetting::get('require_email_verification', false);

            if ($requireVerification) {
                // Send verification email
                $user->sendEmailVerificationNotification();

                Log::info('Email verification sent', ['user_id' => $user->id]);

                // Redirect to verification notice
                return redirect()->route('verification.notice')->with('message', 'Account created successfully! Please check your email to verify your account.');
            }

            // Redirect to dashboard with success message (no verification required)
            return redirect()->route('dashboard')->with('success', 'Account created successfully! Welcome to Budget Control System.');

        } catch (\Exception $e) {
            // Handle any errors during user creation
            Log::error('Signup error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->withErrors(['error' => 'An error occurred while creating your account. Please try again.'])->withInput();
        }
    }
}
