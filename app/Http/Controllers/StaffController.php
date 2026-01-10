<?php

namespace App\Http\Controllers;

use App\Helpers\FileUpload;
use App\Models\Staff;
use App\Trait\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class StaffController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $staff = Staff::with('roles')->get();

        $totalStaff = $staff->count();
        $active = $staff->where('status', true)->count();
        $inactive = $staff->where('status', false)->count();

        $staffRoles = DB::table('model_has_roles')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->where('model_has_roles.model_type', Staff::class)
            ->select('roles.name', DB::raw('COUNT(model_has_roles.model_id) as total'))
            ->groupBy('roles.name')
            ->get();

        $data = [
            'staff' => $staff,
            'totalStaff' => $totalStaff,
            'active' => $active,
            'inactive' => $inactive,
            'staffRoles' => $staffRoles,
        ];

        return $this->successResponse($data, 'Staff fetched successfully');
    }

    public function staff(Request $request)
    {
        $request->validate([
            'fname' => 'required|string|max:255',
            'lname' => 'required|string|max:255',
            'email' => 'required|email|unique:staff,email',
            'phone' => 'required|string|max:255',
            'password' => 'required|string|min:6',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif,svg|max:2048',
        ]);

        $file = null;
        if ($request->hasFile('photo')) {
            $file = FileUpload::storeFile($request->file('photo'), 'uploads/staff');
        }

        $Staff = Staff::create([
            'fname' => $request->fname,
            'lname' => $request->lname,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'photo' => $file,
            'status' => true,
        ]);

        $Staff->syncRoles($request->roles);

        return $this->successResponse($Staff, 'Staff created successfully');
    }

    public function staffEdit($id)
    {
        $staff = Staff::with('roles')->find($id);
        $roles = Role::select('id', 'name')->get();

        if (! $staff) {
            return $this->errorResponse('Staff not found', 404);
        }

        $data = [
            'staff' => $staff,
            'roles' => $roles,
        ];

        return $this->successResponse($data, 'Staff found successfully');
    }

    public function staffUpdate(Request $request, $id)
    {
        $request->validate([
            'fname' => 'required|string|max:255',
            'lname' => 'required|string|max:255',
            'email' => 'required|email|unique:staff,email,'.$id,
            'phone' => 'required|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif,svg|max:2048',
        ]);

        $staff = Staff::find($id);

        if (! $staff) {
            return $this->errorResponse('Staff not found', 404);
        }

        $file = $staff->photo;
        if ($request->hasFile('photo')) {
            $file = FileUpload::updateFile($request->file('photo'), 'uploads/staff', $staff->photo);
        }

        $staff->update([
            'fname' => $request->fname,
            'lname' => $request->lname,
            'email' => $request->email,
            'phone' => $request->phone,
            'photo' => $file,
        ]);

        $staff->syncRoles($request->roles);

        return $this->successResponse($staff, 'Staff updated successfully');
    }

    public function staffDelete($id)
    {
        $staff = Staff::find($id);

        if (! $staff) {
            return $this->errorResponse('Staff not found', 404);
        }

        $staff->delete();

        return $this->successResponse($staff, 'Staff deleted successfully');
    }
}
