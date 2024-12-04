<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('product_id');
            $table->unsignedInteger('customer_id');
            $table->unsignedInteger('transaction_id');
            $table->string('reference_code', 100);
            $table->integer('quantity');
            $table->double('sub_amount', 10, 2)->default(0.00);
            $table->double('total_amount', 10, 2)->default(0.00);
            $table->string('payment_method', 255)->nullable();
            $table->string('status', 100);
            $table->date('order_date');
            $table->timestamps();

            $table->foreign('customer_id', 'orders_buyer_id_foreign')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('product_id', 'orders_product_id_foreign')->references('id')->on('products')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('transaction_id', 'orders_transaction_id_foreign')->references('id')->on('transactions')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
