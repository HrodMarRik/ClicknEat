<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('restaurants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description');
            $table->string('cuisine_type')->nullable();
            $table->string('address');
            $table->string('city');
            $table->string('postal_code');
            $table->string('phone');
            $table->string('email');
            $table->string('cuisine');
            $table->json('opening_hours')->nullable();
            $table->string('opening_time')->nullable();
            $table->string('closing_time')->nullable();
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(false);
            $table->decimal('min_order_amount', 8, 2)->default(0);
            $table->decimal('delivery_fee', 8, 2)->default(0);
            $table->boolean('accepts_onsite_orders')->default(true);
            $table->integer('preparation_time')->default(30);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('restaurants');
    }
};
