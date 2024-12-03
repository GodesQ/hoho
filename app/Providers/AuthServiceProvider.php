<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;  // Import Schema facade

use App\Models\Permission;
use App\Models\Admin;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication/authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // Check if the 'permissions' table exists before accessing it
        if (Schema::hasTable('permissions')) {
            $permissions = Permission::all();
            foreach ($permissions as $permission) {
                Gate::define($permission->permission_name, function (Admin $admin) use ($permission) {
                    $permission_roles = json_decode($permission->roles);
                    return in_array($admin->role, $permission_roles);
                });
            }
        }
    }
}
