<?php

namespace App\Providers;

use App\Models\Permission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
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
        // $this->registerPolicies();

        // Gate::define('view-organizational-outcome', function () {
        //     return Auth::user()->role->hasPermission('manage_organizational_outcome') || Auth::user()->role->name === 'SuperAdmin' ||  Auth::user()->role->name === 'Admin';
        // });

        // Gate::define('view-indicator', function () {
        //     return Auth::user()->role->hasPermission('manage_indicator') || Auth::user()->role->name === 'SuperAdmin' ||  Auth::user()->role->name === 'Admin';
        // });

        // Gate::define('view-entries', function () {
        //     return Auth::user()->role->hasPermission('manage_entries') || Auth::user()->role->name === 'SuperAdmin' ||  Auth::user()->role->name === 'Admin';
        // });

        // Gate::define('generate-reports', function () {
        //     return Auth::user()->role->hasPermission('generate_report') || Auth::user()->role->name === 'SuperAdmin' ||  Auth::user()->role->name === 'Admin';
        // });

        // Gate::define('manage-user-management', function () {
        //     return Auth::user()->role->hasPermission('manage_users') || Auth::user()->role->name === 'SuperAdmin' ||  Auth::user()->role->name === 'Admin';
        // });

        // Gate::define('manage-roles', function () {
        //     return Auth::user()->role->hasPermission('manage_roles') || Auth::user()->role->name === 'SuperAdmin' ||  Auth::user()->role->name === 'Admin';
        // });

        // Gate::define('manage-users', function () {
        //     return Auth::user()->role->hasPermission('manage_users') || Auth::user()->role->name === 'SuperAdmin' ||  Auth::user()->role->name === 'Admin';
        // });

        // Gate::define('view-history', function () {
        //     return Auth::user()->role->hasPermission('manage_history') || Auth::user()->role->name === 'SuperAdmin' ||  Auth::user()->role->name === 'Admin';
        // });

        // Gate::define('view-permissions', function () {
        //     return Auth::user()->role->hasPermission('manage_permissions') || Auth::user()->role->name === 'SuperAdmin' ||  Auth::user()->role->name === 'Admin';
        // });
        // Gate::define('manage_pending_entries', function () {
        //     return Auth::user()->role->hasPermission('manage_pending_entries') ||  Auth::user()->role->name === 'Admin';
        // });
        // Gate::define('filter_dashboard', function () {
        //     return Auth::user()->role->hasPermission('filter_dashboard') || Auth::user()->role->name === 'SuperAdmin'  ||  Auth::user()->role->name === 'Admin';
        // });

        // Get all permissions from the database and create gates dynamically
        if (Schema::hasTable('permissions')) {
            Permission::all()->each(function ($permission) {
                Gate::define($permission->name, function ($user) use ($permission) {
                    return Auth::user()->role->hasPermission($permission->name) || Auth::user()->role->name === 'SuperAdmin' ||  Auth::user()->role->name === 'Admin';
                });
            });
        }
    }
}
