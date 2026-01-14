<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use App\Models\FaqCategory;
use App\Trait\ApiResponse;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $faqs = Faq::with('category:id,name')->get();

        if ($faqs->isEmpty()) {
            return $this->errorResponse('FAQ not found', 404);
        }

        return $this->successResponse($faqs, 'FAQ retrieved successfully');
    }
    

    public function store(Request $request)
    {
        $validated = $request->validate([
            'faq_category_id' => 'required|exists:faq_categories,id',
            'question' => 'required|string|unique:faqs,question',
            'answer' => 'required|string',
        ]);

        $faq = Faq::create([
            'faq_category_id' => $validated['faq_category_id'],
            'question' => $validated['question'],
            'answer' => $validated['answer'],
            'status' => true,
        ]);

        return $this->successResponse($faq, 'FAQ created successfully');
    }

    public function update(Request $request, $id)
    {
        $faq = Faq::find($id);

        if (!$faq) {
            return $this->errorResponse('FAQ not found', 404);
        }

        $validated = $request->validate([
            'faq_category_id' => 'required|exists:faq_categories,id',
            'question' => 'required|string|unique:faqs,question,' . $id,
            'answer' => 'required|string',
        ]);

        $faq->update([
            'faq_category_id' => $validated['faq_category_id'],
            'question' => $validated['question'],
            'answer' => $validated['answer'],
            'status' => true,
        ]);

        return $this->successResponse($faq, 'FAQ updated successfully');
    }   

    public function destroy($id)
    {
        $faq = Faq::find($id);

        if (!$faq) {
            return $this->errorResponse('FAQ not found', 404);
        }

        $faq->delete();

        return $this->successResponse(null, 'FAQ deleted successfully');
    }
}
