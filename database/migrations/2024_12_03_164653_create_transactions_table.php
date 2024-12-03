<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('reference_no', 100);
            $table->string('or_no', 100)->nullable();
            $table->integer('transaction_by_id')->nullable();
            $table->string('sub_amount', 255)->nullable();
            $table->double('total_additional_charges', 10, 2)->default(0.00);
            $table->double('total_discount', 10, 2)->default(0.00);
            $table->string('transaction_type', 100)->nullable();
            $table->decimal('payment_amount', 10, 0);
            $table->double('total_amount', 10, 2)->default(0.00);
            $table->string('type', 100)->nullable();
            $table->mediumText('additional_charges')->nullable();
            $table->string('payment_status', 50)->default('pending');
            $table->string('resolution_status', 50)->default('pending');
            $table->longText('payment_details')->nullable();
            $table->mediumText('payment_url')->nullable();
            $table->date('order_date')->nullable();
            $table->date('transaction_date')->nullable();
            $table->date('payment_date')->nullable();
            $table->mediumText('remarks')->nullable();
            $table->double('payment_provider_fee', 10, 2)->default(0.00);
            $table->string('aqwire_transactionId', 255)->nullable();
            $table->string('aqwire_referenceId', 255)->nullable();
            $table->string('aqwire_paymentMethodCode', 255)->nullable();
            $table->string('aqwire_totalAmount', 255)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
