<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use HasFactory;
    protected $table = "orders";
    protected $fillable = ["product_id", "customer_id", "transaction_id", "reference_code", "quantity", "sub_amount", "total_amount", "payment_method", "status", "order_date"];
    protected $hidden = ['created_at', 'updated_at'];

    protected $dates = ['order_date'];

    protected $casts = [
        'product_id' => 'integer', 
        'customer_id' => 'integer', 
        'quantity' => 'integer',
        'transaction_id' => 'integer',
        'sub_amount' => 'double',
        'total_amount' => 'double',
    ];

    public function product() : BelongsTo {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function customer() : BelongsTo {
        return $this->belongsTo(User::class, 'customer_id');
    }
}
