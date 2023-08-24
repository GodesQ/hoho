<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Transaction;
use App\Models\TourReservation;

use DataTables;

class TransactionController extends Controller
{
    public function list(Request $request) {
        if($request->ajax()) {
            $data = Transaction::latest();
            if($request->ajax()) {
                return DataTables::of($data)
                        ->addIndexColumn()
                        ->addColumn('payment_amount', function($row) {
                            return '₱ ' . number_format($row->payment_amount, 2);
                        })
                        ->addColumn('status', function ($row) {
                            if($row->payment_status == 'success') {
                                return '<span class="badge bg-label-success me-1">Success</span>';
                            } else if($row->payment_status == 'cancelled') {
                                return '<span class="badge bg-label-danger me-1">Cancelled</span>';
                            } else if($row->payment_status == 'failed') {
                                return '<span class="badge bg-label-danger me-1">Failed</span>';
                            } else if($row->payment_status == 'pending') {
                                return '<span class="badge bg-label-warning me-1">Pending</span>';
                            }
                        })
                        ->addColumn('actions', function ($row) {
                            return '<div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="/admin/transactions/edit/' .$row->id. '">
                                                <i class="bx bx-edit-alt me-1"></i> Edit
                                            </a>
                                            <a class="dropdown-item remove-btn" href="javascript:void(0);" id="' .$row->id. '">
                                                <i class="bx bx-trash me-1"></i> Delete
                                            </a>
                                        </div>
                                    </div>';
                        })
                        ->rawColumns(['actions', 'status'])
                        ->make(true);
            }
        }

        return view('admin-page.transactions.list-transaction');
    }


}
