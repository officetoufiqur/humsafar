<?php

namespace App\Http\Controllers;

use App\Helpers\FileUpload;
use App\Models\Staff;
use App\Trait\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StaffController extends Controller
{
    use ApiResponse;

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

        return $this->successResponse($Staff, 'Staff created successfully');
    }
}
