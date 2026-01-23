<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\Participant;
use App\Models\Round;
use App\Models\Vote;
use App\Models\VotedParticipant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VotingController extends Controller
{
    /**
     * Tampilkan halaman voting
     */
    public function index()
    {
        // Cari round yang aktif
        $activeRound = Round::where('status', 'active')
            ->with(['election'])
            ->first();

        // Jika tidak ada round aktif
        if (!$activeRound) {
            return view('voting.index', [
                'error' => 'Tidak ada putaran voting yang aktif saat ini.',
                'participants' => [],
                'candidates' => [],
                'election' => null,
                'round' => null,
                'statistics' => null,
            ]);
        }

        // Validasi: Election harus active
        if ($activeRound->election->status !== 'active') {
            return view('voting.index', [
                'error' => 'Pemilihan belum aktif atau sudah selesai.',
                'participants' => [],
                'candidates' => [],
                'election' => null,
                'round' => null,
                'statistics' => null,
            ]);
        }

        // Ambil participants yang BELUM vote di round ini
        $participants = Participant::availableForRound($activeRound->id)->get();

        // Ambil semua calon yang aktif untuk election ini
        $candidates = Candidate::where('election_id', $activeRound->election_id)
            ->where('status', 'active')
            ->get();

        // Jika tidak ada calon aktif
        if ($candidates->isEmpty()) {
            return view('voting.index', [
                'error' => 'Tidak ada calon yang tersedia untuk voting.',
                'participants' => $participants,
                'candidates' => [],
                'election' => $activeRound->election,
                'round' => $activeRound,
                'statistics' => null,
            ]);
        }

        // Statistik
        $totalParticipants = Participant::count();
        $votedCount = VotedParticipant::where('round_id', $activeRound->id)->count();
        $remainingCount = $totalParticipants - $votedCount;

        $statistics = [
            'total_participants' => $totalParticipants,
            'voted_count' => $votedCount,
            'remaining_count' => $remainingCount,
            'participation_rate' => $totalParticipants > 0 
                ? round(($votedCount / $totalParticipants) * 100, 2) 
                : 0,
        ];

        return view('voting.index', [
            'error' => null,
            'participants' => $participants,
            'candidates' => $candidates,
            'election' => $activeRound->election,
            'round' => $activeRound,
            'statistics' => $statistics,
        ]);
    }

    /**
     * Proses submit vote
     */
    public function store(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'participant_id' => 'required|exists:participants,id',
            'candidate_id' => 'required|exists:candidates,id',
        ]);

        // Cari round yang aktif
        $activeRound = Round::where('status', 'active')->first();

        if (!$activeRound) {
            return redirect()->route('voting.index')
                ->with('error', 'Tidak ada putaran voting yang aktif.');
        }

        // Validasi: Election harus active
        if ($activeRound->election->status !== 'active') {
            return redirect()->route('voting.index')
                ->with('error', 'Pemilihan belum aktif atau sudah selesai.');
        }

        try {
            DB::beginTransaction();

            // Validasi: Participant belum vote di round ini
            $alreadyVoted = VotedParticipant::where('round_id', $activeRound->id)
                ->where('participant_id', $validated['participant_id'])
                ->exists();

            if ($alreadyVoted) {
                DB::rollBack();
                return redirect()->route('voting.index')
                    ->with('error', 'Peserta ini sudah memberikan suara di putaran ini.');
            }

            // Validasi: Candidate harus dari election yang sama dan aktif
            $candidate = Candidate::where('id', $validated['candidate_id'])
                ->where('election_id', $activeRound->election_id)
                ->where('status', 'active')
                ->first();

            if (!$candidate) {
                DB::rollBack();
                return redirect()->route('voting.index')
                    ->with('error', 'Calon yang dipilih tidak valid.');
            }

            // Get participant name untuk voter_name
            $participant = Participant::findOrFail($validated['participant_id']);

            // 1. Simpan vote
            Vote::create([
                'election_id' => $activeRound->election_id,
                'round_id' => $activeRound->id,
                'candidate_id' => $candidate->id,
                'participant_id' => $participant->id,
                'voter_name' => $participant->name, // Opsional, untuk backward compatibility
            ]);

            // 2. Record bahwa participant sudah vote
            VotedParticipant::create([
                'round_id' => $activeRound->id,
                'participant_id' => $participant->id,
                'voted_at' => now(),
            ]);

            // 3. Increment total_votes
            $activeRound->increment('total_votes');

            DB::commit();

            // Redirect dengan pesan sukses
            return redirect()->route('voting.index')
                ->with('success', 'Terima kasih, ' . $participant->name . '! Suara Anda telah tersimpan. Silakan berganti peserta.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->route('voting.index')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}