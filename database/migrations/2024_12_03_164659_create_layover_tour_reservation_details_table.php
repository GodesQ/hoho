<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLayoverTourReservationDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('layover_tour_reservation_details', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->unsignedInteger('reservation_id');
            $table->dateTime('arrival_datetime');
            $table->string('flight_to', 255);
            $table->dateTime('departure_datetime');
            $table->string('flight_from', 255);
            $table->string('passport_number', 150);
            $table->text('special_instruction')->nullable();
            $table->timestamps();
            
            $table->foreign('reservation_id', 'layover_tour_reservation_id_foreign')->references('id')->on('tour_reservations')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('layover_tour_reservation_details');
    }
}
