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

        $ageMin = 18;
        $ageMax = 80;

        if ($pref->looking_age_range) {
            [$ageMin, $ageMax] = array_map('intval', explode('-', $pref->looking_age_range));
        }

        $query = User::with('profile')
            ->where('id', '!=', $user->id)

            ->whereHas('profile', function ($q) use ($pref, $ageMin, $ageMax) {

                if ($pref->looking_gender) {
                    $q->where('gender', $pref->looking_gender);
                }

                if ($pref->looking_religion) {
                    $q->where('religion', $pref->looking_religion);
                }

                if ($pref->looking_location) {
                    $q->where('location', $pref->looking_location);
                }

                if ($pref->looking_education) {
                    $q->where('education', $pref->looking_education);
                }

                $q->whereBetween('dob', [
                    now()->subYears($ageMax),
                    now()->subYears($ageMin),
                ]);
            })

            ->inRandomOrder()
            ->limit(12)
            ->get()

            ->map(function ($u) {
                $u->online = cache()->has('user-is-online-'.$u->id);
                $u->age = $u->profile ? now()->diffInYears($u->profile->dob) : null;

                return $u;
            });

        return $this->successResponse($query, 'Members you may like');
    }
}
