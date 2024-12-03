<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transports', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 255);
            $table->string('route', 255)->nullable();
            $table->integer('available_seats')->default(35);
            $table->string('capacity', 255)->nullable();
            $table->string('duration', 255)->nullable();
            $table->integer('transport_provider_id')->nullable();
            $table->integer('operator_id')->nullable();
            $table->json('tour_assignment_ids')->nullable();
            $table->unsignedInteger('tour_assigned_id')->nullable();
            $table->unsignedInteger('hub_id')->nullable();
            $table->string('latitude', 255)->nullable();
            $table->string('longitude', 255)->nullable();
            $table->string('type', 255)->nullable();
            $table->longText('description')->nullable();
            $table->string('contact_email', 255)->nullable();
            $table->longText('operating_hours')->nullable();
            $table->mediumText('travel_cards')->nullable();
            $table->string('price', 100)->nullable();
            $table->timestamp('arrival_date')->nullable();
            $table->timestamp('departure_date')->nullable();
            $table->string('icon', 255)->nullable();
            $table->mediumText('current_location')->nullable();
            $table->mediumText('next_location')->nullable();
            $table->mediumText('previous_location')->nullable();
            $table->boolean('is_cancellable')->default(0);
            $table->boolean('is_refundable')->default(0);
            $table->boolean('is_active')->default(1);
            $table->boolean('is_tracking')->default(0);
            $table->string('current_tracking_token', 255)->nullable();
            $table->timestamps();
            
            $table->foreign('hub_id', 'transport_hub_id_foreign')->references('id')->on('organizations')->onDelete('set NULL')->onUpdate('cascade');
            $table->foreign('tour_assigned_id', 'transport_tour_id_foreign')->references('id')->on('tours')->onDelete('set NULL')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transports');
    }
}
