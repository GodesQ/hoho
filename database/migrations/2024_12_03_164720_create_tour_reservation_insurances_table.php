<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTourReservationInsurancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tour_reservation_insurances', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->integer('reservation_id');
            $table->integer('insurance_id');
            $table->integer('type_of_plan');
            $table->double('total_insurance_amount', 10, 2);
            $table->integer('number_of_pax');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tour_reservation_insurances');
    }
}
