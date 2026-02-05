<?php

namespace App\Http\Controllers;

use App\Models\DeleteUser;
use App\Models\Profile;
use App\Models\User;
use App\Trait\ApiResponse;
use Illuminate\Http\Request;

class DetailedReportController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $country = $request->input('country');
        $status = $request->input('status');

        $userBaseQuery = User::query()
            ->when($startDate && $endDate, function ($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [
                    $startDate.' 00:00:00',
                    $endDate.' 23:59:59',
                ]);
            })
            ->when($status !== null, fn ($q) => $q->where('status', $status));

        $profileBaseQuery = Profile::query()
            ->join('users', 'users.id', '=', 'profiles.user_id')
            ->when($startDate && $endDate, function ($q) use ($startDate, $endDate) {
                $q->whereBetween('users.created_at', [
                    $startDate.' 00:00:00',
                    $endDate.' 23:59:59',
                ]);
            })
            ->when($country, fn ($q) => $q->where('country', $country))
            ->when($status !== null, fn ($q) => $q->where('status', $status));

        $dailyLogins = (clone $userBaseQuery)
            ->whereDate('last_login_at', now())
            ->count();

        $totalUsers = (clone $userBaseQuery)->count();

        $thisMonthUsers = (clone $userBaseQuery)
            ->whereBetween('created_at', [
                now()->startOfMonth(),
                now()->endOfMonth(),
            ])
            ->count();

        $lastMonthUsers = (clone $userBaseQuery)
            ->whereBetween('created_at', [
                now()->subMonth()->startOfMonth(),
                now()->subMonth()->endOfMonth(),
            ])
            ->count();

        $growthPercentage = $lastMonthUsers > 0
            ? round((($thisMonthUsers - $lastMonthUsers) / $lastMonthUsers) * 100, 1)
            : 0;

        $completeProfile = (clone $userBaseQuery)
            ->where('is_complete', 1)
            ->count();

        $completeRate = $totalUsers > 0
            ? round(($completeProfile / $totalUsers) * 100, 1)
            : 0;

        $deleteAccount = DeleteUser::count();
        $deleteRate = $totalUsers > 0
            ? round(($deleteAccount / $totalUsers) * 100, 1)
            : 0;

        $neverLoginUsers = (clone $userBaseQuery)
            ->whereNull('last_login_at')
            ->count();

        $neverLoginRate = $totalUsers > 0
            ? round(($neverLoginUsers / $totalUsers) * 100, 1)
            : 0;

        $relationStatus = (clone $profileBaseQuery)
            ->select('profiles.relationship')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('profiles.relationship')
            ->get()
            ->map(function ($row) use ($totalUsers) {
                return [
                    'label' => ucfirst($row->relationship),
                    'percentage' => $totalUsers > 0
                        ? round(($row->total / $totalUsers) * 100, 1)
                        : 0,
                ];
            });

        $originStats = (clone $profileBaseQuery)
            ->select('profiles.origin')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('profiles.origin')
            ->orderByDesc('total')
            ->get();

        $monthlyReligion = (clone $profileBaseQuery)
            ->selectRaw('MONTH(profiles.created_at) as month, COUNT(*) as total')
            ->whereYear('profiles.created_at', now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $religionMonthly = array_fill(0, 12, 0);

        foreach ($monthlyReligion as $row) {
            $religionMonthly[$row->month - 1] = $row->total;
        }

        $genderStats = (clone $profileBaseQuery)
            ->select('profiles.gender')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('profiles.gender')
            ->orderByDesc('total')
            ->get();

        $recentUsers = User::orderByDesc('created_at')
            ->limit(10)
            ->get();

        $data = [
            'daily_logins' => $dailyLogins,
            'growth' => $growthPercentage,
            'total_users' => $totalUsers,
            'complete_profile' => $completeProfile,
            'complete_rate' => $completeRate,
            'delete_account' => $deleteAccount,
            'delete_rate' => $deleteRate,
            'never_login' => $neverLoginUsers,
            'never_login_rate' => $neverLoginRate,
            'relationship_status' => $relationStatus,
            'origin_stats' => $originStats,
            'religion_monthly' => $religionMonthly,
            'gender_stats' => $genderStats,
            'recent_users' => $recentUsers,
        ];

        return $this->successResponse($data, 'Dashboard stats retrieved successfully');
    }
}
