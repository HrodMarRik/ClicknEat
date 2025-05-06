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
            $table->string('address');
            $table->string('city');
            $table->string('postal_code');
            $table->string('phone');
            $table->string('email');
            $table->string('cuisine');
            $table->decimal('delivery_fee', 8, 2)->default(0);
            $table->integer('delivery_time')->nullable();
            $table->json('opening_hours')->nullable();
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('restaurants');
    }
};
