<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\Payment;
use App\Trait\ApiResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $payments = Payment::with('user:id,fname,lname')->when(
            $request->status,
            fn ($q) => $q->where('status', $request->status)
        )->get();

        $total = $payments->count();
        $activePackage = Package::where('status', 1)->count();
        $thisMonthPayments = Payment::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('amount');
        $lastMonthPayments = Payment::whereMonth('created_at', now()->subMonth()->month)->whereYear('created_at', now()->subMonth()->year)->sum('amount');
        $thisYearPayments = Payment::whereYear('created_at', now()->year)->sum('amount');
        $lastYearPayments = Payment::whereYear('created_at', now()->subYear()->year)->sum('amount');

        $data = [
            'total' => $total,
            'active_package' => $activePackage,
            'this_month_payments' => $thisMonthPayments,
            'last_month_payments' => $lastMonthPayments,
            'this_year_payments' => $thisYearPayments,
            'last_year_payments' => $lastYearPayments,
            'payments' => $payments,
        ];

        return $this->successResponse($data, 'Payments fetched successfully');
    }

    public function view($id)
    {
        $payment = Payment::with([
            'user:id,fname,lname,photo,display_name,email',
            'user.profile:id,user_id,location',
        ])->findOrFail($id);

        return $this->successResponse($payment, 'Payment details');
    }
}
