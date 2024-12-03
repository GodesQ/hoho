<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateToursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tours', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('capacity')->nullable();
            $table->integer('under_age_limit')->nullable();
            $table->integer('over_age_limit')->nullable();
            $table->integer('tour_provider_id')->nullable();
            $table->mediumText('package_tour')->nullable();
            $table->mediumText('name');
            $table->mediumText('type')->nullable();
            $table->longText('description')->nullable();
            $table->string('contact_no', 100)->nullable();
            $table->mediumText('featured_image')->nullable();
            $table->longText('images')->nullable();
            $table->longText('interests')->nullable();
            $table->longText('operating_hours')->nullable();
            $table->boolean('is_cancellable')->default(0);
            $table->boolean('is_refundable')->default(0);
            $table->string('status', 100)->nullable();
            $table->mediumText('links')->nullable();
            $table->integer('bypass_days')->nullable();
            $table->string('disabled_days', 255)->nullable();
            $table->integer('minimum_pax')->nullable();
            $table->integer('minimum_capacity')->nullable();
            $table->longText('tour_itinerary')->nullable();
            $table->longText('tour_inclusions')->nullable();
            $table->double('price', 10, 2)->default(0.00);
            $table->double('bracket_price_one', 10, 2)->default(0.00);
            $table->double('bracket_price_two', 10, 2)->default(0.00);
            $table->double('bracket_price_three', 10, 2)->default(0.00);
            $table->mediumText('attractions_assignments_ids')->nullable();
            $table->date('start_date_duration')->nullable();
            $table->date('end_date_duration')->nullable();
            $table->integer('tour_duration')->nullable();
            $table->integer('transport_id')->nullable();
            $table->unsignedInteger('organization_id')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            
            $table->foreign('organization_id', 'tours_organization_id_foreign')->references('id')->on('organizations')->onDelete('set NULL')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tours');
    }
}
