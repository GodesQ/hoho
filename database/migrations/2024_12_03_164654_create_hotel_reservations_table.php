<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHotelReservationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hotel_reservations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('reserved_user_id');
            $table->string('reference_number', 100)->nullable();
            $table->unsignedInteger('transaction_id')->nullable();
            $table->unsignedInteger('room_id');
            $table->integer('number_of_pax')->default(1);
            $table->date('checkin_date');
            $table->date('checkout_date');
            $table->integer('adult_quantity');
            $table->integer('children_quantity');
            $table->string('status', 100)->default('pending');
            $table->date('approved_date')->nullable();
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();

            $table->foreign('reserved_user_id', 'hotel_reservations_reserved_user_id_foreign')->references('id')->on('users')->onUpdate('cascade');
            $table->foreign('room_id', 'hotel_reservations_room_id_foreign')->references('id')->on('rooms')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('transaction_id', 'hotel_reservations_transaction_id_foreign')->references('id')->on('transactions')->onDelete('set NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hotel_reservations');
    }
}
