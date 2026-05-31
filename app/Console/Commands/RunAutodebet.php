<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Member;
use App\Models\MemberSaving;
use App\Models\SystemConfig;
use App\Services\SavingsService;
use Illuminate\Support\Facades\DB;
use Exception;

class RunAutodebet extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kdkmp:run-autodebet';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Jalankan autodebet bulanan untuk simpanan wajib anggota';

    /**
     * Execute the console command.
     */
    public function handle(SavingsService $savingsService)
    {
        $this->info('Memulai proses autodebet simpanan wajib bulanan...');

        try {
            $members = Member::where('status_aktif', true)->get();
            $successCount = 0;
            $failCount = 0;
            
            $amount = (float) (SystemConfig::where('key', 'IURAN_WAJIB_NOMINAL')->first()->value ?? 50000.00);
            
            $this->info("Nominal Iuran Wajib: Rp " . number_format($amount, 0, ',', '.'));

            DB::beginTransaction();
            foreach ($members as $member) {
                // Check current sukarela balance
                $sukarelaBalance = MemberSaving::where('member_id', $member->id)
                    ->where('type', 'sukarela')
                    ->sum('amount');

                if ($sukarelaBalance >= $amount) {
                    // 1. Debit from Simpanan Sukarela
                    $savingsService->recordDebit(
                        $member->id,
                        'sukarela',
                        $amount,
                        'Autodebet bulanan untuk Simpanan Wajib'
                    );

                    // 2. Deposit to Simpanan Wajib
                    $savingsService->recordSaving(
                        $member->id,
                        'wajib',
                        $amount,
                        'Setoran Simpanan Wajib via Autodebet Sukarela'
                    );

                    $successCount++;
                    $this->line(" - Member ID {$member->id}: Sukses");
                } else {
                    $failCount++;
                    $this->line(" - Member ID {$member->id}: Gagal (Saldo kurang)");
                }
            }
            DB::commit();

            $this->info("Autodebet selesai! Sukses: {$successCount}, Gagal: {$failCount}.");
            return Command::SUCCESS;
        } catch (Exception $e) {
            DB::rollBack();
            $this->error('Gagal menjalankan autodebet: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
