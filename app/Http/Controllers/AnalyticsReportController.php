<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Trait\ApiResponse;
use Illuminate\Http\Request;

class AnalyticsReportController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $baseQuery = function () use ($startDate, $endDate) {
            return User::query()
                ->when($startDate && $endDate, function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('created_at', [
                        $startDate.' 00:00:00',
                        $endDate.' 23:59:59',
                    ]);
                });
        };

        $totalUsers = $baseQuery()->count();

        // This month users
        $thisMonth = User::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // Last month users
        $lastMonth = User::whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->count();

        // Growth percentage
        $lastMonthPercentage = $lastMonth > 0
            ? (($thisMonth - $lastMonth) / $lastMonth) * 100
            : ($thisMonth > 0 ? 100 : 0);

        $newSignups = User::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $thisWeek = User::whereBetween('created_at', [
            now()->subDays(6)->startOfDay(),
            now()->endOfDay(),
        ])->count();

        $lastWeek = User::whereBetween('created_at', [
            now()->subDays(13)->startOfDay(),
            now()->subDays(7)->endOfDay(),
        ])
            ->count();

        $weeklyGrowth = $lastWeek > 0
            ? (($thisWeek - $lastWeek) / $lastWeek) * 100
            : ($thisWeek > 0 ? 100 : 0);

        $data = [
            'totalUsers' => $totalUsers,
            'growthSinceLastMonth' => round($lastMonthPercentage, 2),
            'newSignups' => $newSignups,
            'weeklyGrowth' => round($weeklyGrowth, 2),
        ];

        return $this->successResponse($data, 'Analytics fetched successfully');
    }
}
