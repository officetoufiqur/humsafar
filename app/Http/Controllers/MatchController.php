<?php

namespace App\Http\Controllers;

use App\Models\LookingFor;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MatchController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $pref = LookingFor::where('user_id', $user->id)->first();
        if (! $pref) {
            return response()->json([
                'status' => false,
                'message' => 'Please complete your preferences.',
            ]);
        }

        $onlyOnline = $request->query('online');
        $onlineLimit = now()->subMinutes(5);

        $ageMin = 0;
        $ageMax = 100;
        if ($pref->looking_age_range) {
            [$ageMin,$ageMax] = array_map('intval', explode('-', $pref->looking_age_range));
        }

        $query = Profile::join('users', 'users.id', '=', 'profiles.user_id')
            ->where('profiles.user_id', '!=', $user->id);

        if ($onlyOnline === 'true') {
            $query->whereNotNull('users.is_online')
                ->where('users.is_online', '>=', $onlineLimit);
        }

        $query->selectRaw('
        profiles.*,

        (
            (CASE WHEN religion = ? THEN 25 ELSE 0 END) +
            (CASE WHEN age BETWEEN ? AND ? THEN 20 ELSE 0 END) +
            (CASE WHEN location = ? THEN 15 ELSE 0 END) +
            (CASE WHEN education = ? THEN 15 ELSE 0 END) +
            (
                (CASE WHEN smoke = ? THEN 5 ELSE 0 END) +
                (CASE WHEN drinking = ? THEN 5 ELSE 0 END) +
                (CASE WHEN going_out = ? THEN 5 ELSE 0 END)
            ) +
            (CASE WHEN origin = ? THEN 10 ELSE 0 END)
        ) AS score,

        IF(users.is_online >= ?, "online", "offline") AS online_status
    ', [
            $pref->looking_religion,
            $ageMin, $ageMax,
            $pref->looking_location,
            $pref->looking_education,
            $pref->looking_smoke,
            $pref->looking_drinking,
            $pref->looking_going_out,
            $pref->looking_origin,
            $onlineLimit,
        ]);

        $matches = $query->orderByDesc('score')
            ->limit(50)
            ->get();

        return response()->json([
            'status' => true,
            'total' => $matches->count(),
            'data' => $matches,
        ]);
    }
}
