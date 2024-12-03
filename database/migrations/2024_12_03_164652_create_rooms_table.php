<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('merchant_id');
            $table->string('room_name', 200);
            $table->string('image', 255)->nullable();
            $table->double('price', 10, 2)->default(0.00);
            $table->integer('available_pax')->default(1);
            $table->integer('number_of_rooms')->default(0);
            $table->longText('amenities')->nullable();
            $table->longText('description')->nullable();
            $table->longText('other_images')->nullable();
            $table->string('product_categories', 100)->nullable();
            $table->boolean('is_cancellable')->default(0);
            $table->boolean('is_refundable')->default(0);
            $table->boolean('is_active')->default(1);
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
        Schema::dropIfExists('rooms');
    }
}
