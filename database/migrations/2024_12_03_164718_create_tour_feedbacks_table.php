<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTourFeedbacksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tour_feedbacks', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('customer_id');
            $table->unsignedInteger('reservation_id');
            $table->unsignedInteger('tour_id');
            $table->text('message')->nullable();
            $table->integer('category_one_rate');
            $table->integer('category_two_rate');
            $table->integer('category_three_rate');
            $table->integer('category_four_rate');
            $table->integer('category_five_rate');
            $table->integer('category_six_rate');
            $table->integer('total_rate');
            $table->timestamps();

            $table->foreign('reservation_id', 'tour_feed_backs_reservation_id_foreign')->references('id')->on('tour_reservations')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('customer_id', 'tour_feedbacks_customer_id_foreign')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('tour_id', 'tour_feedbacks_tour_id_foreign')->references('id')->on('tours')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tour_feedbacks');
    }
}
