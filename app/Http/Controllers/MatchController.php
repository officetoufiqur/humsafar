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

        $matches = Profile::where('gender',$pref->looking_for)
            ->whereBetween('age',[$pref->age_min,$pref->age_max])
            ->where('religion',$pref->religion)
            ->where('education',$pref->education)
            ->where('relationship',$pref->relationship_status)
            ->where('smoke',$pref->smoke)
            ->where('drinking',$pref->drinks)                      
            ->where('going_out',$pref->going_out)
            ->where('children',$pref->children)
            ->where('origin',$pref->origin)
            ->where('user_id','!=',$user->id)
            ->selectRaw("
                profiles.*,
                (6371 * acos(
                    cos(radians(?)) * cos(radians(latitude)) *
                    cos(radians(longitude) - radians(?)) +
                    sin(radians(?)) * sin(radians(latitude))
                )) as distance
            ", [$user->latitude,$user->longitude,$user->latitude])
            ->having('distance','<=',$pref->distance_km)
            ->orderBy('distance','asc')
            ->limit(50)
            ->get();

        return response()->json([
            'status' => true,
            'total' => $matches->count(),
            'data' => $matches
        ]);
    }
}
