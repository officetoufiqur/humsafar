<?php

namespace App\Http\Controllers;

use App\Trait\ApiResponse;
use Illuminate\Http\Request;
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
}
