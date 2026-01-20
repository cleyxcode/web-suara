<?php

namespace App\Services;

use App\Models\Candidate;
use App\Models\Election;
use App\Models\Round;
use Illuminate\Support\Facades\DB;

class CandidateEliminationService
{
    /**
     * Threshold minimum suara untuk lolos eliminasi
     */
    const MINIMUM_VOTES = 5;

    /**
     * Eliminasi calon berdasarkan jumlah suara pada round tertentu
     * 
     * @param Round $round
     * @return array
     */
    public function eliminate(Round $round): array
    {
        // Validasi 1: Round harus berstatus 'completed'
        if ($round->status !== 'completed') {
            return [
                'success' => false,
                'message' => 'Eliminasi hanya bisa dilakukan pada putaran yang sudah selesai dan tervalidasi.',
                'data' => [],
            ];
        }

        // Validasi 2: Eliminasi tidak boleh dilakukan dua kali
        if ($round->is_eliminated) {
            return [
                'success' => false,
                'message' => 'Eliminasi sudah pernah dilakukan pada putaran ini.',
                'data' => [],
            ];
        }

        // Hitung suara per calon pada round ini
        $candidateVotes = DB::table('votes')
            ->select('candidate_id', DB::raw('COUNT(*) as vote_count'))
            ->where('round_id', $round->id)
            ->groupBy('candidate_id')
            ->get()
            ->keyBy('candidate_id');

        // Ambil semua calon aktif dari election ini
        $candidates = Candidate::where('election_id', $round->election_id)
            ->where('status', 'active')
            ->get();

        $eliminatedCandidates = [];
        $survivingCandidates = [];

        foreach ($candidates as $candidate) {
            $voteCount = $candidateVotes->get($candidate->id)->vote_count ?? 0;

            if ($voteCount < self::MINIMUM_VOTES) {
                // Eliminasi calon dengan suara < 5
                $candidate->update([
                    'status' => 'eliminated',
                    'eliminated_at_round_id' => $round->id, // SIMPAN ROUND ID SAAT ELIMINASI
                ]);
                
                $eliminatedCandidates[] = [
                    'id' => $candidate->id,
                    'name' => $candidate->name,
                    'votes' => $voteCount,
                ];
            } else {
                // Calon lolos
                $survivingCandidates[] = [
                    'id' => $candidate->id,
                    'name' => $candidate->name,
                    'votes' => $voteCount,
                ];
            }
        }

        // Update flag eliminasi di round
        $round->update([
            'is_eliminated' => true,
            'eliminated_at' => now(),
        ]);

        // Cek apakah perlu menyelesaikan pemilihan
        $this->checkElectionCompletion($round->election);

        return [
            'success' => true,
            'message' => $this->buildEliminationMessage($eliminatedCandidates, $survivingCandidates),
            'data' => [
                'eliminated' => $eliminatedCandidates,
                'surviving' => $survivingCandidates,
                'total_eliminated' => count($eliminatedCandidates),
                'total_surviving' => count($survivingCandidates),
            ],
        ];
    }

    /**
     * Cek apakah pemilihan sudah selesai (hanya 1 calon tersisa)
     * 
     * @param Election $election
     * @return void
     */
    protected function checkElectionCompletion(Election $election): void
    {
        $activeCandidatesCount = Candidate::where('election_id', $election->id)
            ->where('status', 'active')
            ->count();

        if ($activeCandidatesCount === 1) {
            // Hanya 1 calon tersisa, pemilihan selesai
            $election->update(['status' => 'finished']);
        }
    }

    /**
     * Build pesan eliminasi
     * 
     * @param array $eliminated
     * @param array $surviving
     * @return string
     */
    protected function buildEliminationMessage(array $eliminated, array $surviving): string
    {
        $message = "✅ Eliminasi berhasil dilakukan!\n\n";

        if (count($eliminated) > 0) {
            $message .= "🔴 CALON TERSINGKIR (" . count($eliminated) . "):\n";
            foreach ($eliminated as $candidate) {
                $message .= "- {$candidate['name']} ({$candidate['votes']} suara)\n";
            }
            $message .= "\n";
        } else {
            $message .= "✅ Tidak ada calon yang tersingkir.\n\n";
        }

        if (count($surviving) > 0) {
            $message .= "🟢 CALON LOLOS (" . count($surviving) . "):\n";
            foreach ($surviving as $candidate) {
                $message .= "- {$candidate['name']} ({$candidate['votes']} suara)\n";
            }
        }

        // Cek apakah pemilihan selesai
        if (count($surviving) === 1) {
            $winner = $surviving[0];
            $message .= "\n🎉 PEMILIHAN SELESAI!\n";
            $message .= "🏆 Ketua Terpilih: {$winner['name']}";
        } elseif (count($surviving) > 1) {
            $message .= "\n➡️ Pemilihan dilanjutkan ke putaran berikutnya.";
        }

        return $message;
    }

    /**
     * Get hasil voting untuk round tertentu
     * 
     * @param Round $round
     * @return array
     */
    public function getVotingResults(Round $round): array
    {
        $results = DB::table('votes')
            ->select(
                'candidates.id',
                'candidates.name',
                'candidates.status',
                DB::raw('COUNT(*) as vote_count')
            )
            ->join('candidates', 'votes.candidate_id', '=', 'candidates.id')
            ->where('votes.round_id', $round->id)
            ->groupBy('candidates.id', 'candidates.name', 'candidates.status')
            ->orderByDesc('vote_count')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'status' => $item->status,
                    'votes' => $item->vote_count,
                    'will_be_eliminated' => $item->vote_count < self::MINIMUM_VOTES,
                ];
            })
            ->toArray();

        return $results;
    }

    /**
     * Cek apakah bisa membuat round baru
     * 
     * @param Election $election
     * @return array
     */
    public function canCreateNewRound(Election $election): array
    {
        // Cek 1: Election tidak boleh finished
        if ($election->status === 'finished') {
            return [
                'can_create' => false,
                'reason' => 'Pemilihan sudah selesai.',
            ];
        }

        // Cek 2: Tidak boleh ada round active
        $hasActiveRound = Round::where('election_id', $election->id)
            ->where('status', 'active')
            ->exists();

        if ($hasActiveRound) {
            return [
                'can_create' => false,
                'reason' => 'Masih ada putaran yang aktif. Tutup terlebih dahulu.',
            ];
        }

        // Cek 3: Round terakhir harus sudah dieliminasi
        $lastRound = Round::where('election_id', $election->id)
            ->where('status', 'completed')
            ->orderByDesc('round_number')
            ->first();

        if ($lastRound && !$lastRound->is_eliminated) {
            return [
                'can_create' => false,
                'reason' => 'Putaran sebelumnya belum dilakukan eliminasi.',
            ];
        }

        // Cek 4: Harus ada lebih dari 1 calon aktif
        $activeCandidatesCount = Candidate::where('election_id', $election->id)
            ->where('status', 'active')
            ->count();

        if ($activeCandidatesCount <= 1) {
            return [
                'can_create' => false,
                'reason' => 'Tidak cukup calon aktif untuk membuat putaran baru.',
            ];
        }

        return [
            'can_create' => true,
            'active_candidates' => $activeCandidatesCount,
        ];
    }
}