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
        Schema::create('crop_absorptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->onDelete('restrict');
            $table->string('product_name');
            $table->decimal('quantity', 10, 2); // e.g. in kg
            $table->decimal('price_per_unit', 15, 2);
            $table->decimal('total_payout', 15, 2);
            $table->enum('status', ['pending', 'received', 'paid'])->default('pending');
            $table->dateTime('absorption_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crop_absorptions');
    }
};
