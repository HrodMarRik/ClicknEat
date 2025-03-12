<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Cette migration est désactivée pour éviter les conflits
        // La table order_items est déjà créée par 2023_01_01_000006_create_order_items_table.php
        if (!Schema::hasTable('order_items')) {
            Schema::create('order_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->constrained()->onDelete('cascade');
                $table->foreignId('dish_id')->constrained()->onDelete('cascade');
                $table->integer('quantity');
                $table->decimal('price', 8, 2);
                $table->text('special_instructions')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Ne rien faire pour éviter de supprimer la table si elle a été créée par une autre migration
    }
};
