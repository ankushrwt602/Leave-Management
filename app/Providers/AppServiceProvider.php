<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\LeaveRequest;
use App\Models\LeaveType;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register Socialite (safe even if auto-discovery works)
        if (class_exists(\Laravel\Socialite\SocialiteServiceProvider::class)) {
            $this->app->register(\Laravel\Socialite\SocialiteServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Force HTTPS in production (Railway fix)
        |--------------------------------------------------------------------------
        */
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        /*
        |--------------------------------------------------------------------------
        | Register Socialite alias (only if missing)
        |--------------------------------------------------------------------------
        */
        if (!class_exists('Socialite')) {
            class_alias(
                \Laravel\Socialite\Facades\Socialite::class,
                'Socialite'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Register Policies
        |--------------------------------------------------------------------------
        */
        Gate::policy(LeaveRequest::class, \App\Policies\LeaveRequestPolicy::class);
        Gate::policy(User::class, \App\Policies\UserPolicy::class);
        Gate::policy(LeaveType::class, \App\Policies\LeaveTypePolicy::class);

        /*
        |--------------------------------------------------------------------------
        | Custom Gates
        |--------------------------------------------------------------------------
        */
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
