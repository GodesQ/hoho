<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Models\Transaction;
use App\Models\TourReservation;

use Yajra\DataTables\DataTables;

class TransactionController extends Controller
{
    public function list(Request $request)
    {
        if ($request->ajax()) {
            $data = Transaction::latest();
            if ($request->ajax()) {
                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('user', function ($row) {
                        if ($row->user) {
                            return $row->user->firstname.' '.$row->user->lastname;
                        } else {
                            return 'Deleted User';
                        }
                    })
                    ->addColumn('payment_amount', function ($row) {
                        return '₱ '.number_format($row->payment_amount, 2);
                    })
                    ->editColumn('transaction_type', function ($row) {
                        return str_replace('_', ' ', $row->transaction_type);
                    })
                    ->addColumn('status', function ($row) {
                        if ($row->payment_status == 'success') {
                            return '<span class="badge bg-label-success me-1">Success</span>';
                        } else if ($row->payment_status == 'cancelled') {
                            return '<span class="badge bg-label-danger me-1">Cancelled</span>';
                        } else if ($row->payment_status == 'failed') {
                            return '<span class="badge bg-label-danger me-1">Failed</span>';
                        } else if ($row->payment_status == 'pending') {
                            return '<span class="badge bg-label-warning me-1">Pending</span>';
                        } else if ($row->payment_status == 'inc') {
                            return '<span class="badge bg-label-warning me-1">Inc</span>';
                        }
                    })
                    ->editColumn('transaction_date', function ($row) {
                        return Carbon::parse($row->transaction_date)->format('M-d-Y');
                    })
                    ->addColumn('actions', function ($row) {
                        return '<div class="dropdown">
                                        <a href="/admin/transactions/edit/'.$row->id.'" class="btn btn-outline-primary btn-sm"><i class="bx bx-file me-1"></i></a>
                                    </div>';
                    })
                    ->filter(function ($query) use ($request) {
                        $search = $request->search;
                        $transaction_type = $request->transaction_type;
                        $status = $request->status;

                        if ($search) {
                            $query->where('reference_no', 'LIKE', "%{$search}%");
                        }

                        if ($transaction_type) {
                            $query->where("transaction_type", $transaction_type);
                        }

                        if ($status) {
                            $query->where("payment_status", $status);
                        }

                    })
                    ->rawColumns(['actions', 'status'])
                    ->make(true);
            }
        }

        return view('admin-page.transactions.list-transaction');
    }

    public function edit(Request $request)
    {
        $transaction = Transaction::where('id', $request->id)->with('user', 'items')->firstOrFail();
        // dd($transaction);
        return view('admin-page.transactions.edit-transaction', compact('transaction'));
    }

    public function update(Request $request)
    {
        $transaction = Transaction::findOrFail($request->id);
        $transaction->update($request->all());

        return back()->withSuccess('Transaction Details Updated Successfully');
    }

    public function print(Request $request)
    {
        $transaction = Transaction::where('id', $request->id)->firstOrFail();
        return view('admin-page.transactions.print-transaction', compact('transaction'));
    }


}
