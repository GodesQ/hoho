<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Report\TourReservationReportRequest;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\TourReservation;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class DataReportController extends Controller
{
    public function user_demographics(Request $request)
    {
        return view('admin-page.reports.user_demographics');
    }

    public function sales_report(Request $request)
    {
        return view('admin-page.reports.sales-report');
    }

    public function tour_reservations_report(Request $request)
    {
        return view('admin-page.reports.tour_reservations_report');
    }

    public function travel_taxes_report(Request $request) {
        return view('admin-page.reports.travel-taxes-report');
    }

    public function getTravelTaxCustomersPerMonth(Request $request) {
        
    }

    public function getUsersByLocation(Request $request)
    {
        $userCounts = User::select('country_of_residence', \DB::raw('COUNT(1) as total_user'))
            ->whereNotNull('country_of_residence')
            ->groupBy('country_of_residence')
            ->orderByDesc('total_user')
            ->limit(8)
            ->get();

        $userCounts->each(fn($user) => $user->setAppends([]));

        return response([
            'status' => true,
            'result' => $userCounts
        ]);
    }

    public function getUsersPerMonth(Request $request)
    {

        $userCounts = User::select(DB::raw('MONTH(created_at) as month, MONTHNAME(created_at) AS month_name, COUNT(*) as total_user'))
            ->whereYear('created_at', '=', 2024)
            ->groupBy('month_name', 'month')
            ->orderBy('month', 'ASC')
            ->get();

        $userCounts->each(fn($user) => $user->setAppends([]));

        return response([
            'status' => true,
            'result' => $userCounts
        ]);
    }

    public function getUsersByAge(Request $request)
    {
        $users = User::selectRaw('
            SUM(CASE WHEN age BETWEEN 4 AND 6 THEN 1 ELSE 0 END) AS "4-6",
            SUM(CASE WHEN age BETWEEN 7 AND 9 THEN 1 ELSE 0 END) AS "7-9",
            SUM(CASE WHEN age BETWEEN 10 AND 12 THEN 1 ELSE 0 END) AS "10-12",
            SUM(CASE WHEN age BETWEEN 13 AND 15 THEN 1 ELSE 0 END) AS "13-15",
            SUM(CASE WHEN age BETWEEN 16 AND 30 THEN 1 ELSE 0 END) AS "16-30",
            SUM(CASE WHEN age BETWEEN 31 AND 45 THEN 1 ELSE 0 END) AS "31-45",
            SUM(CASE WHEN age BETWEEN 46 AND 65 THEN 1 ELSE 0 END) AS "46-65",
            SUM(CASE WHEN age BETWEEN 66 AND 85 THEN 1 ELSE 0 END) AS "66-85"
        ')->first();

        $users->setAppends([]);

        return response([
            'status' => true,
            'result' => $users,
        ]);
    }

    public function getTourReservationData(TourReservationReportRequest $request)
    {
        $tour_reservations = TourReservation::whereBetween('start_date', [$request->from_date, $request->to_date])
            ->where('type', $request->tour_type)
            ->when($request->status, function ($q) use ($request) {
                return $q->where('status', $request->status);
            })
            ->has('user')
            ->with('user')
            ->orderBy('start_date', 'desc')
            ->get();

        return view('admin-page.printable_pages.reservation_print_page', compact('tour_reservations'));
    }

    public function getCurrentMonthProfit(Request $request)
    {
        $currentMonth = now()->format('Y-m');

        $totalProfit = Transaction::where('payment_status', 'success')
            ->where(DB::raw('DATE_FORMAT(payment_date, "%Y-%m")'), $currentMonth)
            ->sum('payment_amount');

        return response([
            'status' => TRUE,
            'total_profit' => number_format($totalProfit, 2)
        ]);
    }

    public function getTopSellingTours(Request $request)
    {
        $currentMonth = now()->format('Y-m');

        $topSellingTours = TourReservation::select('tour_id', DB::raw('count(*) as total_reservations'), DB::raw('sum(amount) as total_amount'))
            ->where('status', 'approved')
            ->where(DB::raw('DATE_FORMAT(created_at, "%Y-%m")'), $currentMonth)
            ->groupBy('tour_id')
            ->orderBy('total_reservations', 'desc')
            ->take(4)
            ->with('tour')
            ->get();

        $total_orders = TourReservation::where('status', 'approved')->count();

        return response([
            'status' => TRUE,
            'total_orders' => $total_orders,
            'top_selling_tours' => $topSellingTours
        ]);
    }

    function getTotalBookingsPerType(Request $request)
    {
        $totalDIYTours = TourReservation::where('status', 'approved')->where('type', 'DIY')->count();
        $totalGuidedTours = TourReservation::where('status', 'approved')->where('type', 'Guided')->count();

        return response([
            'status' => TRUE,
            'total_diy_tours' => $totalDIYTours,
            'total_guided_tours' => $totalGuidedTours
        ]);
    }

    public function getSalesData()
    {
        $currentMonth = now()->month;
        $months = range(1, $currentMonth);

        $transactions = Transaction::select(DB::raw('SUM(payment_amount) as total_sales'), DB::raw('MONTH(payment_date) as month'))
            ->whereYear('payment_date', now()->year)
            ->whereIn(DB::raw('MONTH(payment_date)'), $months)
            ->groupBy(DB::raw('MONTH(payment_date)'))
            ->get();

        $salesData = [];
        foreach ($months as $month) {
            $salesData[] = $transactions->where('month', $month)->first()->total_sales ?? 0;
        }

        $monthNames = array_map(function ($month) {
            return \Carbon\Carbon::create(now()->year, $month, 1)->format('M');
        }, $months);

        return response()->json(['salesData' => $salesData, 'months' => $monthNames]);
    }

    public function getTransactionStatusData()
    {
        $currentMonth = now()->month;
        $months = range(1, $currentMonth);

        $data = [];


        $transactions = Transaction::select('payment_status', DB::raw('max(transaction_date) as transaction_date'), DB::raw('count(*) as total'))
            ->whereMonth('transaction_date', $months[8])
            ->groupBy('payment_status')
            ->get();

        foreach ($months as $month) {
            $transactions = Transaction::select('payment_status', DB::raw('max(transaction_date) as transaction_date'), DB::raw('count(*) as total'))
                ->whereMonth('transaction_date', $month)
                ->groupBy('payment_status')
                ->get();
            // dd($transactions);

            $statusData = [
                'Pending' => 0,
                'Success' => 0,
                'Incomplete' => 0
            ];

            foreach ($transactions as $transaction) {
                if ($transaction->payment_status == 'pending') {
                    $statusData['Pending'] = $transaction->total;
                } elseif ($transaction->payment_status == 'success') {
                    $statusData['Success'] = $transaction->total;
                } elseif ($transaction->payment_status == 'inc') {
                    $statusData['Incomplete'] = $transaction->total;
                }
            }

            $monthName = \Carbon\Carbon::create(now()->year, $month, 1)->format('M');
            $data[$monthName] = $statusData;
        }

        return response()->json(['data' => $data]);
    }
}
