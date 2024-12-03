<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('merchant_id');
            $table->string('name', 255);
            $table->string('image', 255)->nullable();
            $table->longText('description')->nullable();
            $table->double('price', 10, 2)->default(0.00);
            $table->integer('stock')->default(1);
            $table->longText('other_images')->nullable();
            $table->boolean('is_best_seller')->default(0);
            $table->boolean('is_active')->default(1);
            $table->timestamps();
            
            $table->foreign('merchant_id', 'products_merchant_id_foreign')->references('id')->on('merchants')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
