<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Member;
use App\Models\MemberSaving;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class GotongRoyongSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Staff for Desa Gotong Royong (branch_id = 2)
        User::create([
            'name' => 'Pengurus Gotong Royong',
            'email' => 'pengurus_gr@kdkmp.org',
            'password' => Hash::make('password123'),
            'role' => 'pengurus',
            'status' => 'active',
            'branch_id' => 2,
        ]);

        User::create([
            'name' => 'Kasir Gotong Royong',
            'email' => 'kasir_gr@kdkmp.org',
            'password' => Hash::make('password123'),
            'role' => 'kasir',
            'status' => 'active',
            'branch_id' => 2,
        ]);

        // 2. Create Member for Desa Gotong Royong (branch_id = 2)
        $userGatot = User::create([
            'name' => 'Gatot Wibowo',
            'email' => 'gatot@kdkmp.org',
            'password' => Hash::make('password123'),
            'role' => 'anggota',
            'status' => 'active',
            'branch_id' => 2,
        ]);

        $memberGatot = Member::create([
            'user_id' => $userGatot->id,
            'nik' => '3201234567890003',
            'nomor_anggota' => 'MBR-20260531-0003',
            'alamat_desa' => 'RT 01 / RW 02, Desa Gotong Royong',
            'tanggal_bergabung' => '2026-05-31',
            'total_poin' => 150,
            'status_aktif' => true,
            'no_hp' => '081234567892',
        ]);

        // 3. Seed savings for Gatot so he has balance to purchase/test
        MemberSaving::create([
            'member_id' => $memberGatot->id,
            'type' => 'pokok',
            'amount' => 100000.00,
            'transaction_date' => Carbon::now(),
            'notes' => 'Setoran Simpanan Pokok Keanggotaan',
        ]);

        MemberSaving::create([
            'member_id' => $memberGatot->id,
            'type' => 'wajib',
            'amount' => 50000.00,
            'transaction_date' => Carbon::now(),
            'notes' => 'Iuran Wajib Bulanan',
        ]);

        MemberSaving::create([
            'member_id' => $memberGatot->id,
            'type' => 'sukarela',
            'amount' => 350000.00,
            'transaction_date' => Carbon::now(),
            'notes' => 'Setoran Sukarela Awal',
        ]);
    }
}
