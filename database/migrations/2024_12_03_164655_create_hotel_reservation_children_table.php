<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHotelReservationChildrenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hotel_reservation_children', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->integer('reservation_id');
            $table->integer('age');
            $table->timestamps();
            
            $table->foreign('reservation_id', 'hotel_reservations_reservation_id_foreign')->references('id')->on('hotel_reservations')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hotel_reservation_children');
    }
}
