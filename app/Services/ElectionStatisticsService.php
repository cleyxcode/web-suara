<?php

namespace App\Services;

use App\Models\Candidate;
use App\Models\Election;
use App\Models\Round;
use Illuminate\Support\Facades\DB;

class ElectionStatisticsService
{
    /**
     * Get statistik per round
     * 
     * @param Round $round
     * @return array
     */
    public function getRoundStatistics(Round $round): array
    {
        // Ambil semua candidate yang BELUM tersingkir SEBELUM round ini dimulai
        // ATAU yang tersingkir PADA round ini (untuk ditampilkan di round ini)
        $candidates = Candidate::where('election_id', $round->election_id)
            ->where(function ($query) use ($round) {
                // Calon yang masih aktif
                $query->where('status', 'active')
                    // ATAU calon yang tersingkir DI round ini
                    ->orWhere('eliminated_at_round_id', $round->id);
            })
            ->get();

        // Ambil vote count per candidate untuk round ini
        $voteCounts = DB::table('votes')
            ->select('candidate_id', DB::raw('COUNT(*) as total_votes'))
            ->where('round_id', $round->id)
            ->groupBy('candidate_id')
            ->pluck('total_votes', 'candidate_id');

        // Hitung total suara di round ini
        $totalVotes = $voteCounts->sum();

        // Format data dengan candidate yang relevan
        $statistics = $candidates->map(function ($candidate) use ($voteCounts, $totalVotes, $round) {
            $votes = $voteCounts->get($candidate->id, 0);
            $percentage = $totalVotes > 0 ? round(($votes / $totalVotes) * 100, 2) : 0;
            
            // Cek apakah calon tersingkir di round ini
            $isEliminatedThisRound = ($candidate->eliminated_at_round_id == $round->id);
            
            return [
                'candidate_id' => $candidate->id,
                'candidate_name' => $candidate->name,
                'candidate_status' => $candidate->status,
                'candidate_photo' => $candidate->photo,
                'total_votes' => $votes,
                'percentage' => $percentage,
                'is_eliminated' => $candidate->status === 'eliminated',
                'eliminated_this_round' => $isEliminatedThisRound,
            ];
        })
        ->sortByDesc('total_votes')
        ->values()
        ->toArray();

        return [
            'round' => [
                'id' => $round->id,
                'round_number' => $round->round_number,
                'status' => $round->status,
                'total_votes' => $totalVotes,
                'is_eliminated' => $round->is_eliminated,
            ],
            'statistics' => $statistics,
        ];
    }

    /**
     * Get statistik untuk semua round dalam satu election
     * 
     * @param Election $election
     * @return array
     */
    public function getElectionStatistics(Election $election): array
    {
        $rounds = Round::where('election_id', $election->id)
            ->orderBy('round_number')
            ->get();

        $statisticsPerRound = [];

        foreach ($rounds as $round) {
            $statisticsPerRound[] = $this->getRoundStatistics($round);
        }

        return [
            'election' => [
                'id' => $election->id,
                'name' => $election->name,
                'status' => $election->status,
                'total_participants' => $election->total_participants,
            ],
            'rounds' => $statisticsPerRound,
        ];
    }

    /**
     * Get data untuk line chart (perbandingan antar round)
     * 
     * @param Election $election
     * @return array
     */
    public function getLineChartData(Election $election): array
    {
        $rounds = Round::where('election_id', $election->id)
            ->orderBy('round_number')
            ->get();

        // Ambil semua candidate yang pernah ikut
        $candidates = Candidate::where('election_id', $election->id)->get();

        $datasets = [];

        foreach ($candidates as $candidate) {
            $data = [];

            foreach ($rounds as $round) {
                $votes = DB::table('votes')
                    ->where('round_id', $round->id)
                    ->where('candidate_id', $candidate->id)
                    ->count();

                $data[] = $votes;
            }

            $datasets[] = [
                'label' => $candidate->name,
                'data' => $data,
                'status' => $candidate->status,
            ];
        }

        return [
            'labels' => $rounds->map(fn($r) => "Putaran {$r->round_number}")->toArray(),
            'datasets' => $datasets,
        ];
    }

    /**
     * Get winner (jika election sudah selesai)
     * 
     * @param Election $election
     * @return array|null
     */
    public function getWinner(Election $election): ?array
    {
        if ($election->status !== 'finished') {
            return null;
        }

        $winner = Candidate::where('election_id', $election->id)
            ->where('status', 'active')
            ->first();

        if (!$winner) {
            return null;
        }

        // Ambil total suara winner dari round terakhir
        $lastRound = Round::where('election_id', $election->id)
            ->orderByDesc('round_number')
            ->first();

        $totalVotes = 0;
        if ($lastRound) {
            $totalVotes = DB::table('votes')
                ->where('round_id', $lastRound->id)
                ->where('candidate_id', $winner->id)
                ->count();
        }

        return [
            'id' => $winner->id,
            'name' => $winner->name,
            'photo' => $winner->photo,
            'total_votes' => $totalVotes,
            'round_number' => $lastRound ? $lastRound->round_number : 0,
        ];
    }
}