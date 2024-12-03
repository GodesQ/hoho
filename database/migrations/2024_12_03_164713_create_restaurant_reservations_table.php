<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRestaurantReservationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('restaurant_reservations', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->integer('reserved_user_id');
            $table->integer('merchant_id');
            $table->integer('seats')->default(2);
            $table->date('reservation_date');
            $table->time('reservation_time');
            $table->string('food_ids', 255)->nullable();
            $table->string('status', 100);
            $table->date('approved_date')->nullable();
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
        Schema::dropIfExists('restaurant_reservations');
    }
}
