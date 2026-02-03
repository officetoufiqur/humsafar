<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Trait\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IncomeReportController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $country = $request->input('country');

        $avgMonthly = Payment::where('status', 'completed')
            ->when($startDate && $endDate, function ($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [
                    $startDate.' 00:00:00',
                    $endDate.' 23:59:59',
                ]);
            })
            ->avg('amount');

        $last3Months = Payment::where('status', 'completed')
            ->whereBetween('created_at', [now()->subMonths(3), now()])
            ->sum('amount');

        $totalThisYear = Payment::where('status', 'completed')
            ->whereYear('created_at', now()->year)
            ->sum('amount');

        $diffLastYear = Payment::where('status', 'completed')
            ->whereYear('created_at', now()->subYear()->year)
            ->sum('amount');

        $revenueData = Payment::where('status', 'completed')
            ->whereYear('created_at', now()->year)
            ->when($startDate && $endDate, function ($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [
                    $startDate.' 00:00:00',
                    $endDate.' 23:59:59',
                ]);
            })
            ->selectRaw('MONTH(created_at) as month, SUM(amount) as total')
            ->groupBy('month')
            ->get()
            ->keyBy('month');

        $monthlyRevenue = collect(range(1, 12))->map(function ($month) use ($revenueData) {
            return [
                'month' => date('M', mktime(0, 0, 0, $month, 1)),
                'total' => isset($revenueData[$month])
                    ? (float) $revenueData[$month]->total
                    : 0,
            ];
        });

        $revenueByGender = Payment::where('payments.status', 'completed')
            ->join('users', 'payments.user_id', '=', 'users.id')
            ->join('profiles', 'profiles.user_id', '=', 'users.id')
            ->select('profiles.gender')
            ->selectRaw('SUM(payments.amount) as total_revenue')
            ->groupBy('profiles.gender')
            ->get();

        $rows = Payment::where('payments.status', 'completed')
            ->whereYear('payments.created_at', now()->year)
            ->when($startDate && $endDate, function ($q) use ($startDate, $endDate) {
                $q->whereBetween('payments.created_at', [
                    $startDate.' 00:00:00',
                    $endDate.' 23:59:59',
                ]);
            })
            ->join('users', 'payments.user_id', '=', 'users.id')
            ->selectRaw('
                users.id as user_id,
                users.fname as customer,
                MONTH(payments.created_at) as month,
                SUM(payments.amount) as total
            ')
            ->groupBy(
                'users.id',
                'users.fname',
                DB::raw('MONTH(payments.created_at)')
            )
            ->get();

        $customers = $rows
            ->groupBy('user_id')
            ->map(function ($items) {
                $months = $items->keyBy('month');

                $monthly = collect(range(1, 12))->map(function ($month) use ($months) {
                    return (float) ($months[$month]->total ?? 0);
                });

                return [
                    'customer' => $items->first()->customer,
                    'months' => $monthly->values(),
                    'total' => $monthly->sum(),
                ];
            })->values();

        $data = [
            'avgMonthly' => $avgMonthly,
            'last3Months' => $last3Months,
            'totalThisYear' => $totalThisYear,
            'diffLastYear' => $diffLastYear,
            'monthlyRevenue' => $monthlyRevenue,
            'revenueByGender' => $revenueByGender,
            'customers' => $customers,
        ];

        return $this->successResponse($data, 'Average monthly income');

    }
}
