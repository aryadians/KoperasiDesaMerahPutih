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
        // 1. Announcements (Warta Desa)
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->string('image_url')->nullable();
            $table->foreignId('author_id')->constrained('users')->onDelete('cascade');
            $table->boolean('is_pinned')->default(false);
            $table->timestamps();
        });

        // 2. P2P Marketplace Products
        Schema::create('p2p_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description');
            $table->decimal('price', 15, 2);
            $table->string('image_url')->nullable();
            $table->enum('status', ['available', 'sold', 'hidden'])->default('available');
            $table->timestamps();
        });

        // 3. Member Lands (Agro-GIS)
        Schema::create('member_lands', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->onDelete('cascade');
            $table->string('location_name');
            $table->string('coordinates')->nullable(); // e.g. "lat,long"
            $table->decimal('area_m2', 12, 2);
            $table->string('commodity_type');
            $table->date('last_planting_date')->nullable();
            $table->timestamps();
        });

        // 4. Vouchers (Loyalty)
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->enum('discount_type', ['percentage', 'fixed']);
            $table->decimal('discount_value', 15, 2);
            $table->decimal('min_purchase', 15, 2)->default(0);
            $table->dateTime('expires_at');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 5. Update existing tables for Payments & Tiers
        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_gateway_ref')->nullable()->after('payment_method');
            $table->string('payment_url')->nullable()->after('payment_gateway_ref');
        });

        Schema::table('members', function (Blueprint $table) {
            $table->enum('tier', ['silver', 'gold', 'platinum'])->default('silver')->after('total_poin');
        });

        Schema::table('loan_payments', function (Blueprint $table) {
            $table->string('payment_gateway_ref')->nullable()->after('payment_date');
            $table->string('payment_url')->nullable()->after('payment_gateway_ref');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vouchers');
        Schema::dropIfExists('member_lands');
        Schema::dropIfExists('p2p_products');
        Schema::dropIfExists('announcements');

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['payment_gateway_ref', 'payment_url']);
        });

        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn('tier');
        });

        Schema::table('loan_payments', function (Blueprint $table) {
            $table->dropColumn(['payment_gateway_ref', 'payment_url']);
        });
    }
};
