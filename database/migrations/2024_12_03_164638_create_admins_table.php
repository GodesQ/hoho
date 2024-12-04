<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->increments('id');
            $table->string('username', 255);
            $table->string('email', 255);
            $table->string('admin_profile', 255)->nullable();
            $table->string('password', 255);
            $table->string('firstname', 50)->nullable();
            $table->string('middlename', 50)->nullable();
            $table->string('lastname', 50)->nullable();
            $table->integer('age')->nullable();
            $table->date('birthdate')->nullable();
            $table->string('contact_no', 50)->nullable();
            $table->string('address', 200)->nullable();
            $table->string('role', 100)->default('guest');
            $table->boolean('is_active')->default(1);
            $table->boolean('is_merchant')->default(0);
            $table->boolean('is_approved')->default(0);
            $table->date('merchant_email_approved_at')->nullable();
            $table->integer('merchant_data_id')->nullable();
            $table->unsignedInteger('merchant_id')->nullable();
            $table->unsignedInteger('transport_id')->nullable();
            $table->timestamps();

            $table->foreign('merchant_id', 'admins_merchant_id_foreign')->references('id')->on('merchants')->onDelete('set NULL')->onUpdate('cascade');
            $table->foreign('transport_id', 'admins_transport_id_foreign')->references('id')->on('transports')->onDelete('set NULL')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admins');
    }
}
