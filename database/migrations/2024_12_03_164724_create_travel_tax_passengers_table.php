<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTravelTaxPassengersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('travel_tax_passengers', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('payment_id');
            $table->string('firstname', 100);
            $table->string('lastname', 100);
            $table->string('middlename', 100)->nullable();
            $table->string('suffix', 40)->nullable();
            $table->string('passport_number', 150);
            $table->string('ticket_number', 150);
            $table->string('class', 150);
            $table->string('mobile_number', 20);
            $table->string('email_address', 100);
            $table->string('destination', 100);
            $table->date('departure_date');
            $table->string('passenger_type', 100);
            $table->double('amount', 10, 2);
            $table->timestamps();
            
            $table->foreign('payment_id', 'travel_tax_passengers_payment_id_foreign')->references('id')->on('travel_tax_payments')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('travel_tax_passengers');
    }
}
