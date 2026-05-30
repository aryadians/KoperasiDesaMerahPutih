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
        // 1. Create Categories (Alfamart/Indomaret style)
        $sembakoCat = Category::create(['name' => 'Sembako & Bahan Pokok', 'slug' => 'sembako']);
        $mieCat = Category::create(['name' => 'Mie Instan & Makanan Cepat Saji', 'slug' => 'mie-instan']);
        $minumanCat = Category::create(['name' => 'Minuman & Air Mineral', 'slug' => 'minuman']);
        $snackCat = Category::create(['name' => 'Camilan & Snack', 'slug' => 'camilan']);
        $householdCat = Category::create(['name' => 'Kebutuhan Rumah Tangga & Mandi', 'slug' => 'kebutuhan-mandi']);
        $cropCat = Category::create(['name' => 'Hasil Tani Lokal Desa (Agro)', 'slug' => 'hasil-tani']);

        // 2. Create Products with diverse Unsplash images (no duplicate IDs)
        
        // --- Category: Sembako ---
        Product::create([
            'category_id' => $sembakoCat->id,
            'name' => 'Beras Sania Premium 5kg',
            'description' => 'Beras poles premium putih bersih dan pulen alami.',
            'price_member' => 68500.00,
            'price_non_member' => 74000.00,
            'current_stock' => 50,
            'unit' => 'bag',
            'is_local_product' => false,
            'image_url' => 'https://images.unsplash.com/photo-1586201375761-83865001e31c?auto=format&fit=crop&w=400&q=80',
        ]);
        Product::create([
            'category_id' => $sembakoCat->id,
            'name' => 'Minyak Goreng Bimoli 2L',
            'description' => 'Minyak goreng kelapa sawit murni berkualitas tinggi.',
            'price_member' => 34500.00,
            'price_non_member' => 38000.00,
            'current_stock' => 45,
            'unit' => 'bottle',
            'is_local_product' => false,
            'image_url' => 'https://images.unsplash.com/photo-1474979266404-7eaacbcd87c5?auto=format&fit=crop&w=400&q=80',
        ]);
        Product::create([
            'category_id' => $sembakoCat->id,
            'name' => 'Gula Pasir Gulaku Premium 1kg',
            'description' => 'Gula kristal putih manis tebu asli pilihan.',
            'price_member' => 15800.00,
            'price_non_member' => 17500.00,
            'current_stock' => 60,
            'unit' => 'kg',
            'is_local_product' => false,
            'image_url' => 'https://images.unsplash.com/photo-1581798459219-318e76aecc7b?auto=format&fit=crop&w=400&q=80',
        ]);
        Product::create([
            'category_id' => $sembakoCat->id,
            'name' => 'Telur Ayam Negeri 1 Pack (10 Pcs)',
            'description' => 'Telur ayam negeri segar berprotein tinggi pilihan.',
            'price_member' => 24000.00,
            'price_non_member' => 26500.00,
            'current_stock' => 30,
            'unit' => 'pack',
            'is_local_product' => false,
            'image_url' => 'https://images.unsplash.com/photo-1516448620398-c5f44bf9f441?auto=format&fit=crop&w=400&q=80',
        ]);
        Product::create([
            'category_id' => $sembakoCat->id,
            'name' => 'Tepung Terigu Segitiga Biru 1kg',
            'description' => 'Tepung terigu protein sedang serbaguna.',
            'price_member' => 12500.00,
            'price_non_member' => 14000.00,
            'current_stock' => 40,
            'unit' => 'kg',
            'is_local_product' => false,
            'image_url' => 'https://images.unsplash.com/photo-1509440159596-0249088772ff?auto=format&fit=crop&w=400&q=80',
        ]);

        // --- Category: Mie Instan ---
        Product::create([
            'category_id' => $mieCat->id,
            'name' => 'Indomie Goreng Spesial',
            'description' => 'Mie goreng instan terfavorit dengan bumbu legendaris.',
            'price_member' => 3100.00,
            'price_non_member' => 3500.00,
            'current_stock' => 200,
            'unit' => 'pcs',
            'is_local_product' => false,
            'image_url' => 'https://images.unsplash.com/photo-1569718212165-3a8278d5f624?auto=format&fit=crop&w=400&q=80',
        ]);
        Product::create([
            'category_id' => $mieCat->id,
            'name' => 'Indomie Kari Ayam dengan Bawang Goreng',
            'description' => 'Mie instan kuah rasa kari ayam yang gurih mantap.',
            'price_member' => 3200.00,
            'price_non_member' => 3600.00,
            'current_stock' => 150,
            'unit' => 'pcs',
            'is_local_product' => false,
            'image_url' => 'https://images.unsplash.com/photo-1612927601601-6638404737ce?auto=format&fit=crop&w=400&q=80',
        ]);
        Product::create([
            'category_id' => $mieCat->id,
            'name' => 'Mie Sedaap Goreng',
            'description' => 'Mie goreng instan renyah kriuk-kriuk gurih.',
            'price_member' => 3000.00,
            'price_non_member' => 3400.00,
            'current_stock' => 120,
            'unit' => 'pcs',
            'is_local_product' => false,
            'image_url' => 'https://images.unsplash.com/photo-1552611052-33e04de081de?auto=format&fit=crop&w=400&q=80',
        ]);

        // --- Category: Minuman ---
        Product::create([
            'category_id' => $minumanCat->id,
            'name' => 'Air Mineral Aqua 600ml',
            'description' => 'Air mineral murni alami dari pegunungan vulkanik.',
            'price_member' => 3000.00,
            'price_non_member' => 3500.00,
            'current_stock' => 300,
            'unit' => 'bottle',
            'is_local_product' => false,
            'image_url' => 'https://images.unsplash.com/photo-1608889174633-e02ff35b9dc6?auto=format&fit=crop&w=400&q=80',
        ]);
        Product::create([
            'category_id' => $minumanCat->id,
            'name' => 'Teh Botol Sosro Kotak 350ml',
            'description' => 'Teh melati manis khas Indonesia dalam kemasan kotak.',
            'price_member' => 3800.00,
            'price_non_member' => 4500.00,
            'current_stock' => 100,
            'unit' => 'pcs',
            'is_local_product' => false,
            'image_url' => 'https://images.unsplash.com/photo-1576092768241-dec231879fc3?auto=format&fit=crop&w=400&q=80',
        ]);
        Product::create([
            'category_id' => $minumanCat->id,
            'name' => 'Susu UHT Ultra Milk Cokelat 250ml',
            'description' => 'Susu cair segar bergizi dengan rasa cokelat lezat.',
            'price_member' => 5800.00,
            'price_non_member' => 6500.00,
            'current_stock' => 80,
            'unit' => 'pcs',
            'is_local_product' => false,
            'image_url' => 'https://images.unsplash.com/photo-1550583724-b2692b85b150?auto=format&fit=crop&w=400&q=80',
        ]);

        // --- Category: Snack / Camilan ---
        Product::create([
            'category_id' => $snackCat->id,
            'name' => 'Taro Net Seaweed 65g',
            'description' => 'Camilan keripik jaring rasa rumput laut gurih.',
            'price_member' => 8500.00,
            'price_non_member' => 9500.00,
            'current_stock' => 50,
            'unit' => 'pcs',
            'is_local_product' => false,
            'image_url' => 'https://images.unsplash.com/photo-1599490659213-e2b9527b0876?auto=format&fit=crop&w=400&q=80',
        ]);
        Product::create([
            'category_id' => $snackCat->id,
            'name' => 'Kusuka Keripik Singkong Barbeque 180g',
            'description' => 'Keripik singkong renyah dengan rasa bumbu barbeque.',
            'price_member' => 14000.00,
            'price_non_member' => 16000.00,
            'current_stock' => 40,
            'unit' => 'pcs',
            'is_local_product' => false,
            'image_url' => 'https://images.unsplash.com/photo-1566478989037-eec170784d0b?auto=format&fit=crop&w=400&q=80',
        ]);
        Product::create([
            'category_id' => $snackCat->id,
            'name' => 'Cokelat Silverqueen Almond 58g',
            'description' => 'Cokelat susu lezat bertabur kacang almond gurih.',
            'price_member' => 13500.00,
            'price_non_member' => 15500.00,
            'current_stock' => 60,
            'unit' => 'pcs',
            'is_local_product' => false,
            'image_url' => 'https://images.unsplash.com/photo-1587132137056-bfbf0166836e?auto=format&fit=crop&w=400&q=80',
        ]);

        // --- Category: Household / Toiletries ---
        Product::create([
            'category_id' => $householdCat->id,
            'name' => 'Sabun Mandi Lifebuoy Merah 85g',
            'description' => 'Sabun mandi antiseptik perlindungan kuman sekeluarga.',
            'price_member' => 3800.00,
            'price_non_member' => 4500.00,
            'current_stock' => 100,
            'unit' => 'pcs',
            'is_local_product' => false,
            'image_url' => 'https://images.unsplash.com/photo-1607006342411-1a90e5440c31?auto=format&fit=crop&w=400&q=80',
        ]);
        Product::create([
            'category_id' => $householdCat->id,
            'name' => 'Pasta Gigi Pepsodent Pencegah Gigi Berlubang 120g',
            'description' => 'Perlindungan gigi berlubang siang dan malam.',
            'price_member' => 9500.00,
            'price_non_member' => 11000.00,
            'current_stock' => 4, // Trigger low stock!
            'unit' => 'pcs',
            'is_local_product' => false,
            'image_url' => 'https://images.unsplash.com/photo-1559599141-38398a0e352d?auto=format&fit=crop&w=400&q=80',
        ]);
        Product::create([
            'category_id' => $householdCat->id,
            'name' => 'Deterjen Bubuk Rinso Antinoda 800g',
            'description' => 'Deterjen bubuk pembersih noda membandel sekali kucek.',
            'price_member' => 26500.00,
            'price_non_member' => 29000.00,
            'current_stock' => 30,
            'unit' => 'bag',
            'is_local_product' => false,
            'image_url' => 'https://images.unsplash.com/photo-1626806787461-102c1bfaaea1?auto=format&fit=crop&w=400&q=80',
        ]);

        // --- Category: Agro / Crop Local ---
        Product::create([
            'category_id' => $cropCat->id,
            'name' => 'Cabai Rawit Merah Lokal (Super Pedas)',
            'description' => 'Cabai rawit segar langsung petik hari ini dari kebun warga desa.',
            'price_member' => 38000.00,
            'price_non_member' => 42000.00,
            'current_stock' => 25,
            'unit' => 'kg',
            'is_local_product' => true,
            'image_url' => 'https://images.unsplash.com/photo-1588252399419-b9546121f599?auto=format&fit=crop&w=400&q=80',
        ]);
        Product::create([
            'category_id' => $cropCat->id,
            'name' => 'Bawang Merah Brebes Pilihan',
            'description' => 'Bawang merah wangi, padat kering berkualitas tinggi hasil bumi desa.',
            'price_member' => 28000.00,
            'price_non_member' => 32000.00,
            'current_stock' => 3, // Trigger low stock!
            'unit' => 'kg',
            'is_local_product' => true,
            'image_url' => 'https://images.unsplash.com/photo-1604998103154-e125f2107852?auto=format&fit=crop&w=400&q=80',
        ]);
        Product::create([
            'category_id' => $cropCat->id,
            'name' => 'Tomat Merah Segar Garut',
            'description' => 'Tomat sayur tebal, manis asam segar, berair melimpah.',
            'price_member' => 12000.00,
            'price_non_member' => 15000.00,
            'current_stock' => 15,
            'unit' => 'kg',
            'is_local_product' => true,
            'image_url' => 'https://images.unsplash.com/photo-1595855759920-86582396756a?auto=format&fit=crop&w=400&q=80',
        ]);
        Product::create([
            'category_id' => $cropCat->id,
            'name' => 'Kentang Dieng Super',
            'description' => 'Kentang dataran tinggi Dieng berukuran besar dan bersih.',
            'price_member' => 16000.00,
            'price_non_member' => 19000.00,
            'current_stock' => 35,
            'unit' => 'kg',
            'is_local_product' => true,
            'image_url' => 'https://images.unsplash.com/photo-1518977676601-b53f82aba655?auto=format&fit=crop&w=400&q=80',
        ]);
        Product::create([
            'category_id' => $cropCat->id,
            'name' => 'Beras Merah Organik Cianjur',
            'description' => 'Beras merah diet sehat kaya serat langsung dari petani Cianjur.',
            'price_member' => 18000.00,
            'price_non_member' => 21000.00,
            'current_stock' => 40,
            'unit' => 'kg',
            'is_local_product' => true,
            'image_url' => 'https://images.unsplash.com/photo-1590080875515-8a3a8dc5735e?auto=format&fit=crop&w=400&q=80',
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
