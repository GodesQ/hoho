<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrganizationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 200);
            $table->string('acronym', 50)->nullable();
            $table->string('region', 255)->nullable();
            $table->mediumText('icon')->nullable();
            $table->mediumText('featured_image')->nullable();
            $table->mediumText('images')->nullable();
            $table->longText('description')->nullable();
            $table->mediumText('visibility')->nullable();
            $table->boolean('is_active')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('organizations');
    }
}
