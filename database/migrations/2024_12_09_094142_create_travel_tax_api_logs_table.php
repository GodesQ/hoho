<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('travel_tax_api_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('travel_tax_id');
            $table->string('status_code');
            $table->json('response');
            $table->dateTime('date_of_submission');
            $table->timestamps();
            $table->foreign('travel_tax_id', 'travel_tax_passengers_travel_tax_id_foreign')->references('id')->on('travel_tax_payments')->onDelete('cascade')->onUpdate('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('travel_tax_api_logs');
    }
};
