<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Trait\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $totalRoles = Role::count();

        $staffAssigned = DB::table('model_has_roles')
            ->where('model_type', Staff::class)
            ->distinct('model_id')
            ->count('model_id');

        $administrativeRoles = Role::where('name', 'like', '%admin%')->count();

        $permissionSets = Permission::count();

        $roles = DB::table('roles')
            ->leftJoin('model_has_roles', function ($join) {
                $join->on('roles.id', '=', 'model_has_roles.role_id')
                    ->where('model_has_roles.model_type', Staff::class);
            })
            ->select(
                'roles.id',
                'roles.name',
                DB::raw('COUNT(model_has_roles.model_id) as staffCount')
            )
            ->groupBy('roles.id', 'roles.name')
            ->get();

        return response()->json([
            'stats' => [
                'totalRoles' => $totalRoles,
                'staffAssigned' => $staffAssigned,
                'administrativeRoles' => $administrativeRoles,
                'permissionSets' => $permissionSets,
            ],
            'roles' => $roles->map(fn ($r) => [
                'id' => $r->id,
                'name' => $r->name,
                'category' => str_contains(strtolower($r->name), 'admin') ? 'Administrative' : 'Custom',
                'staffCount' => $r->staffCount,
                'status' => 'active',
            ]),
        ]);

        return $this->successResponse($data, 'Roles fetched successfully');
    }

    public function roles()
    {
        $roles = Role::select('id', 'name')->get();

        if (! $roles) {
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

        if (! $permissions) {
            return $this->errorResponse('Permissions not found', 404);
        }

        return $this->successResponse($permissions, 'Permissions fetched successfully');
    }

    public function roleEdit($id)
    {
        $role = Role::with('permissions:id,name')->find($id);

        if (! $role) {
            return $this->errorResponse('Role not found', 404);
        }

        $permissions = Permission::select('id', 'name')->get();

        $data = [
            'role' => $role,
            'permissions' => $permissions,
        ];

        return $this->successResponse($data, 'Role fetched successfully');
    }

    public function roleUpdate(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,'.$id,
            'description' => 'nullable|string|max:500',
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = Role::find($id);

        if (! $role) {
            return $this->errorResponse('Role not found', 404);
        }

        $role->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        $role->syncPermissions($request->permissions);

        return $this->successResponse($role, 'Role updated successfully');
    }

     public function roleView($id)
    {
        $role = Role::with('permissions:id,name')->find($id);

        if (! $role) {
            return $this->errorResponse('Role not found', 404);
        }

        $permissions = Permission::select('id', 'name')->get();

        $modelRoles = DB::table('model_has_roles')
            ->where('role_id', $id)
            ->select('model_id')
            ->count();

        $data = [
            'role' => $role,
            'roleCount' => $modelRoles,
            'permissions' => $permissions,
        ];

        return $this->successResponse($data, 'Role fetched successfully');
    }

    public function roleDelete($id)
    {
        $role = Role::find($id);

        if (! $role) {
            return $this->errorResponse('Role not found', 404);
        }

        $role->delete();

        return $this->successResponse(null, 'Role deleted successfully');
    }
}
