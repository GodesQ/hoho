<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTourBadgesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tour_badges', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('tour_id');
            $table->string('badge_name', 255);
            $table->string('badge_code', 255);
            $table->string('badge_img', 255)->nullable();
            $table->text('location');
            $table->string('latitude', 255);
            $table->string('longitude', 255);
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
        Schema::dropIfExists('tour_badges');
    }
}
