<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReservationUserCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reservation_user_codes', function (Blueprint $table) {
            $table->id();
            $table->integer('reservation_id');
            $table->string('code', 100);
            $table->integer('scan_count')->default(0);
            $table->timestamp('start_datetime')->nullable();
            $table->timestamp('end_datetime')->nullable();
            $table->string('status', 100)->nullable();
            $table->integer('current_hub')->nullable();
            $table->integer('current_attraction')->nullable();
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
        Schema::dropIfExists('reservation_user_codes');
    }
}
