<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Member;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\MemberSaving;
use App\Models\Loan;
use App\Models\LoanPayment;
use App\Models\CropAbsorption;
use App\Models\PurchaseOrder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StressTestSeeder extends Seeder
{
    /**
     * Run the database seeds to simulate high volume for performance testing.
     */
    public function run(): void
    {
        $branchId = 1; // Testing on Branch 1 (Desa Merah Putih)
        $member = Member::whereHas('user', fn($q) => $q->where('branch_id', $branchId))->first();
        
        if (!$member) {
            $this->command->error('No member found for branch 1. Please run DatabaseSeeder first.');
            return;
        }

        $this->command->info('Starting stress test seeding for 12-month period...');

        DB::beginTransaction();
        try {
            for ($m = 1; $m <= 12; $m++) {
                $date = Carbon::create(date('Y'), $m, 15);
                
                // 1. Generate 500 orders per month
                for ($i = 0; $i < 500; $i++) {
                    Order::create([
                        'user_id' => $member->user_id,
                        'branch_id' => $branchId,
                        'order_number' => 'STR-' . $m . '-' . $i . '-' . uniqid(),
                        'total_amount' => rand(50000, 500000),
                        'payment_status' => 'paid',
                        'payment_method' => 'cash',
                        'created_at' => $date->copy()->addDays(rand(-14, 14)),
                    ]);
                }

                // 2. Generate 200 savings transactions per month
                for ($i = 0; $i < 200; $i++) {
                    MemberSaving::create([
                        'member_id' => $member->id,
                        'type' => 'sukarela',
                        'amount' => rand(10000, 100000),
                        'transaction_date' => $date->copy()->addDays(rand(-14, 14)),
                        'notes' => 'Stress test saving',
                    ]);
                }

                // 3. Generate 50 crop absorptions per month
                for ($i = 0; $i < 50; $i++) {
                    CropAbsorption::create([
                        'member_id' => $member->id,
                        'branch_id' => $branchId,
                        'product_name' => 'Stress Padi',
                        'quantity' => rand(100, 1000),
                        'price_per_unit' => 6000,
                        'total_payout' => rand(600000, 6000000),
                        'status' => 'paid',
                        'absorption_date' => $date->copy()->addDays(rand(-14, 14)),
                        'created_at' => $date->copy()->addDays(rand(-14, 14)),
                    ]);
                }

                // 4. Generate 20 loans per month
                for ($i = 0; $i < 20; $i++) {
                    Loan::create([
                        'member_id' => $member->id,
                        'branch_id' => $branchId,
                        'loan_code' => 'LN-STR-' . $m . '-' . $i . '-' . uniqid(),
                        'amount_requested' => 10000000,
                        'amount_approved' => rand(1000000, 10000000),
                        'interest_rate' => 1.5,
                        'tenor_months' => 12,
                        'status' => 'active',
                        'created_at' => $date->copy()->addDays(rand(-14, 14)),
                    ]);
                }
            }
            DB::commit();
            $this->command->info('Stress test seeding completed successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Error during seeding: ' . $e->getMessage());
        }
    }
}
