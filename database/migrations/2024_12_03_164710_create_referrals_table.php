<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReferralsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('referrals', function (Blueprint $table) {
            $table->increments('id');
            $table->string('referral_name', 100);
            $table->string('referral_code', 100);
            $table->unsignedInteger('merchant_id')->nullable();
            $table->longText('qrcode')->nullable();
            $table->integer('commision')->default(0);
            $table->timestamps();

            $table->foreign('merchant_id', 'referrals_merchant_id_foreign')->references('id')->on('merchants')->onDelete('set NULL')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('referrals');
    }
}
