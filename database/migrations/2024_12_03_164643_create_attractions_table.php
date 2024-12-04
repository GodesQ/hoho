<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttractionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attractions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 255);
            $table->integer('attraction_provider')->nullable();
            $table->longText('featured_image')->nullable();
            $table->unsignedInteger('organization_id')->nullable();
            $table->longText('images')->nullable();
            $table->string('contact_no', 50)->nullable();
            $table->longText('description')->nullable();
            $table->mediumText('interest_ids')->nullable();
            $table->string('youtube_id', 255)->nullable();
            $table->mediumText('product_category_ids')->nullable();
            $table->string('price', 100)->nullable();
            $table->longText('operating_hours')->nullable();
            $table->string('address', 255)->nullable();
            $table->string('latitude', 255)->nullable();
            $table->string('longitude', 255)->nullable();
            $table->integer('is_cancellable')->default(0);
            $table->integer('is_refundable')->default(0);
            $table->boolean('is_featured')->default(0);
            $table->integer('featured_arrangement_number')->nullable();
            $table->integer('status')->default(1);
            $table->mediumText('nearest_attraction_ids')->nullable();
            $table->mediumText('nearest_hotel_ids')->nullable();
            $table->mediumText('nearest_store_ids')->nullable();
            $table->mediumText('nearest_restaurant_ids')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();

            $table->foreign('organization_id', 'attractions_organization_id_foreign')->references('id')->on('organizations')->onDelete('set NULL')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attractions');
    }
}
