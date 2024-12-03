<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTourReservationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tour_reservations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('tour_id')->nullable();
            $table->string('type', 100);
            $table->string('total_additional_charges', 255)->default('0');
            $table->string('discount', 255)->default('0');
            $table->string('sub_amount', 255)->default('0');
            $table->integer('amount');
            $table->integer('reserved_user_id')->nullable();
            $table->string('passenger_ids', 255)->nullable();
            $table->string('reference_code', 255);
            $table->unsignedInteger('order_transaction_id');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('status', 100)->default('pending');
            $table->integer('number_of_pass');
            $table->string('ticket_pass', 255)->nullable();
            $table->string('payment_method', 255)->nullable();
            $table->integer('referral_merchant_id')->nullable();
            $table->string('referral_code', 100)->nullable();
            $table->string('promo_code', 255)->nullable();
            $table->string('requirement_file_path', 255)->nullable();
            $table->string('discount_amount', 255)->nullable();
            $table->boolean('has_insurance')->default(0);
            $table->integer('created_by')->nullable();
            $table->string('created_user_type', 50)->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('order_transaction_id', 'tour_reservations_transaction_id_foreign')->references('id')->on('transactions')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tour_reservations');
    }
}
