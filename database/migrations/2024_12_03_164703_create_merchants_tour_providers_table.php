<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMerchantsTourProvidersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('merchants_tour_providers', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->unsignedInteger('merchant_id')->nullable();
            $table->mediumText('images')->nullable();
            $table->mediumText('payment_options')->nullable();
            $table->longText('tags')->nullable();
            $table->mediumText('links')->nullable();
            $table->mediumText('interests')->nullable();
            $table->mediumText('contact_number')->nullable();
            $table->string('contact_email', 200)->nullable();
            $table->mediumText('location')->nullable();
            $table->longText('business_hours')->nullable();
            $table->longText('address')->nullable();
            $table->integer('account_id')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent();
            
            $table->foreign('merchant_id', 'merchant_tour_providers_merchant_id_foreign')->references('id')->on('merchants')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('merchants_tour_providers');
    }
}
