<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\LeaveTypeController;
use App\Http\Controllers\LeaveApprovalController;

// Public routes
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Authentication required routes
Route::middleware(['auth'])->group(function () {
    // Dashboard routes
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/balance', [DashboardController::class, 'balance'])->name('balance');

    // Leave request routes
    Route::resource('leave-requests', LeaveRequestController::class);
    Route::post('leave-requests/{leaveRequest}/cancel', [LeaveRequestController::class, 'cancel'])->name('leave-requests.cancel');

    // Leave approval routes (for admins/managers)
    Route::middleware(['auth'])->group(function () {
        Route::get('/approvals', [LeaveApprovalController::class, 'index'])->name('leave-approvals.index');
        Route::get('/approvals/{leaveRequest}', [LeaveApprovalController::class, 'show'])->name('leave-approvals.show');
        Route::post('/approvals/{leaveRequest}/approve', [LeaveApprovalController::class, 'approve'])->name('leave-approvals.approve');
        Route::post('/approvals/{leaveRequest}/reject', [LeaveApprovalController::class, 'reject'])->name('leave-approvals.reject');
        Route::post('/approvals/bulk-approve', [LeaveApprovalController::class, 'bulkApprove'])->name('leave-approvals.bulk-approve');
        Route::post('/approvals/bulk-reject', [LeaveApprovalController::class, 'bulkReject'])->name('leave-approvals.bulk-reject');
    });

    // Leave type management routes (admin only)
    Route::middleware(['can:manage,App\Models\LeaveType'])->group(function () {
        Route::resource('leave-types', LeaveTypeController::class)->middleware('can:manage,App\Models\LeaveType');
        Route::patch('leave-types/{leaveType}/toggle-status', [LeaveTypeController::class, 'toggleStatus'])->name('leave-types.toggle-status');
    });

    // User management routes (admin only)
    Route::middleware(['auth'])->group(function () {
        Route::get('/users', [DashboardController::class, 'users'])->name('admin.users');
        Route::get('/users/create', [DashboardController::class, 'createUser'])->name('admin.users.create');
        Route::post('/users', [DashboardController::class, 'storeUser'])->name('admin.users.store');
        Route::get('/users/{user}/edit', [DashboardController::class, 'editUser'])->name('admin.users.edit');
        Route::put('/users/{user}', [DashboardController::class, 'updateUser'])->name('admin.users.update');
        Route::delete('/users/{user}', [DashboardController::class, 'deleteUser'])->name('admin.users.delete');
    });
});

// Include authentication routes
require __DIR__.'/auth.php';

// Temporary admin setup route (remove in production)
Route::get('/admin-setup', function () {
    // Create or update test user as admin
    $testUser = App\Models\User::firstOrCreate(
        ['email' => 'test@example.com'],
        [
            'name' => 'Test Admin',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]
    );

    // Also ensure the first user (ID: 1) is admin
    $firstUser = App\Models\User::find(1);
    if ($firstUser) {
        // Update first user to be admin if it's not the test user
        if ($firstUser->email !== 'test@example.com') {
            $firstUser->update(['email' => 'admin@example.com']);
        }
    }

    echo "Admin user setup completed:<br>";
    echo "Email: " . $testUser->email . "<br>";
    echo "Admin: " . ($testUser->isAdmin() ? 'YES' : 'NO') . "<br>";
    echo "Password: password<br><br>";
    echo '<a href="/">Go to Home</a> | <a href="/login">Go to Login</a>';
});

// Force admin mode for testing (remove in production)
Route::get('/force-admin', function () {
    if (auth()->check()) {
        // Temporarily override the isAdmin method for the current user
        $user = auth()->user();
        $user->isAdmin = function() { return true; };

        return redirect('/dashboard')->with('success', 'Admin mode activated for this session!');
    }

    return redirect('/login')->with('error', 'Please login first');
});

// Temporary admin check route (remove in production)
Route::get('/admin-check', function () {
    $users = App\Models\User::all();
    echo "Users in database:<br>";
    foreach($users as $user) {
        echo $user->email . ' - Admin: ' . ($user->isAdmin() ? 'YES' : 'NO') . '<br>';
    }
});
