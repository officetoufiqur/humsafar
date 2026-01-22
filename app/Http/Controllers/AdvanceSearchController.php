<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Trait\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdvanceSearchController extends Controller
{
    use ApiResponse;

    public function searchProfiles(Request $request)
    {
        $authId = Auth::user()->id;

        $onlyOnline = $request->query('online');
        $onlineLimit = now()->subMinutes(5);

        $users = User::with('profile')
            ->where('id', '!=', $authId)

            ->when($onlyOnline === 'true', function ($q) use ($onlineLimit) {
                $q->whereNotNull('is_online')
                    ->where('is_online', '>=', $onlineLimit);
            })

            ->whereHas('profile', function ($q) use ($request) {

                if ($request->looking_for) {
                    $q->where('gender', $request->looking_for);
                }

                if ($request->relation_status) {
                    $q->where('relation_status', $request->relation_status);
                }

                if ($request->country) {
                    $q->where('country', 'like', '%'.$request->country.'%');
                }

                if ($request->from_age && $request->to_age) {
                    $from = now()->subYears($request->to_age);
                    $to = now()->subYears($request->from_age);
                    $q->whereBetween('dob', [$from, $to]);
                }

                if ($request->religion) {
                    $q->where('religion', $request->religion);
                }

                if ($request->children) {
                    $q->where('children', $request->children);
                }

                if ($request->education) {
                    $q->where('education', $request->education);
                }
            })

            ->when($request->search, function ($q) use ($request) {
                $q->where('fname', 'like', '%'.$request->search.'%');
            })

            ->get()

            ->map(function ($user) use ($onlineLimit) {
                $user->online_status = $user->is_online && $user->is_online >= $onlineLimit
                    ? 'online'
                    : 'offline';

                $user->age = $user->profile
                    ? now()->diffInYears($user->profile->dob)
                    : null;

                return $user;
            });

        return $this->successResponse($users, 'Profiles fetched successfully');
    }
}
