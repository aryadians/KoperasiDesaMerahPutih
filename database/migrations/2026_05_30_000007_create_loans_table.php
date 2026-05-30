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
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->onDelete('restrict');
            $table->string('loan_code')->unique();
            $table->decimal('amount_requested', 15, 2);
            $table->decimal('amount_approved', 15, 2)->default(0.00);
            $table->decimal('interest_rate', 5, 2); // e.g. 5.50%
            $table->integer('tenor_months');
            $table->enum('status', ['draft', 'approved', 'rejected', 'active', 'paid_off'])->default('draft');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
