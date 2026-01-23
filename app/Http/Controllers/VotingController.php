<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\Election;
use App\Models\Participant;
use App\Models\Round;
use App\Models\Vote;
use App\Models\VotedParticipant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VotingController extends Controller
{
    /**
     * Display voting page using Livewire
     */
    public function index()
    {
        // Return view with Livewire component
        return view('voting.index-livewire');
    }

    /**
     * Legacy store method (for backward compatibility)
     * Bisa dihapus jika sudah full menggunakan Livewire
     */
    public function store(Request $request)
    {
        $request->validate([
            'participant_id' => 'required|exists:participants,id',
            'candidate_id' => 'required|exists:candidates,id',
        ]);

        try {
            DB::beginTransaction();

            // Ambil active round
            $round = Round::where('status', 'active')->first();

            if (!$round) {
                return back()->with('error', 'Tidak ada putaran yang sedang aktif saat ini.');
            }

            // Cek apakah participant sudah vote di round ini
            $alreadyVoted = VotedParticipant::where('round_id', $round->id)
                ->where('participant_id', $request->participant_id)
                ->exists();

            if ($alreadyVoted) {
                return back()->with('error', 'Anda sudah memberikan suara di putaran ini!');
            }

            // Ambil nama participant
            $participant = Participant::find($request->participant_id);
            
            // Validasi candidate aktif
            $candidate = Candidate::where('id', $request->candidate_id)
                ->where('status', 'active')
                ->first();

            if (!$candidate) {
                return back()->with('error', 'Calon yang dipilih sudah tidak aktif.');
            }

            // Simpan vote
            Vote::create([
                'election_id' => $round->election_id,
                'round_id' => $round->id,
                'candidate_id' => $request->candidate_id,
                'participant_id' => $request->participant_id,
                'voter_name' => $participant->name,
            ]);

            // Tandai participant sudah vote
            VotedParticipant::create([
                'round_id' => $round->id,
                'participant_id' => $request->participant_id,
                'voted_at' => now(),
            ]);

            // Update total votes di round
            $totalVotes = Vote::where('round_id', $round->id)->count();
            $round->update(['total_votes' => $totalVotes]);

            DB::commit();

            return back()->with('success', "✅ Terima kasih! Suara Anda untuk {$candidate->name} telah berhasil disimpan.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}