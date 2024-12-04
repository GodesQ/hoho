<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHotelRoomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hotel_rooms', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('merchant_hotel_id');
            $table->mediumText('room_name')->nullable();
            $table->mediumText('room_type')->nullable();
            $table->mediumText('description')->nullable();
            $table->mediumText('location_of_room')->nullable();
            $table->mediumText('room_rate_per_night')->nullable();
            $table->mediumText('is_cancellable')->nullable();
            $table->mediumText('is_refundable')->nullable();
            $table->mediumText('thumbnail_image')->nullable();
            $table->mediumText('product_categories')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hotel_rooms');
    }
}
