<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReservationCodeScanLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reservation_code_scan_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('reservation_code_id');
            $table->dateTime('scan_datetime');
            $table->string('scan_type', 100);
            $table->integer('hub_type_id')->nullable();
            $table->integer('attraction_id')->nullable();
            $table->timestamps();

            $table->foreign('reservation_code_id', 'reservation_code_scanlogs_reservation_code_id_foreign')->references('id')->on('reservation_user_codes')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reservation_code_scan_logs');
    }
}
