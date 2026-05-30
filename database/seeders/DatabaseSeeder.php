<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Member;
use App\Models\Category;
use App\Models\Product;
use App\Models\MemberSaving;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Categories
        $sembakoCat = Category::create(['name' => 'Sembako & Kebutuhan Pokok', 'slug' => 'sembako']);
        $cropCat = Category::create(['name' => 'Hasil Tani Lokal', 'slug' => 'hasil-tani']);
        $mandiCat = Category::create(['name' => 'Kebutuhan Mandi & Rumah Tangga', 'slug' => 'kebutuhan-mandi']);

        // 2. Create Products
        // Sembako
        Product::create([
            'category_id' => $sembakoCat->id,
            'name' => 'Beras Pandan Wangi Premium',
            'description' => 'Beras lokal wangi dan pulen asli dari sawah warga.',
            'price_member' => 13500.00,
            'price_non_member' => 15000.00,
            'current_stock' => 150,
            'unit' => 'kg',
            'is_local_product' => true,
        ]);
        Product::create([
            'category_id' => $sembakoCat->id,
            'name' => 'Minyak Goreng Sawit 1L',
            'description' => 'Minyak goreng jernih kualitas ekspor.',
            'price_member' => 18000.00,
            'price_non_member' => 20000.00,
            'current_stock' => 80,
            'unit' => 'liter',
            'is_local_product' => false,
        ]);
        Product::create([
            'category_id' => $sembakoCat->id,
            'name' => 'Gula Pasir Kristal 1kg',
            'description' => 'Gula tebu murni manis alami.',
            'price_member' => 15500.00,
            'price_non_member' => 17000.00,
            'current_stock' => 60,
            'unit' => 'kg',
            'is_local_product' => false,
        ]);

        // Hasil Tani
        Product::create([
            'category_id' => $cropCat->id,
            'name' => 'Cabai Rawit Merah Lokal',
            'description' => 'Cabai rawit segar langsung petik dari kebun sayur desa.',
            'price_member' => 38000.00,
            'price_non_member' => 42000.00,
            'current_stock' => 20,
            'unit' => 'kg',
            'is_local_product' => true,
        ]);
        Product::create([
            'category_id' => $cropCat->id,
            'name' => 'Bawang Merah Samosir',
            'description' => 'Bawang merah berkualitas dengan aroma tajam.',
            'price_member' => 28000.00,
            'price_non_member' => 32000.00,
            'current_stock' => 3, // Trigger low stock!
            'unit' => 'kg',
            'is_local_product' => true,
        ]);

        // Kebutuhan Mandi
        Product::create([
            'category_id' => $mandiCat->id,
            'name' => 'Sabun Mandi Batang 80g',
            'description' => 'Sabun mandi antiseptik wangi menyegarkan.',
            'price_member' => 3500.00,
            'price_non_member' => 4500.00,
            'current_stock' => 120,
            'unit' => 'pcs',
            'is_local_product' => false,
        ]);

        // 3. Create Staff Users
        User::create([
            'name' => 'Pengurus Koperasi',
            'email' => 'pengurus@kdkmp.org',
            'password' => Hash::make('password123'),
            'role' => 'pengurus',
            'status' => 'active',
        ]);
        
        User::create([
            'name' => 'Kasir Koperasi',
            'email' => 'kasir@kdkmp.org',
            'password' => Hash::make('password123'),
            'role' => 'kasir',
            'status' => 'active',
        ]);

        // 4. Create Members Users & Profiles
        // Member 1
        $userBudi = User::create([
            'name' => 'Budi Setiadi',
            'email' => 'budi@kdkmp.org',
            'password' => Hash::make('password123'),
            'role' => 'anggota',
            'status' => 'active',
        ]);
        $memberBudi = Member::create([
            'user_id' => $userBudi->id,
            'nik' => '3201234567890001',
            'nomor_anggota' => 'MBR-20260530-0001',
            'alamat_desa' => 'RT 02 / RW 04, Dusun Merah, Desa Merah Putih',
            'tanggal_bergabung' => '2026-01-15',
            'total_poin' => 450, // Has points for SHU testing!
            'status_aktif' => true,
        ]);

        // Member 2
        $userAni = User::create([
            'name' => 'Ani Wijaya',
            'email' => 'ani@kdkmp.org',
            'password' => Hash::make('password123'),
            'role' => 'anggota',
            'status' => 'active',
        ]);
        $memberAni = Member::create([
            'user_id' => $userAni->id,
            'nik' => '3201234567890002',
            'nomor_anggota' => 'MBR-20260530-0002',
            'alamat_desa' => 'RT 05 / RW 01, Dusun Putih, Desa Merah Putih',
            'tanggal_bergabung' => '2026-02-20',
            'total_poin' => 250, // Has points for SHU testing!
            'status_aktif' => true,
        ]);

        // 5. Seed initial Savings for Members
        // Budi's savings
        MemberSaving::create([
            'member_id' => $memberBudi->id,
            'type' => 'pokok',
            'amount' => 100000.00,
            'transaction_date' => Carbon::parse('2026-01-15 09:00:00'),
            'notes' => 'Setoran Simpanan Pokok Keanggotaan',
        ]);
        MemberSaving::create([
            'member_id' => $memberBudi->id,
            'type' => 'wajib',
            'amount' => 50000.00,
            'transaction_date' => Carbon::parse('2026-02-01 10:00:00'),
            'notes' => 'Iuran Wajib Bulanan Februari',
        ]);
        MemberSaving::create([
            'member_id' => $memberBudi->id,
            'type' => 'wajib',
            'amount' => 50000.00,
            'transaction_date' => Carbon::parse('2026-03-01 10:00:00'),
            'notes' => 'Iuran Wajib Bulanan Maret',
        ]);
        MemberSaving::create([
            'member_id' => $memberBudi->id,
            'type' => 'sukarela',
            'amount' => 500000.00,
            'transaction_date' => Carbon::parse('2026-03-15 11:30:00'),
            'notes' => 'Setoran Sukarela Awal',
        ]);

        // Ani's savings
        MemberSaving::create([
            'member_id' => $memberAni->id,
            'type' => 'pokok',
            'amount' => 100000.00,
            'transaction_date' => Carbon::parse('2026-02-20 14:00:00'),
            'notes' => 'Setoran Simpanan Pokok Keanggotaan',
        ]);
        MemberSaving::create([
            'member_id' => $memberAni->id,
            'type' => 'wajib',
            'amount' => 50000.00,
            'transaction_date' => Carbon::parse('2026-03-01 11:00:00'),
            'notes' => 'Iuran Wajib Bulanan Maret',
        ]);
        MemberSaving::create([
            'member_id' => $memberAni->id,
            'type' => 'sukarela',
            'amount' => 200000.00,
            'transaction_date' => Carbon::parse('2026-03-05 15:45:00'),
            'notes' => 'Setoran Sukarela Awal',
        ]);
    }
}
