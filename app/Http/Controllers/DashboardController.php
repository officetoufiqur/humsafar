<?php

namespace App\Http\Controllers;

use App\Models\LookingFor;
use App\Models\User;
use App\Trait\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $userId = Auth::user()->id;

        $user = User::findOrFail($userId);

        if (! $user) {
            return $this->errorResponse('User not found', 404);
        }

        return $this->successResponse($user, 'Dashboard');
    }

    public function membersYouMayLike(Request $request)
    {
        $user = Auth::user();

        $pref = LookingFor::where('user_id', $user->id)->first();
        if (! $pref) {
            return response()->json([
                'status' => false,
                'message' => 'Please complete your preferences first.',
            ]);
        }

        [$ageMin, $ageMax] = $pref->looking_age_range
            ? array_map('intval', explode('-', $pref->looking_age_range))
            : [18, 80];

        $users = User::with('profile')
            ->where('id', '!=', $user->id)
            ->whereHas('profile', function ($q) use ($pref, $ageMin, $ageMax) {

                if ($pref->looking_gender) {
                    $q->whereRaw(
                        'LOWER(gender) = ?',
                        [strtolower($pref->looking_gender)]
                    );
                }

                $q->whereBetween('age', [$ageMin, $ageMax]);

                if ($pref->looking_religion) {
                    $q->where('religion', $pref->looking_religion);
                }

                if ($pref->looking_relationship) {
                    $q->where('relationship', $pref->looking_relationship);
                }

            })
            ->inRandomOrder()
            ->limit(12)
            ->get()
            ->map(function ($u) {
                $u->online_status = cache()->has('user-is-online-'.$u->id)
                    ? 'online'
                    : 'offline';

                return $u;
            });

        return $this->successResponse($users, 'Members you may like');
    }
}
