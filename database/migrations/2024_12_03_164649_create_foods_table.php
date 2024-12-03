<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFoodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('foods', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->unsignedInteger('merchant_id');
            $table->string('title', 255);
            $table->text('image')->nullable();
            $table->longText('description')->nullable();
            $table->double('price', 10, 2)->default(0.00);
            $table->unsignedInteger('food_category_id')->nullable();
            $table->string('note', 100)->nullable();
            $table->json('other_images')->nullable();
            $table->boolean('is_active')->default(1);
            $table->timestamps();
            
            $table->foreign('food_category_id', 'foods_food_category_id_foreign')->references('id')->on('food_categories')->onDelete('set NULL')->onUpdate('cascade');
            $table->foreign('merchant_id', 'foods_merchant_id_foreign')->references('id')->on('merchants')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('foods');
    }
}
