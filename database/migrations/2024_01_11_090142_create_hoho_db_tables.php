<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->string('username');
            $table->string('email');
            $table->string('admin_profile')->nullable();
            $table->string('password');
            $table->string('firstname')->nullable();
            $table->string('middlename')->nullable();
            $table->string('lastname')->nullable();
            $table->integer('age')->nullable();
            $table->date('birthdate')->nullable();
            $table->string('contact_no')->nullable();
            $table->string('address')->nullable();
            $table->string('role')->default('guest');
            $table->boolean('is_active')->default(1);
            $table->boolean('is_merchant')->default(0);
            $table->boolean('is_approved')->default(0);
            $table->date('merchant_email_approved_at')->nullable();
            $table->integer('merchant_data_id')->nullable();
            $table->unsignedInteger('merchant_id')->nullable()->index('admins_merchant_id_foreign');
            $table->foreign(['merchant_id'])->references(['id'])->on('merchant')->onUpdate('CASCADE')->onDelete('SET NULL');
            $table->timestamps();
            $table->primary('id');
        });

        Schema::create('announcements', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type');
            $table->string('name');
            $table->string('announcement_image')->nullable();
            $table->text('message');
            $table->boolean('is_active')->default(1);
            $table->boolean('is_important')->default(0);
            $table->timestamps();
            $table->primary('id');
        });

        Schema::create('attractions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('attraction_provider')->nullable();
            $table->longText('featured_image');
            $table->longText('images');
            $table->string('contact_no')->nullable();
            $table->longText('description');
            $table->text('interest_ids');
            $table->string('youtube_id')->nullable();
            $table->text('product_category_ids');
            $table->string('price')->nullable();
            $table->longText('operating_hours');
            $table->integer('organization_id')->nullable();
            $table->string('address')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->integer('is_cancellable')->default(0);
            $table->integer('is_refundable')->default(0);
            $table->boolean('is_featured')->default(0);
            $table->integer('status')->default(1);
            $table->text('nearest_attraction_ids');
            $table->text('nearest_hotel_ids');
            $table->text('nearest_store_ids');
            $table->text('nearest_restaurant_ids');
            $table->timestamps();
            $table->primary('id');
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
};
