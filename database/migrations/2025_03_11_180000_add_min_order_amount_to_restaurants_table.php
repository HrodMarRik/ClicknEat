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
        Schema::table('restaurants', function (Blueprint $table) {
            $table->decimal('min_order_amount', 8, 2)->default(0);
            // Ajoutez d'autres colonnes manquantes si nécessaire
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn('min_order_amount');
            // Supprimez d'autres colonnes ajoutées si nécessaire
        });
    }
};
