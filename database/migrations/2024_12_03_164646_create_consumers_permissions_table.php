<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConsumersPermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('consumers_permissions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('consumer_id');
            $table->unsignedInteger('permission_id')->nullable();
            $table->timestamps();
            
            $table->foreign('consumer_id', 'consumers_permissions_consumer_id_foreign')->references('id')->on('api_consumers')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('permission_id', 'consumers_permissions_permission_id_foreign')->references('id')->on('api_permissions')->onDelete('set NULL')->onUpdate('set NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('consumers_permissions');
    }
}
