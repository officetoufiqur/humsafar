<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Payment;
use App\Trait\ApiResponse;
use Illuminate\Support\Facades\DB;

class OverviewController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $financial = Payment::where('status', 'completed')->sum('amount');

        $currentYearTotal = Payment::where('status', 'completed')
            ->whereYear('created_at', now()->year)
            ->sum('amount');

        $lastYearTotal = Payment::where('status', 'completed')
            ->whereYear('created_at', now()->subYear()->year)
            ->sum('amount');

        $financialGrowth = $currentYearTotal - $lastYearTotal;

        $financialGrowthPercentage = $lastYearTotal > 0
            ? (($financialGrowth / $lastYearTotal) * 100)
            : 0;

        $avgMonthly = Payment::where('status', 'completed')->avg('amount');

        $last3Months = Payment::where('status', 'completed')
            ->whereBetween('created_at', [now()->subMonths(3), now()])
            ->sum('amount');

        $last3MonthsGrowth = $last3Months - Payment::where('status', 'completed')
            ->whereBetween('created_at', [now()->subMonths(6), now()->subMonths(3)])
            ->sum('amount');

        $last3MonthsGrowthPercentage = $last3Months > 0
            ? (($last3MonthsGrowth / $last3Months) * 100)
            : 0;

        $totalVisitors = User::count();
        $newVisitors = User::where('created_at', '>=', now()->subDays(30))->count();
        $avgVisitors = User::where('created_at', '>=', now()->subDays(30))->count() / 30;

        $totalMembers = User::where('status', 'active')->count();
        $activePercentage = $totalMembers > 0
          ? ($totalMembers / User::count()) * 100
          : 0;

         $matchesCurrent = DB::table('match_users')
            ->where('created_at', '>=', now()->subDays(30))
            ->count();

        $previous30Days = DB::table('match_users')
            ->whereBetween('created_at', [
                now()->subDays(60),
                now()->subDays(30),
            ])
            ->count();

        $messagesCurrent = DB::table('messages')
            ->where('created_at', '>=', now()->subDays(30))
            ->count();

        $matchesChangePercent = $previous30Days > 0
            ? (($matchesCurrent - $previous30Days) / $previous30Days) * 100
            : 0;

        return $this->successResponse([
            'financial' => $financial,
            'financial_growth' => round($financialGrowthPercentage, 2),
            'income_avg_monthly' => $avgMonthly,
            'income_last_3_months' => $last3Months,
            'income_last_3_months_growth' => round($last3MonthsGrowthPercentage, 2),
            'total_visitors' => $totalVisitors,
            'new_visitors' => $newVisitors,
            'avg_visitors_per_day' => round($avgVisitors, 2),
            'total_active_members' => $totalMembers,
            'active_members_percentage' => round($activePercentage, 2),
            'matches_last_30_days' => $matchesCurrent,
            'matches_growth_percentage' => round($matchesChangePercent, 2),
            'messages_last_30_days' => $messagesCurrent,
        ]);
    }
}
