<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\Election;
use App\Models\Round;
use App\Models\Vote;
use Illuminate\Http\Request;

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
                'candidates' => [],
                'election' => null,
                'round' => null,
            ]);
        }

        // Validasi: Election harus active
        if ($activeRound->election->status !== 'active') {
            return view('voting.index', [
                'error' => 'Pemilihan belum aktif atau sudah selesai.',
                'candidates' => [],
                'election' => null,
                'round' => null,
            ]);
        }

        // Ambil semua calon yang aktif untuk election ini
        $candidates = Candidate::where('election_id', $activeRound->election_id)
            ->where('status', 'active')
            ->get();

        // Jika tidak ada calon aktif
        if ($candidates->isEmpty()) {
            return view('voting.index', [
                'error' => 'Tidak ada calon yang tersedia untuk voting.',
                'candidates' => [],
                'election' => $activeRound->election,
                'round' => $activeRound,
            ]);
        }

        return view('voting.index', [
            'error' => null,
            'candidates' => $candidates,
            'election' => $activeRound->election,
            'round' => $activeRound,
        ]);
    }

    /**
     * Proses submit vote
     */
    public function store(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'candidate_id' => 'required|exists:candidates,id',
            'voter_name' => 'nullable|string|max:255',
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

        // Validasi: Candidate harus dari election yang sama dan aktif
        $candidate = Candidate::where('id', $validated['candidate_id'])
            ->where('election_id', $activeRound->election_id)
            ->where('status', 'active')
            ->first();

        if (!$candidate) {
            return redirect()->route('voting.index')
                ->with('error', 'Calon yang dipilih tidak valid.');
        }

        // Simpan vote
        Vote::create([
            'election_id' => $activeRound->election_id,
            'round_id' => $activeRound->id,
            'candidate_id' => $candidate->id,
            'voter_name' => $validated['voter_name'],
        ]);

        // Update total_votes di round
        $activeRound->increment('total_votes');

        // Redirect dengan pesan sukses
        return redirect()->route('voting.index')
            ->with('success', 'Terima kasih! Suara Anda telah tersimpan. Silakan berganti peserta.');
    }
}