<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTourReservationCustomerDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tour_reservation_customer_details', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('tour_reservation_id');
            $table->string('firstname', 50)->nullable();
            $table->string('lastname', 50)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('contact_no', 20)->nullable();
            $table->text('address')->nullable();
            $table->timestamps();

            $table->foreign('tour_reservation_id', 'customer_details_tour_reservation_id_foreign')->references('id')->on('tour_reservations')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tour_reservation_customer_details');
    }
}
