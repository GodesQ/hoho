<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('account_uid', 255);
            $table->string('username', 255);
            $table->string('email', 255);
            $table->string('password', 255);
            $table->string('user_profile', 255)->nullable();
            $table->string('firstname', 50)->nullable();
            $table->string('middlename', 50)->nullable();
            $table->string('lastname', 50)->nullable();
            $table->integer('age')->nullable();
            $table->date('birthdate')->nullable();
            $table->string('gender', 50)->nullable();
            $table->string('contact_no', 50)->nullable();
            $table->mediumText('interest_ids')->nullable();
            $table->string('status', 255)->default('active');
            $table->boolean('is_old_user')->default(0);
            $table->boolean('is_verify')->default(0);
            $table->string('countryCode', 100)->nullable();
            $table->string('isoCode', 100)->nullable();
            $table->string('country_of_residence', 255)->nullable();
            $table->string('is_first_time_philippines', 255)->nullable();
            $table->string('is_international_tourist', 255)->nullable();
            $table->string('role', 50)->default('guest');
            $table->string('login_with', 100)->default('hoho');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
