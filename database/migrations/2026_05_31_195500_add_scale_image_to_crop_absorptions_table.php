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
            $table->longText('scale_image')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('crop_absorptions', function (Blueprint $table) {
            $table->dropColumn('scale_image');
        });
    }
};
