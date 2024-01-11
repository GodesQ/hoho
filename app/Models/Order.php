<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $table = "orders";
    protected $fillable = ["product_id", "buyer_id", "transaction_id", "reference_code", "quantity", "sub_amount", "total_amount", "payment_method", "status", "order_date"];
}
