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
            if (!Schema::hasColumn('restaurants', 'email')) {
                $table->string('email')->after('name')->nullable();
            }
            if (!Schema::hasColumn('restaurants', 'delivery_fee')) {
                $table->decimal('delivery_fee', 8, 2)->default(0)->after('phone');
            }
            if (!Schema::hasColumn('restaurants', 'minimum_order')) {
                $table->decimal('minimum_order', 8, 2)->default(0)->after('delivery_fee');
            }
            if (!Schema::hasColumn('restaurants', 'delivery_time')) {
                $table->integer('delivery_time')->default(30)->after('minimum_order');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn(['email', 'delivery_fee', 'minimum_order', 'delivery_time']);
        });
    }
};