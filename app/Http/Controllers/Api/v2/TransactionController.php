<?php

namespace App\Http\Controllers\Api\v2;

use App\Enum\TransactionTypeEnum;
use App\Http\Controllers\Controller;
use App\Models\TourReservation;
use App\Models\Transaction;
use Exception;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function getTransaction(Request $request, $id) {
        try {
            $transaction = Transaction::where("id", $id)->first();

            $total_items = 1;

            if (!$transaction) throw new Exception("Transaction Not Found.", 404);

            if ($transaction->type === TransactionTypeEnum::BOOK_TOUR) {
                $total_items = TourReservation::where("order_transaction_id", $transaction->id)->count();
            }

            return response()->json([
                "status" => true,
                "transaction" => $transaction,
                'total_items' => $total_items
            ]);

        } catch (Exception $exception) {
            $exception_code = $exception->getCode() == 0 ? 500 : $exception->getCode();
            
            return response()->json([
                "status" => false,
                "message"=> $exception->getMessage(),
            ], $exception_code);
        }

    }
}
