<?php

namespace App\Http\Controllers;

use App\Models\ProfileVisit;
use App\Trait\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileVisitController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $userId = Auth::id();
        $status = $request->query('status');

        $visitors = ProfileVisit::with('visitor')
            ->where('visited_id', $userId)
            ->latest('visited_at')
            ->get()
            ->groupBy('visitor_id')
            ->map(function ($group) {
                $visit = $group->first();
                $visitor = $visit->visitor;

                return [
                    'visitor' => $visitor,
                    'online' => cache()->has('user-is-online-'.$visit->visitor->id),
                    'visited_at' => $visit->visited_at,
                ];
            })
            ->filter(function ($user) use ($status) {
                if ($status === 'active') {
                    return $user['online'];
                }
                if ($status === 'inactive') {
                    return ! $user['online'];
                }

                return true;
            })
            ->values()
            ->take(20);

        return $this->successResponse($visitors, 'Visitors retrieved successfully');
    }

    public function store($id)
    {
        $user = Auth::user();
        if ($user->id !== (int) $id) {
            ProfileVisit::create([
                'visitor_id' => $user->id,
                'visited_id' => $id,
                'visited_at' => now(),
            ]);
        }

        return $this->successResponse([], 'Profile visited successfully');
    }
}
