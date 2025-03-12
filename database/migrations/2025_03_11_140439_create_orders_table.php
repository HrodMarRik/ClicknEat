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
        // La table orders est déjà créée par 2023_01_01_000005_create_orders_table.php
        if (!Schema::hasTable('orders')) {
            Schema::create('orders', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained();
                $table->foreignId('restaurant_id')->constrained();
                $table->enum('status', ['pending', 'preparing', 'ready', 'delivering', 'delivered', 'cancelled'])->default('pending');
                $table->decimal('subtotal', 10, 2);
                $table->decimal('delivery_fee', 8, 2)->default(0);
                $table->decimal('total', 10, 2);
                $table->string('address');
                $table->string('city');
                $table->string('postal_code');
                $table->string('phone');
                $table->text('notes')->nullable();
                $table->string('payment_intent_id')->nullable();
                $table->enum('payment_status', ['pending', 'paid', 'failed'])->default('pending');
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
