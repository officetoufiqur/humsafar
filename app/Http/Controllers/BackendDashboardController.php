<?php

namespace App\Http\Controllers;

use App\Models\MatchUser;
use App\Models\Package;
use App\Models\Payment;
use App\Models\User;
use App\Trait\ApiResponse;
use Illuminate\Http\Request;

class BackendDashboardController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $totalMember = User::count();
        $vipMember = User::where('membership_type', 'vip')->count();
        $freeMember = User::where('membership_type', 'free')->count();
        $blockedMember = User::where('status', 'blocked')->count();

        $data = [
            'totalMember' => $totalMember,
            'vipMember' => $vipMember,
            'freeMember' => $freeMember,
            'blockedMember' => $blockedMember,
        ];

        return $this->successResponse($data, 'Dashboard fetched successfully');
    }

    public function bestSelling()
    {
        $topPackage = Package::withCount(['payments as total_sales' => function ($q) {
            $q->where('status', 'completed')
                ->whereYear('created_at', now()->year);
        }])
            ->orderByDesc('total_sales')
            ->first();

        return $this->successResponse($topPackage, 'Top package fetched successfully');
    }

    public function newUsers()
    {
        $newUsers = User::with(['profile:id,user_id,gender,age', 'payments:id,user_id,package_id,status,tier,created_at'])
            ->select('id', 'fname', 'lname', 'photo', 'created_at')
            ->whereDate('created_at', now()->toDateString())
            ->latest()->take(5)->get();

        if ($newUsers->isNotEmpty()) {
            return $this->successResponse($newUsers, 'New users fetched successfully');
        }

        return $this->successResponse([], 'No new users found');
    }

    public function performance(Request $request)
    {
        $year = $request->year ?? now()->year;

        $data = User::selectRaw('
            MONTH(created_at) as month,
            COUNT(*) as total
        ')
            ->whereYear('created_at', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $monthlyData = collect(range(1, 12))->map(function ($month) use ($data) {
            return [
                'month' => date('M', mktime(0, 0, 0, $month, 1)),
                'total' => $data->firstWhere('month', $month)->total ?? 0,
            ];
        });

         // ---- Daily Avg ----
        $start = now()->startOfMonth();
        $end = now();

        $totalUsersThisMonth = User::whereBetween('created_at', [$start, $end])->count();
        $daysPassed = $start->diffInDays($end) + 1;

        $avgDaily = round($totalUsersThisMonth / $daysPassed, 1);

        // ---- Monthly ----
        $currentMonth = User::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $lastMonth = User::whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->count();

        // ---- Yearly Trend ----
        $thisYear = User::whereYear('created_at', now()->year)->count();
        $lastYear = User::whereYear('created_at', now()->subYear()->year)->count();

        $growthPercent = $lastYear > 0
            ? round((($thisYear - $lastYear) / $lastYear) * 100, 1)
            : 0;

        $data = [
            'avgDaily' => $avgDaily,
            'monthly' => [
                'total' => $currentMonth,
                'difference' => $currentMonth - $lastMonth,
            ],
            'growthPercent' => $growthPercent,
            'monthlyData' => $monthlyData,
        ];

        return $this->successResponse($data, 'Performance data fetched');
    }

    public function matchingSatisfaction(Request $request)
    {
        $year = $request->year ?? now()->year;

        $rows = MatchUser::selectRaw('
            MONTH(created_at) as month,
            COUNT(*) as total_matches,
            ROUND(AVG(score / 20), 2) as rating
        ')
            ->whereYear('created_at', $year)
            ->where('score', '>', 0)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $monthlyData = collect(range(1, 12))->map(function ($month) use ($rows) {
            $row = $rows->firstWhere('month', $month);

            return [
                'month' => date('M', mktime(0, 0, 0, $month, 1)),
                'rating' => (float) ($row->rating ?? 0),
                'total_matches' => (int) ($row->total_matches ?? 0),
            ];
        });

        $totalEarnings = Payment::where('status', 'completed')->sum('amount');

        $last12MonthsEarnings = Payment::where('status', 'completed')
            ->whereBetween('created_at', [now()->subMonths(12), now()])
            ->sum('amount');

        $last6MonthsEarnings = Payment::where('status', 'completed')
            ->whereBetween('created_at', [now()->subMonths(6), now()])
            ->sum('amount');

        $data = [
            'total_earnings' => $totalEarnings,
            'last_12_months_earnings' => $last12MonthsEarnings,
            'last_6_months_earnings' => $last6MonthsEarnings,
            'chart' => $monthlyData,
        ];

        return $this->successResponse($data, 'Monthly matching satisfaction fetched');
    }
}
