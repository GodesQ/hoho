<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApiConsumersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('api_consumers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('consumer_name', 100);
            $table->string('api_code', 100);
            $table->string('api_key', 200);
            $table->string('contact_email', 50)->nullable();
            $table->string('contact_phone', 20)->nullable();
            $table->string('platform', 50);
            $table->boolean('status')->default(1);
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
        Schema::dropIfExists('api_consumers');
    }
}
