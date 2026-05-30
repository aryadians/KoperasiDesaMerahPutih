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
        Schema::table('crop_absorptions', function (Blueprint $table) {
            $table->decimal('deducted_loan_payment', 15, 2)->default(0.00)->nullable();
            $table->decimal('net_payout', 15, 2)->default(0.00)->nullable();
            $table->text('notes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('crop_absorptions', function (Blueprint $table) {
            $table->dropColumn(['deducted_loan_payment', 'net_payout', 'notes']);
        });
    }
};
