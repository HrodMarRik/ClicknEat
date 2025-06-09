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
        Schema::table('users', function (Blueprint $table) {
            // Champs pour le rôle et l'administration
            $table->boolean('is_admin')->default(false);
            $table->enum('role', ['admin', 'restaurateur', 'client'])->default('client');

            // Champs pour l'adresse
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('phone')->nullable();

            // Statut du compte
            $table->boolean('is_active')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'is_admin',
                'role',
                'address',
                'city',
                'postal_code',
                'phone',
                'is_active'
            ]);
        });
    }
};
