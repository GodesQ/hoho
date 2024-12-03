<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMerchantsStoreTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('merchants_store', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->unsignedInteger('merchant_id')->nullable();
            $table->string('brochure', 255)->nullable();
            $table->mediumText('images')->nullable();
            $table->mediumText('payment_options')->nullable();
            $table->longText('tags')->nullable();
            $table->mediumText('links')->nullable();
            $table->mediumText('interests')->nullable();
            $table->mediumText('contact_number')->nullable();
            $table->string('contact_email', 255)->nullable();
            $table->mediumText('location')->nullable();
            $table->longText('business_hours')->nullable();
            $table->longText('address')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent();
            
            $table->foreign('merchant_id', 'merchant_stores_merchant_id_foreign')->references('id')->on('merchants')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('merchants_store');
    }
}
