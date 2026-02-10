<?php

namespace App\Http\Controllers;

use App\Helpers\FileUpload;
use App\Models\Package;
use App\Models\PaymentSetting;
use App\Trait\ApiResponse;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    use ApiResponse;

    public function packagesList()
    {
        $setting = PaymentSetting::first();

        return response()->json([
            'mollie' => $setting?->mollie_status ?? false,
            'stripe' => $setting?->stripe_status ?? false,
        ]);
    }

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

    public function packagesEdit($id)
    {
        $package = Package::find($id);

        if (! $package) {
            return $this->errorResponse('Package not found', 404);
        }

        return $this->successResponse($package, 'Package fetched successfully');
    }

    public function packagesUpdate(Request $request, $id)
    {
        $package = Package::find($id);

        if (! $package) {
            return $this->errorResponse('Package not found', 404);
        }

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'price' => 'sometimes|required|numeric',
            'validity' => 'sometimes|required|integer',
            'description' => 'sometimes|required|string',
            'features' => 'sometimes|required|array',
            'features*' => 'sometimes|required|array',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif,svg|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($package->image) {
                FileUpload::deleteFile($package->image);
            }
            $package->image = FileUpload::storeFile($request->file('image'), 'uploads/packages');
        }

        $package->name = $request->name;
        $package->price = $request->price;
        $package->validity = $request->validity;
        $package->description = $request->description;
        $package->features = $request->features;
        if ($request->has('status')) {
            $package->status = $request->status;
        }

        $package->save();

        return $this->successResponse($package, 'Package updated successfully');
    }

    public function packagesUpdateStatus($id)
    {
        $package = Package::find($id);

        if (! $package) {
            return $this->errorResponse('Package not found', 404);
        }

        $package->status = ! $package->status;
        $package->save();

        $status = $package->status ? 'activated' : 'deactivated';

        return $this->successResponse($package, "Package {$status} successfully");
    }
}
