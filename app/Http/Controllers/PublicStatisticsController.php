<?php

namespace App\Http\Controllers;

use App\Models\Election;
use App\Models\Round;
use App\Services\ElectionStatisticsService;
use Illuminate\Http\Request;

class PublicStatisticsController extends Controller
{
    /**
     * Tampilkan halaman statistik publik
     */
    public function index(Request $request)
    {
        // Ambil election yang active atau finished
        $elections = Election::whereIn('status', ['active', 'finished'])
            ->orderByDesc('created_at')
            ->get();

        // Default: election pertama
        $selectedElectionId = $request->get('election_id', $elections->first()?->id);
        $selectedElection = Election::find($selectedElectionId);

        if (!$selectedElection) {
            return view('public-statistics.index', [
                'elections' => $elections,
                'selectedElection' => null,
                'rounds' => collect([]),
                'statistics' => null,
                'winner' => null,
            ]);
        }

        // Ambil semua round dari election ini (yang sudah completed)
        $rounds = Round::where('election_id', $selectedElection->id)
            ->whereIn('status', ['completed', 'invalid'])
            ->orderBy('round_number')
            ->get();

        // Default: round terakhir
        $selectedRoundId = $request->get('round_id', $rounds->last()?->id);
        $selectedRound = Round::find($selectedRoundId);

        $statistics = null;
        if ($selectedRound) {
            $service = new ElectionStatisticsService();
            $statistics = $service->getRoundStatistics($selectedRound);
        }

        // Get winner jika election finished
        $winner = null;
        if ($selectedElection->status === 'finished') {
            $service = new ElectionStatisticsService();
            $winner = $service->getWinner($selectedElection);
        }

        return view('public-statistics.index', [
            'elections' => $elections,
            'selectedElection' => $selectedElection,
            'rounds' => $rounds,
            'selectedRound' => $selectedRound,
            'statistics' => $statistics,
            'winner' => $winner,
        ]);
    }
}