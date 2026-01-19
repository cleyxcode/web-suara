<?php

namespace App\Services;

use App\Models\Round;
use Illuminate\Support\Facades\DB;

class RoundValidationService
{
    /**
     * Validasi apakah jumlah suara sesuai dengan jumlah peserta
     * 
     * @param Round $round
     * @return array ['is_valid' => bool, 'total_votes' => int, 'total_participants' => int, 'message' => string]
     */
    public function validateRound(Round $round): array
    {
        // Validasi: Round harus dalam status 'completed'
        if ($round->status !== 'completed') {
            return [
                'is_valid' => false,
                'total_votes' => 0,
                'total_participants' => 0,
                'message' => 'Putaran harus ditutup terlebih dahulu sebelum divalidasi.',
            ];
        }

        // Ambil total_participants dari election
        $totalParticipants = $round->election->total_participants;

        // Hitung total votes DARI DATABASE (bukan dari kolom total_votes)
        $totalVotes = $round->votes()->count();

        // Update kolom total_votes dengan data yang benar dari database
        $round->update(['total_votes' => $totalVotes]);

        // Validasi: Jumlah suara harus sama dengan jumlah peserta
        $isValid = ($totalVotes === $totalParticipants);

        // Update status round berdasarkan hasil validasi
        if ($isValid) {
            // Putaran SAH - status tetap completed
            $round->update(['status' => 'completed']);

            return [
                'is_valid' => true,
                'total_votes' => $totalVotes,
                'total_participants' => $totalParticipants,
                'message' => "✅ PUTARAN SAH! Jumlah suara ({$totalVotes}) sesuai dengan jumlah peserta ({$totalParticipants}).",
            ];
        } else {
            // Putaran TIDAK SAH
            $round->update(['status' => 'invalid']);

            // Hitung selisih dengan benar
            if ($totalVotes > $totalParticipants) {
                $difference = $totalVotes - $totalParticipants;
                $message = "❌ PUTARAN TIDAK SAH! Jumlah suara ({$totalVotes}) MELEBIHI jumlah peserta ({$totalParticipants}). Kelebihan: {$difference} suara. Ada kesalahan dalam proses voting.";
            } else {
                $difference = $totalParticipants - $totalVotes;
                $message = "❌ PUTARAN TIDAK SAH! Jumlah suara ({$totalVotes}) kurang dari jumlah peserta ({$totalParticipants}). Kekurangan: {$difference} suara. Silakan lakukan voting ulang.";
            }

            return [
                'is_valid' => false,
                'total_votes' => $totalVotes,
                'total_participants' => $totalParticipants,
                'message' => $message,
            ];
        }
    }

    /**
     * Reset round untuk voting ulang
     * Round tetap tersimpan, votes tetap ada (diarsipkan)
     * Status diubah ke 'draft' untuk bisa diaktifkan lagi
     * 
     * @param Round $round
     * @return array
     */
    public function resetRoundForRevote(Round $round): array
    {
        // Validasi: Hanya round dengan status 'invalid' yang bisa di-reset
        if ($round->status !== 'invalid') {
            return [
                'success' => false,
                'message' => 'Hanya putaran yang tidak sah yang dapat di-reset untuk voting ulang.',
            ];
        }

        // Ubah status ke draft agar bisa diaktifkan kembali
        $round->update([
            'status' => 'draft',
            'total_votes' => 0,
        ]);

        return [
            'success' => true,
            'message' => "Putaran {$round->round_number} telah di-reset. Silakan aktifkan kembali untuk voting ulang. Vote sebelumnya tetap tersimpan sebagai arsip.",
        ];
    }

    /**
     * Get summary untuk round tertentu
     * 
     * @param Round $round
     * @return array
     */
    public function getRoundSummary(Round $round): array
    {
        $totalParticipants = $round->election->total_participants;
        $totalVotes = $round->votes()->count();
        $difference = $totalParticipants - $totalVotes;
        $percentage = $totalParticipants > 0 
            ? round(($totalVotes / $totalParticipants) * 100, 2) 
            : 0;

        return [
            'round_number' => $round->round_number,
            'status' => $round->status,
            'total_participants' => $totalParticipants,
            'total_votes' => $totalVotes,
            'difference' => $difference,
            'percentage' => $percentage,
            'is_valid' => ($totalVotes === $totalParticipants && $round->status === 'completed'),
        ];
    }
}