<?php

namespace App\Services;

use App\Models\MemberSaving;
use App\Models\Member;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

class SavingsService
{
    /**
     * Record a new savings transaction for a member.
     *
     * @param int $memberId
     * @param string $type 'pokok'|'wajib'|'sukarela'
     * @param float $amount
     * @param string|null $notes
     * @return MemberSaving
     * @throws Exception
     */
    public function recordSaving(int $memberId, string $type, float $amount, ?string $notes = null): MemberSaving
    {
        if ($amount <= 0) {
            throw new Exception("Jumlah nominal simpanan harus lebih besar dari nol.");
        }

        if (!in_array($type, ['pokok', 'wajib', 'sukarela'])) {
            throw new Exception("Jenis simpanan tidak valid.");
        }

        return DB::transaction(function () use ($memberId, $type, $amount, $notes) {
            // Verify member exists
            $member = Member::find($memberId);
            if (!$member) {
                throw new Exception("Anggota tidak ditemukan.");
            }

            if (!$member->status_aktif) {
                throw new Exception("Status anggota tidak aktif, tidak dapat memproses simpanan.");
            }

            // Create saving record
            $saving = MemberSaving::create([
                'member_id' => $memberId,
                'type' => $type,
                'amount' => $amount,
                'transaction_date' => Carbon::now(),
                'notes' => $notes ?? "Setoran simpanan {$type}",
            ]);

            return $saving;
        });
    }

    /**
     * Get savings balance by type for a member.
     *
     * @param int $memberId
     * @return array
     */
    public function getBalances(int $memberId): array
    {
        return [
            'pokok' => MemberSaving::where('member_id', $memberId)->where('type', 'pokok')->sum('amount'),
            'wajib' => MemberSaving::where('member_id', $memberId)->where('type', 'wajib')->sum('amount'),
            'sukarela' => MemberSaving::where('member_id', $memberId)->where('type', 'sukarela')->sum('amount'),
            'total' => MemberSaving::where('member_id', $memberId)->sum('amount'),
        ];
    }
}
