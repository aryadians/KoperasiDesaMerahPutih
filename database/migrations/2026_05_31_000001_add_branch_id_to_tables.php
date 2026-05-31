<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Seed default branches
        DB::table('branches')->insert([
            [
                'id' => 1,
                'name' => 'Desa Merah Putih',
                'code' => 'DMP',
                'address' => 'Desa Merah Putih, Kecamatan Makmur',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'Desa Gotong Royong',
                'code' => 'DGR',
                'address' => 'Desa Gotong Royong, Kecamatan Sejahtera',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        // 2. Add branch_id to users, products, orders, crop_absorptions, loans
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('branch_id')->default(1)->after('id')->constrained('branches')->onDelete('cascade');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('branch_id')->default(1)->after('id')->constrained('branches')->onDelete('cascade');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('branch_id')->default(1)->after('id')->constrained('branches')->onDelete('cascade');
        });

        Schema::table('crop_absorptions', function (Blueprint $table) {
            $table->foreignId('branch_id')->default(1)->after('id')->constrained('branches')->onDelete('cascade');
        });

        Schema::table('loans', function (Blueprint $table) {
            $table->foreignId('branch_id')->default(1)->after('id')->constrained('branches')->onDelete('cascade');
        });

        // 3. Copy products of DMP to DGR (for catalog to have items in DGR)
        $existingProducts = DB::table('products')->where('branch_id', 1)->get();
        foreach ($existingProducts as $product) {
            DB::table('products')->insert([
                'branch_id' => 2,
                'category_id' => $product->category_id,
                'barcode' => $product->barcode ? $product->barcode . '-DGR' : null,
                'name' => $product->name,
                'description' => $product->description,
                'price_member' => $product->price_member,
                'price_non_member' => $product->price_non_member,
                'current_stock' => $product->current_stock,
                'unit' => $product->unit,
                'is_local_product' => $product->is_local_product,
                'image_url' => $product->image_url,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove columns
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            $table->dropColumn('branch_id');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            $table->dropColumn('branch_id');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            $table->dropColumn('branch_id');
        });

        Schema::table('crop_absorptions', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            $table->dropColumn('branch_id');
        });

        Schema::table('loans', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            $table->dropColumn('branch_id');
        });
    }
};
