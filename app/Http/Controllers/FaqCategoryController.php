<?php

namespace App\Http\Controllers;

use App\Models\FaqCategory;
use App\Trait\ApiResponse;
use Illuminate\Http\Request;

class FaqCategoryController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $categorys = FaqCategory::all();

        if ($categorys->isEmpty()) {
            return $this->errorResponse('No FAQ categories found', 404);
        }

        return $this->successResponse($categorys, 'FAQ categories retrieved successfully');
    }

    public function store(Request $request)
    {
        $vlidated = $request->validate([
            'name' => 'required|string|unique:faq_categories,name'
        ]);

        $slug = strtolower(str_replace(' ', '-', $vlidated['name']));

        $category = FaqCategory::create([
            'name' => $vlidated['name'],
            'slug' => $slug,
            'status' => true,
        ]);

        return $this->successResponse($category, 'FAQ category created successfully');
    }

    public function update(Request $request, $id)
    {
        $category = FaqCategory::find($id);

        if (!$category) {
            return $this->errorResponse('FAQ category not found', 404);
        }

        $vlidated = $request->validate([
            'name' => 'required|string|unique:faq_categories,name,' . $id
        ]);

        $slug = strtolower(str_replace(' ', '-', $vlidated['name']));

        $category->update([
            'name' => $vlidated['name'],
            'slug' => $slug,
            'status' => true,
        ]);

        return $this->successResponse($category, 'FAQ category updated successfully');
    }

    public function destroy($id)
    {
        $category = FaqCategory::find($id);

        if (!$category) {
            return $this->errorResponse('FAQ category not found', 404);
        }

        $category->delete();

        return $this->successResponse(null, 'FAQ category deleted successfully');
    }
}
