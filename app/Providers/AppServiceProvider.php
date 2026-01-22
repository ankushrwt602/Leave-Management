<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register policies
        Gate::policy(LeaveRequest::class, \App\Policies\LeaveRequestPolicy::class);
        Gate::policy(User::class, \App\Policies\UserPolicy::class);
        Gate::policy(LeaveType::class, \App\Policies\LeaveTypePolicy::class);

        // Define custom gates for leave management
        Gate::define('approve-leave-requests', function (User $user) {
            return $user->canApproveLeaveRequests();
        });

        Gate::define('manage-leave-types', function (User $user) {
            return $user->isAdmin();
        });

        Gate::define('view-leave-analytics', function (User $user) {
            return $user->isAdmin();
        });

        Gate::define('manage-users', function (User $user) {
            return $user->isAdmin();
        });
    }
}
