<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

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
        // Define the 'edit tasks' permission
        Gate::define('edit tasks', function ($user) {
            return $user->hasRole(['admin', 'project-manager']);
        });

        // Define additional permissions
        Gate::define('manage users', function ($user) {
            return $user->hasRole('admin');
        });

        Gate::define('manage projects', function ($user) {
            return $user->hasRole(['admin', 'project-manager']);
        });

        Gate::define('view tasks', function ($user) {
            return $user->hasAnyRole(['admin', 'project-manager', 'member']);
        });

        Gate::define('delete tasks', function ($user) {
            return $user->hasRole(['admin', 'project-manager']);
        });
    }
}
