<?php

namespace App\Http\Controllers;

use App\Helpers\FileUpload;
use App\Models\Package;
use App\Trait\ApiResponse;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    use ApiResponse;

    public function packages(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'validity' => 'required|integer',
            'description' => 'required|string',
            'features' => 'required|array',
            'features*' => 'required|array',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif,svg|max:2048',
        ]);

        $file = null;
        if ($request->hasFile('image')) {
            $file = FileUpload::storeFile($request->file('image'), 'uploads/packages');
        }

        $package = Package::create([
            'name' => $request->name,
            'price' => $request->price,
            'validity' => $request->validity,
            'description' => $request->description,
            'features' => $request->features,
            'image' => $file,
            'status' => true,
        ]);

        return $this->successResponse($package, 'Package created successfully');
    }
}
