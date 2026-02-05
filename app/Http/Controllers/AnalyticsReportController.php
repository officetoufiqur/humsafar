<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Trait\ApiResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        // Date ranges
        $last30Days = Carbon::now()->subDays(30);
        $previous30Days = Carbon::now()->subDays(60);

        // Matches
        $matchesCurrent = DB::table('match_users')
            ->where('created_at', '>=', $last30Days)
            ->count();

        $matchesPrevious = DB::table('match_users')
            ->whereBetween('created_at', [$previous30Days, $last30Days])
            ->count();

        // Messages
        $messagesCurrent = DB::table('messages')
            ->where('created_at', '>=', $last30Days)
            ->count();

        $messagesPrevious = DB::table('messages')
            ->whereBetween('created_at', [$previous30Days, $last30Days])
            ->count();

        $matchesChangePercent = $this->percentChange($matchesPrevious, $matchesCurrent);
        $messagesChangePercent = $this->percentChange($messagesPrevious, $messagesCurrent);

        $countries = DB::table('profiles')
            ->select('country', DB::raw('COUNT(*) as total'))
            ->whereNotNull('country')
            ->groupBy('country')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $countriesTotal = $countries->sum('total');

        $topCountries = $countries->map(function ($item) use ($countriesTotal) {
            return [
                'name' => $item->country,
                'percentage' => round(($item->total / $countriesTotal) * 100, 1),
            ];
        });

        $cities = DB::table('profiles')
            ->select('city', DB::raw('COUNT(*) as total'))
            ->whereNotNull('city')
            ->groupBy('city')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $maxCityCount = $cities->max('total');

        $topCities = $cities->map(function ($item) use ($maxCityCount) {
            return [
                'name' => $item->city,
                'count' => $item->total,
                'percentage' => round(($item->total / $maxCityCount) * 100),
            ];
        });


        $data = [
            'totalUsers' => $totalUsers,
            'growthSinceLastMonth' => round($lastMonthPercentage, 2),
            'newSignups' => $newSignups,
            'weeklyGrowth' => round($weeklyGrowth, 2),
            'matchesCurrent' => $matchesCurrent,
            'matchesChangePercent' => round($matchesChangePercent, 2),
            'messagesCurrent' => $messagesCurrent,
            'messagesChangePercent' => round($messagesChangePercent, 2),
            'topCountries' => $topCountries,
            'topCities' => $topCities,
        ];

        return $this->successResponse($data, 'Analytics fetched successfully');
    }

    private function percentChange($old, $new)
    {
        if ($old == 0) {
            return $new > 0 ? 100 : 0;
        }

        return round((($new - $old) / $old) * 100, 1);
    }
}
