<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Permission;

class PermissionAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {   
        $auth_user = Auth::guard('admin')->user();
        $permissions = Permission::all();

        foreach ($permissions as $key => $permission) {
            $permission_roles = json_decode($permission->roles);
            if(in_array($auth_user->role, $permission_roles)) {
                return $next($request);
            }
        }

        abort(403);
    }
}
