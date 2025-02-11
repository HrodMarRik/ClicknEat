<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsTable extends Migration
{
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->integer('cost')->nullable();
            $table->integer('price');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->foreignId('category_id')->constrained();
        });
    }

    public function down()
    {
        Schema::dropIfExists('items');
    }
}
