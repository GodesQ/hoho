<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTravelTaxPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('travel_tax_payments', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('transaction_id');
            $table->string('ar_number', 150)->nullable();
            $table->unsignedInteger('user_id')->nullable();
            $table->string('transaction_number', 100);
            $table->string('reference_number', 100);
            $table->dateTime('transaction_time');
            $table->string('currency', 50)->default('PHP');
            $table->double('amount', 10, 2);
            $table->double('processing_fee', 10, 2);
            $table->double('discount', 10, 2);
            $table->double('total_amount', 10, 2);
            $table->string('payment_method', 100)->nullable();
            $table->dateTime('payment_time');
            $table->enum('status', ['unpaid', 'paid'])->default('unpaid');
            $table->string('created_by_role', 100)->nullable();
            $table->integer('created_by')->nullable();
            $table->timestamps();
            
            $table->foreign('transaction_id', 'travel_tax_payments_transaction_id_foreign')->references('id')->on('transactions')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('travel_tax_payments');
    }
}
