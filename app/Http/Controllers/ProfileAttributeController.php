<?php

namespace App\Http\Controllers;

use App\Models\ProfileAttribute;
use App\Trait\ApiResponse;
use Illuminate\Http\Request;

class ProfileAttributeController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $attributes = ProfileAttribute::select(
            'id',
            'label',
            'values',
            'showOn',
        )
            ->selectRaw('JSON_LENGTH(`values`) as values_count')
            ->get();

        return $this->successResponse($attributes);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'label' => 'required|string|max:255',
            'values' => 'nullable|array',
            'showOn' => 'required|boolean',
        ]);

        $attribute = ProfileAttribute::findOrFail($id);

        $attribute->update([
            'label' => $request->label,
            'values' => $request->values, 
            'showOn' => $request->showOn,
        ]);

        return response()->json([
            'message' => 'Profile Attribute updated successfully',
            'data' => $attribute,
        ]);
    }
}
