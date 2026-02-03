<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Trait\ApiResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MemberReportController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $country = $request->country;
        $status = $request->status;

        $baseQuery = function () use ($startDate, $endDate, $country, $status) {
            return User::query()
                ->when($startDate && $endDate, function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('created_at', [
                        $startDate.' 00:00:00',
                        $endDate.' 23:59:59',
                    ]);
                })
                ->when($country, function ($q) use ($country) {
                    $q->whereHas('profile', function ($sub) use ($country) {
                        $sub->whereRaw(
                            'LOWER(country) LIKE ?',
                            ['%'.strtolower($country).'%']
                        );
                    });
                })
                ->when($status, function ($q) use ($status) {
                    $q->where('status', $status);
                });
        };

        $totalMembers = $baseQuery()->count();

        $thisMonth = $baseQuery()
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        $lastMonth = $baseQuery()
            ->whereMonth('created_at', Carbon::now()->subMonth()->month)
            ->whereYear('created_at', Carbon::now()->subMonth()->year)
            ->count();

        $activeMembers = $baseQuery()
            ->where('status', 'active')
            ->count();

        $activePercentage = $totalMembers > 0
            ? ($activeMembers / $totalMembers) * 100
            : 0;

        $progressPercentage = $lastMonth > 0
            ? (($thisMonth - $lastMonth) / $lastMonth) * 100
            : ($thisMonth > 0 ? 100 : 0);

        $newMemberGrowth = ($thisMonth / $totalMembers) * 100;

        $lastMonthGrowth = $lastMonth > 0
            ? (($thisMonth - $lastMonth) / $lastMonth) * 100
            : 0;

        $avgMembership = DB::table('payments')
            ->selectRaw('COUNT(*) / COUNT(DISTINCT user_id) as avg_membership')
            ->where('status', 'completed')
            ->value('avg_membership');

        $memberByRole = DB::table('model_has_roles')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->where('model_has_roles.model_type', User::class)
            ->selectRaw('roles.name as role, COUNT(*) as count')
            ->groupBy('role')
            ->get();

        $memberStatus = User::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();

        $recentMembers = User::select('id', 'display_name', 'created_at', 'status')->with(['roles', 'profile:id,user_id,relationship,country,city,gender,age'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $data = [
            'total_members' => $totalMembers,
            'active_members' => $activeMembers,
            'active_percentage' => round($activePercentage, 2),
            'progress_percentage' => round($progressPercentage, 2),
            'new_members' => $thisMonth,
            'new_member_growth' => $newMemberGrowth,
            'last_month_growth' => $lastMonthGrowth,
            'avg_membership' => $avgMembership,
            'member_by_role' => $memberByRole,
            'member_status' => $memberStatus,
            'recent_members' => $recentMembers,
        ];

        return $this->successResponse($data, 'Member report fetched successfully');
    }
}
