<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMerchantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('merchants', function (Blueprint $table) {
            $table->increments('id');
            $table->mediumText('name')->nullable();
            $table->mediumText('type')->nullable();
            $table->mediumText('featured_image')->nullable();
            $table->string('main_featured_image', 255)->nullable();
            $table->mediumText('nature_of_business')->nullable();
            $table->integer('organization_id')->nullable();
            $table->mediumText('code')->nullable();
            $table->longText('description')->nullable();
            $table->string('address', 255)->nullable();
            $table->string('latitude', 255)->nullable();
            $table->string('longitude', 255)->nullable();
            $table->boolean('is_active')->default(1);
            $table->boolean('is_featured')->default(0);
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('merchants');
    }
}
