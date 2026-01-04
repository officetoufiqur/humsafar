<?php

namespace App\Http\Controllers;

use App\Helpers\FileUpload;
use App\Models\Package;
use App\Trait\ApiResponse;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    use ApiResponse;

    public function getPackages(Request $request)
    {
        $parameter = $request->query('parameter');

        $query = Package::query();

        if ($parameter === 'active') {
            $query->where('status', true);
            $message = 'Active packages fetched successfully';

        } elseif ($parameter === 'inactive') {
            $query->where('status', false);
            $message = 'Inactive packages fetched successfully';

        } elseif ($parameter !== null) {
            return $this->errorResponse(
                'Invalid parameter value',
                422
            );
        } else {
            $message = 'Packages fetched successfully';
        }

        return $this->successResponse(
            $query->get(),
            $message
        );
    }

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
