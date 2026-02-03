<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\Payment;
use App\Trait\ApiResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FinancialReportController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $country = $request->input('country');

        $query = Payment::with([
            'package',
            'user.profile',
        ]);

        if ($country) {
            $query->whereHas('user.profile', function ($q) use ($country) {
                $q->whereRaw('LOWER(country) LIKE ?', ['%'.strtolower($country).'%']);
            });
        }

        $payments = $query->orderBy('payments.created_at', 'desc')->get();

        $totalRevenue = Payment::where('status', 'completed')
            ->when($startDate && $endDate, function ($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [
                    $startDate.' 00:00:00',
                    $endDate.' 23:59:59',
                ]);
            })
            ->sum('amount');

        $pendingAmount = $payments
            ->where('status', 'pending')
            ->sum(fn ($p) => (float) $p->amount);

        $totalPackages = Package::count();
        $paymentMethods = Payment::where('status', 'completed')
            ->groupBy('method')
            ->select('method')
            ->selectRaw('COUNT(*) as count')
            ->limit(3)->get();

        $year = $startDate ? Carbon::parse($startDate)->year : now()->year;

        $revenueData = Payment::where('status', 'completed')
            ->whereYear('created_at', $year)
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

        $bestSellingPackages = Package::select('id', 'name')->withSum(
            ['payments as total_revenue' => function ($q) use ($startDate, $endDate) {
                $q->where('status', 'completed')
                    ->when($startDate && $endDate, function ($q) use ($startDate, $endDate) {
                        $q->whereBetween('created_at', [
                            $startDate.' 00:00:00',
                            $endDate.' 23:59:59',
                        ]);
                    });
            }], 'amount')->get();

        $financialSummary = Payment::where('status', 'completed')
            ->when($startDate && $endDate, function ($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate.' 00:00:00', $endDate.' 23:59:59']);
            })
            ->selectRaw('
                MONTH(created_at) as month,
                YEAR(created_at) as year,
                SUM(amount) as total_revenue,
                COUNT(*) as total_payments
            ')
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get()
            ->map(function ($payment, $key) use (&$lastRevenue) {
                $currentRevenue = (float) $payment->total_revenue;
                $growth = 0;
                if (isset($lastRevenue) && $lastRevenue > 0) {
                    $growth = (($currentRevenue - $lastRevenue) / $lastRevenue) * 100;
                }

                $lastRevenue = $currentRevenue;

                return [
                    'month' => date('F', mktime(0, 0, 0, $payment->month, 1)),
                    'year' => $payment->year,
                    'total_revenue' => $currentRevenue,
                    'total_payments' => (int) $payment->total_payments,
                    'growth_percentage' => round($growth, 2).'%',
                ];
            });

        $data = [
            'total_revenue' => $totalRevenue,
            'pending_amount' => $pendingAmount,
            'total_packages' => $totalPackages,
            'payment_methods' => $paymentMethods,
            'monthly_revenue' => $monthlyRevenue,
            'best_selling_packages' => $bestSellingPackages,
            'financial_summary' => $financialSummary,
        ];

        return $this->successResponse($data, 'Financial report fetched successfully');
    }
}
