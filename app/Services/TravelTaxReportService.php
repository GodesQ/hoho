<?php

namespace App\Services;

use App\Models\TravelTaxPayment;
use Carbon\Carbon;
use DB;

class TravelTaxReportService
{
    public function __construct()
    {

    }

    public static function getTravelTaxTxnCountPerMonth()
    {
        $results = TravelTaxPayment::select(
            DB::raw('DATE_FORMAT(transaction_time, "%M") as month_name'),
            DB::raw('YEAR(transaction_time) as year'),
            DB::raw('MONTH(transaction_time) as month'),
            DB::raw('COUNT(*) as total_count')
        )
            ->whereYear('transaction_time', date('Y'))
            ->groupBy('year', 'month', 'month_name')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        return $results;
    }

    public static function getTravelTaxTotalPayment()
    {
        $total_amount = TravelTaxPayment::whereMonth('created_at', Carbon::now()->format('m'))
            ->where('status', 'paid')
            ->sum('total_amount');

        return $total_amount;

        
    }
}