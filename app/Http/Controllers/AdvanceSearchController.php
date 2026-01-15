<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdvanceSearchController extends Controller
{
    public function searchProfiles(Request $request)
    {
        $authId = Auth::user()->id;

        $users = User::with('profile')
            ->where('id', '!=', $authId)

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

            ->map(function ($user) {

                $user->online = cache()->has('user-is-online-'.$user->id);
                $user->age = $user->profile ? now()->diffInYears($user->profile->dob) : null;

                return $user;
            })

            ->filter(function ($user) use ($request) {
                if ($request->online) {
                    return $user->online;
                }

                return true;
            })
            ->values();

        return response()->json([
            'status' => true,
            'data' => $users,
        ]);
    }
}
