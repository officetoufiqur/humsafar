<?php

namespace App\Http\Controllers;

use App\Models\Work;
use App\Trait\ApiResponse;
use Illuminate\Http\Request;

class WorkController extends Controller
{
    use ApiResponse;
    public function index()
    {
        $works = Work::with('steps')->first();

        if (!$works) {
            return $this->errorResponse('No works found.', 404);
        }

        return $this->successResponse($works, 'Works retrieved successfully.');
    }
}
