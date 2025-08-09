<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of users (Admin only)
     */
    public function index()
    {
        // Check if user is admin
        if (!auth()->user()->is_admin) {
            abort(403, 'Unauthorized access');
        }

        $users = User::orderBy('created_at', 'desc')->paginate(10);
        
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user (Admin only)
     */
    public function create()
    {
        if (!auth()->user()->is_admin) {
            abort(403, 'Unauthorized access');
        }

        return view('admin.users.create');
    }

    /**
     * Store a newly created user (Admin only)
     */
    public function store(Request $request)
    {
        if (!auth()->user()->is_admin) {
            abort(403, 'Unauthorized access');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'username' => 'required|string|max:255|unique:users,username',
            'password' => 'required|string|min:6|confirmed',
            'is_admin' => 'boolean',
        ]);

        try {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'username' => $validated['username'],
                'password' => Hash::make($validated['password']),
                'is_admin' => $request->has('is_admin'),
                'is_active' => $request->has('is_active') ? true : true, // Default to active for new users
            ]);

            Log::info('User created by admin', [
                'created_user_id' => $user->id,
                'created_by' => auth()->id(),
                'username' => $user->username
            ]);

            return redirect()->route('users.index')
                ->with('success', "User '{$user->name}' has been created successfully.");

        } catch (\Exception $e) {
            Log::error('Failed to create user', [
                'error' => $e->getMessage(),
                'created_by' => auth()->id()
            ]);

            return back()->withInput()
                ->with('error', 'Failed to create user. Please try again.');
        }
    }

    /**
     * Display the specified user (Admin only)
     */
    public function show(User $user)
    {
        if (!auth()->user()->is_admin) {
            abort(403, 'Unauthorized access');
        }

        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user (Admin only)
     */
    public function edit(User $user)
    {
        if (!auth()->user()->is_admin) {
            abort(403, 'Unauthorized access');
        }

        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified user (Admin only)
     */
    public function update(Request $request, User $user)
    {
        if (!auth()->user()->is_admin) {
            abort(403, 'Unauthorized access');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:6|confirmed',
            'is_admin' => 'boolean',
        ]);

        try {
            $updateData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'username' => $validated['username'],
                'is_admin' => $request->has('is_admin'),
                'is_active' => $request->has('is_active') ? true : ($user->is_active ?? true), // Preserve existing status if not specified
            ];

            // Only update password if provided
            if (!empty($validated['password'])) {
                $updateData['password'] = Hash::make($validated['password']);
            }

            $user->update($updateData);

            Log::info('User updated by admin', [
                'updated_user_id' => $user->id,
                'updated_by' => auth()->id(),
                'changes' => $updateData
            ]);

            return redirect()->route('users.index')
                ->with('success', "User '{$user->name}' has been updated successfully.");

        } catch (\Exception $e) {
            Log::error('Failed to update user', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'updated_by' => auth()->id()
            ]);

            return back()->withInput()
                ->with('error', 'Failed to update user. Please try again.');
        }
    }

    /**
     * Remove the specified user (Admin only)
     */
    public function destroy(User $user)
    {
        if (!auth()->user()->is_admin) {
            abort(403, 'Unauthorized access');
        }

        // Prevent admin from deleting themselves
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        try {
            $userName = $user->name;
            $user->delete();

            Log::info('User deleted by admin', [
                'deleted_user_id' => $user->id,
                'deleted_user_name' => $userName,
                'deleted_by' => auth()->id()
            ]);

            return redirect()->route('users.index')
                ->with('success', "User '{$userName}' has been deleted successfully.");

        } catch (\Exception $e) {
            Log::error('Failed to delete user', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'deleted_by' => auth()->id()
            ]);

            return back()->with('error', 'Failed to delete user. Please try again.');
        }
    }

    /**
     * Toggle user admin status (Admin only)
     */
    public function toggleAdmin(User $user)
    {
        if (!auth()->user()->is_admin) {
            abort(403, 'Unauthorized access');
        }

        // Prevent admin from removing their own admin status
        if ($user->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot modify your own admin status.'
            ], 400);
        }

        try {
            $user->is_admin = !$user->is_admin;
            $user->save();

            $status = $user->is_admin ? 'granted' : 'revoked';

            Log::info('User admin status toggled', [
                'user_id' => $user->id,
                'new_status' => $user->is_admin,
                'changed_by' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => "Admin privileges {$status} for {$user->name}.",
                'is_admin' => $user->is_admin
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to toggle admin status', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'changed_by' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update admin status.'
            ], 500);
        }
    }

    /**
     * Toggle user active status (Admin only)
     */
    public function toggleStatus(User $user)
    {
        if (!auth()->user()->is_admin) {
            abort(403, 'Unauthorized access');
        }

        // Prevent admin from deactivating their own account
        if ($user->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot modify your own account status.'
            ], 400);
        }

        try {
            $user->is_active = !($user->is_active ?? true);
            $user->save();

            $status = $user->is_active ? 'activated' : 'deactivated';

            Log::info('User status toggled', [
                'user_id' => $user->id,
                'new_status' => $user->is_active,
                'changed_by' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => "User {$status} successfully.",
                'is_active' => $user->is_active
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to toggle user status', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'changed_by' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update user status. Please try again.'
            ], 500);
        }
    }
}
