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
        Schema::table('products', function (Blueprint $table) {
            $table->index('is_local_product');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->index('payment_status');
        });

        Schema::table('loans', function (Blueprint $table) {
            $table->index('status');
        });

        Schema::table('crop_absorptions', function (Blueprint $table) {
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['is_local_product']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['payment_status']);
        });

        Schema::table('loans', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });

        Schema::table('crop_absorptions', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });
    }
};
