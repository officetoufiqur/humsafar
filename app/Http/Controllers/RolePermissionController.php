<?php

namespace App\Http\Controllers;

use App\Trait\ApiResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionController extends Controller
{
    use ApiResponse;

    public function roles()
    {
        $roles = Role::select('id', 'name')->get();
        
        if (!$roles) {
            return $this->errorResponse('Roles not found', 404);
        }

        return $this->successResponse($roles, 'Roles fetched successfully');
    }

    public function roleStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'description' => 'nullable|string|max:500',
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id',
        ]);
        $role = Role::create([
            'name' => $request->name,
            'description' => $request->description,
            'guard_name' => 'web',
        ]);

       $role->syncPermissions($request->permissions);

        return $this->successResponse($role, 'Role created successfully');
    }

    public function permissions()
    {
        $permissions = Permission::select('id', 'name')->get();
        
        if (!$permissions) {
            return $this->errorResponse('Permissions not found', 404);
        }

        return $this->successResponse($permissions, 'Permissions fetched successfully');
    }
}
