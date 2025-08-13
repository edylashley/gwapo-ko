
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\MonthlyAssignmentController;
use App\Http\Controllers\EngineerController;
use App\Http\Controllers\SignupController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SystemSettingsController;


Route::redirect('/', '/login');

// Authentication routes
Route::get('/signup', [SignupController::class, 'show'])->name('signup')->middleware(['guest', 'registration.enabled']);
Route::post('/signup', [SignupController::class, 'store'])->name('signup.store')->middleware(['guest', 'registration.enabled']);

Route::get('/dashboard', function () {
    return view('project-management-dashboard');
})->middleware('auth')->name('dashboard');

// API routes for dashboard
Route::get('/api/engineers', [EngineerController::class, 'getEngineersForDashboard'])->middleware('auth');

Route::get('/login', function () {
    if (Auth::check()) {
        return redirect('/dashboard');
    }
    return view('login');
})->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Change password route
Route::post('/change-password', [AuthController::class, 'changePassword'])->middleware('auth')->name('change-password');

// Password reset routes
Route::get('password/forgot', [App\Http\Controllers\AuthController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('password/email', [App\Http\Controllers\AuthController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [App\Http\Controllers\AuthController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [App\Http\Controllers\AuthController::class, 'resetPassword'])->name('password.update');

// Email Verification Routes
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (\Illuminate\Foundation\Auth\EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/dashboard')->with('success', 'Email verified successfully!');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (\Illuminate\Http\Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

Route::get('/one-click-login', [App\Http\Controllers\AuthController::class, 'oneClickLogin'])->name('one-click-login');

// Debug route to test expense creation
Route::get('/test-expense', function() {
    $user = \App\Models\User::first();
    \Illuminate\Support\Facades\Auth::login($user);

    $project = \App\Models\Project::first();

    $expense = \App\Models\Expense::create([
        'project_id' => $project->id,
        'description' => 'Test expense',
        'amount' => 100.00,
        'date' => now()->format('Y-m-d'),
        'status' => 'approved',
        'approved_at' => now(),
        'approved_by' => $user->id,
    ]);

    return response()->json([
        'success' => true,
        'expense' => $expense,
        'user' => $user,
        'project' => $project
    ]);
})->name('test-expense');

// Debug route to test CSRF and form submission
Route::get('/test-form', function() {
    return view('test-form');
})->name('test-form');

Route::post('/test-form', function(\Illuminate\Http\Request $request) {
    return response()->json([
        'success' => true,
        'data' => $request->all(),
        'csrf_token' => $request->header('X-CSRF-TOKEN'),
        'user' => \Illuminate\Support\Facades\Auth::user()
    ]);
})->middleware('auth')->name('test-form.post');



// Expense routes - View for all users, modify for admins only
Route::get('/expenses', [ExpenseController::class, 'index'])->middleware('auth')->name('expenses.index');
Route::post('/expenses', [ExpenseController::class, 'store'])->middleware(['auth', 'admin'])->name('expenses.store');
Route::put('/expenses/{expense}', [ExpenseController::class, 'update'])->middleware(['auth', 'admin'])->name('expenses.update');
Route::delete('/expenses/{expense}', [ExpenseController::class, 'destroy'])->middleware(['auth', 'admin'])->name('expenses.destroy');

// Dashboard API routes
Route::get('/api/dashboard/statistics', [App\Http\Controllers\Api\DashboardController::class, 'statistics'])->middleware('auth');
Route::get('/api/dashboard/budget-summary', [App\Http\Controllers\Api\DashboardController::class, 'budgetSummary'])->middleware('auth');
Route::get('/api/dashboard/budget-alerts', [App\Http\Controllers\Api\DashboardController::class, 'budgetAlerts'])->middleware('auth');

// Project Engineers API routes - View for all users, modify for admins only
Route::get('/api/project-engineers', [App\Http\Controllers\ProjectEngineerController::class, 'index'])->middleware('auth');
Route::post('/api/project-engineers', [App\Http\Controllers\ProjectEngineerController::class, 'store'])->middleware(['auth', 'admin']);
Route::get('/api/project-engineers/{engineer}', [App\Http\Controllers\ProjectEngineerController::class, 'show'])->middleware('auth');
Route::put('/api/project-engineers/{engineer}', [App\Http\Controllers\ProjectEngineerController::class, 'update'])->middleware(['auth', 'admin']);
Route::delete('/api/project-engineers/{engineer}', [App\Http\Controllers\ProjectEngineerController::class, 'destroy'])->middleware(['auth', 'admin']);
Route::get('/api/projects', [ProjectController::class, 'apiIndex'])->middleware('auth');

// Project management routes - View/Print for all users, modify for admins only
Route::get('/projects', [ProjectController::class, 'index'])->middleware('auth')->name('projects.index');
Route::post('/projects', [ProjectController::class, 'store'])->middleware(['auth', 'admin'])->name('projects.store');
Route::put('/projects/{project}', [ProjectController::class, 'update'])->middleware(['auth', 'admin'])->name('projects.update');
Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])->middleware(['auth', 'admin'])->name('projects.destroy');
Route::get('/projects/recently-deleted', [ProjectController::class, 'recentlyDeleted'])->middleware(['auth', 'admin'])->name('projects.recently-deleted');
Route::post('/projects/{id}/restore', [ProjectController::class, 'restore'])->middleware(['auth', 'admin'])->name('projects.restore');
Route::delete('/projects/{id}/force-delete', [ProjectController::class, 'forceDelete'])->middleware(['auth', 'admin'])->name('projects.force-delete');
Route::get('/projects/{project}/track-record', [ProjectController::class, 'trackRecord'])->middleware('auth')->name('projects.track-record');
Route::get('/projects/{project}/monthly-assignments', [ProjectController::class, 'getMonthlyAssignments'])->middleware('auth')->name('projects.monthly-assignments');
Route::get('/projects/batch', [ProjectController::class, 'getBatch'])->middleware('auth')->name('projects.batch');
Route::get('/projects/{project}/receipt', [ProjectController::class, 'receipt'])->middleware('auth')->name('projects.receipt');
Route::post('/projects/multiple-receipts', [ProjectController::class, 'multipleReceipts'])->middleware('auth')->name('projects.multiple-receipts');

// Project expense routes
Route::get('/projects/{project}/expenses/{expense}/team-members', [ProjectController::class, 'getExpenseTeamMembers'])->middleware('auth')->name('projects.expenses.team-members');
Route::put('/projects/{project}/expenses/{expense}', [ExpenseController::class, 'update'])->middleware(['auth', 'admin'])->name('projects.expenses.update');
Route::delete('/projects/{project}/expenses/{expense}/team-members/{engineer}', [ProjectController::class, 'removeExpenseTeamMember'])->middleware(['auth', 'admin'])->name('projects.expenses.team-members.remove');

Route::get('/projects/archive', [ProjectController::class, 'archivePage'])->middleware('auth');
// Only admins can archive, and use POST for mutating
Route::post('/projects/{project}/archive', [ProjectController::class, 'archiveProject'])->middleware(['auth', 'admin'])->name('projects.archive');
// routes/web.php
Route::post('/projects/{id}/unarchive', [ProjectController::class, 'unarchive'])->middleware('auth');


// Monthly Assignment Routes - View for all users, modify for admins only
Route::get('/monthly-assignments', [MonthlyAssignmentController::class, 'index'])->middleware('auth')->name('monthly-assignments.index');
Route::post('/monthly-assignments/assign', [MonthlyAssignmentController::class, 'assign'])->middleware(['auth', 'admin'])->name('monthly-assignments.assign');
Route::post('/monthly-assignments/set-team-head', [MonthlyAssignmentController::class, 'setTeamHead'])->middleware(['auth', 'admin'])->name('monthly-assignments.set-team-head');
Route::post('/monthly-assignments/update-salary', [MonthlyAssignmentController::class, 'updateSalary'])->middleware(['auth', 'admin'])->name('monthly-assignments.update-salary');
Route::post('/monthly-assignments/create-salary', [MonthlyAssignmentController::class, 'createSalaryAssignment'])->middleware(['auth', 'admin'])->name('monthly-assignments.create-salary');
Route::post('/monthly-assignments/update-salary', [MonthlyAssignmentController::class, 'updateSalaryAssignment'])->middleware(['auth', 'admin'])->name('monthly-assignments.update-salary');
Route::delete('/monthly-assignments/remove-engineer', [MonthlyAssignmentController::class, 'removeEngineer'])->middleware(['auth', 'admin'])->name('monthly-assignments.remove-engineer');
Route::delete('/monthly-assignments/remove', [MonthlyAssignmentController::class, 'remove'])->middleware(['auth', 'admin'])->name('monthly-assignments.remove');

// Engineer Routes - View for all users, modify for admins only
Route::get('/api/engineers', [EngineerController::class, 'index'])->middleware('auth')->name('engineers.index');
Route::post('/api/engineers', [EngineerController::class, 'store'])->middleware(['auth', 'admin'])->name('engineers.store');

// User Management Routes - Admin only
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::resource('users', UserController::class)->names([
        'index' => 'users.index',
        'create' => 'users.create',
        'store' => 'users.store',
        'show' => 'users.show',
        'edit' => 'users.edit',
        'update' => 'users.update',
        'destroy' => 'users.destroy',
    ]);
    Route::post('users/{user}/toggle-admin', [UserController::class, 'toggleAdmin'])->name('users.toggle-admin');
    Route::post('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');

    // System Settings Routes (Admin only)
    Route::get('system-settings', [SystemSettingsController::class, 'index'])->name('system-settings.index');
    Route::post('system-settings', [SystemSettingsController::class, 'update'])->name('system-settings.update');
    Route::post('system-settings/clear-cache', [SystemSettingsController::class, 'clearCache'])->name('system-settings.clear-cache');
});
// Salaries API for detailed engineering team (all authenticated users can view/edit own projects)
Route::get('/projects/{project}/team-salaries', [ProjectController::class, 'getTeamSalaries'])->middleware('auth');
Route::patch('/projects/{project}/team-salaries/{engineer}', [ProjectController::class, 'updateTeamSalary'])->middleware('auth');
Route::delete('/projects/{project}/team-salaries/{engineer}', [ProjectController::class, 'deleteTeamSalary'])->middleware('auth');

// User Management Routes - Admin only
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::resource('users', UserController::class)->names([
        'index' => 'users.index',
        'create' => 'users.create',
        'store' => 'users.store',
        'show' => 'users.show',
        'edit' => 'users.edit',
        'update' => 'users.update',
        'destroy' => 'users.destroy',
    ]);
    Route::post('users/{user}/toggle-admin', [UserController::class, 'toggleAdmin'])->name('users.toggle-admin');
    Route::post('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');

    // System Settings Routes (Admin only)
    Route::get('system-settings', [SystemSettingsController::class, 'index'])->name('system-settings.index');
    Route::post('system-settings', [SystemSettingsController::class, 'update'])->name('system-settings.update');
    Route::post('system-settings/clear-cache', [SystemSettingsController::class, 'clearCache'])->name('system-settings.clear-cache');
});