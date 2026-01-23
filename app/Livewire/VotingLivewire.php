<?php

namespace App\Livewire;

use App\Models\Candidate;
use App\Models\Election;
use App\Models\Participant;
use App\Models\Round;
use App\Models\Vote;
use App\Models\VotedParticipant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class VotingLivewire extends Component
{
    public $participantId;
    public $candidateId;
    public $successMessage;
    public $errorMessage;
    public $isSubmitting = false;

    public function mount()
    {
        $this->successMessage = session('success');
        $this->errorMessage = session('error');
    }

    public function submitVote()
    {
        $this->isSubmitting = true;
        $this->errorMessage = null;
        $this->successMessage = null;

        $this->validate([
            'participantId' => 'required|exists:participants,id',
            'candidateId' => 'required|exists:candidates,id',
        ], [
            'participantId.required' => 'Silakan pilih nama Anda',
            'participantId.exists' => 'Nama peserta tidak valid',
            'candidateId.required' => 'Silakan pilih calon',
            'candidateId.exists' => 'Calon tidak valid',
        ]);

        try {
            DB::beginTransaction();

            $round = Round::where('status', 'active')->first();

            if (!$round) {
                throw new \Exception('Tidak ada putaran yang sedang aktif saat ini.');
            }

            $alreadyVoted = VotedParticipant::where('round_id', $round->id)
                ->where('participant_id', $this->participantId)
                ->exists();

            if ($alreadyVoted) {
                throw new \Exception('Anda sudah memberikan suara di putaran ini!');
            }

            $participant = Participant::find($this->participantId);
            
            $candidate = Candidate::where('id', $this->candidateId)
                ->where('status', 'active')
                ->first();

            if (!$candidate) {
                throw new \Exception('Calon yang dipilih sudah tidak aktif.');
            }

            Vote::create([
                'election_id' => $round->election_id,
                'round_id' => $round->id,
                'candidate_id' => $this->candidateId,
                'participant_id' => $this->participantId,
                'voter_name' => $participant->name,
            ]);

            VotedParticipant::create([
                'round_id' => $round->id,
                'participant_id' => $this->participantId,
                'voted_at' => now(),
            ]);

            $totalVotes = Vote::where('round_id', $round->id)->count();
            $round->update(['total_votes' => $totalVotes]);

            DB::commit();

            $this->successMessage = "✅ Terima kasih! Suara Anda untuk {$candidate->name} telah berhasil disimpan.";
            
            $this->participantId = null;
            $this->candidateId = null;

            $this->dispatch('voteSubmitted');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->errorMessage = $e->getMessage();
        }

        $this->isSubmitting = false;
    }

    public function render()
    {
        $round = Round::where('status', 'active')
            ->with('election')
            ->first();

        // Debug: Log round info
        Log::info('Active Round:', ['round' => $round ? $round->toArray() : null]);

        if (!$round) {
            return view('livewire.voting-livewire', [
                'election' => null,
                'round' => null,
                'participants' => collect([]),
                'candidates' => collect([]),
                'statistics' => null,
                'error' => 'Tidak ada putaran yang sedang aktif saat ini.',
            ]);
        }

        $election = $round->election;
        
        // Debug: Log election info
        Log::info('Election:', ['election' => $election ? $election->toArray() : null]);

        $participants = Participant::availableForRound($round->id)->get();
        
        // Debug: Log participants
        Log::info('Available Participants:', ['count' => $participants->count()]);

        $candidates = Candidate::where('election_id', $election->id)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        // Debug: Log candidates
        Log::info('Active Candidates:', [
            'count' => $candidates->count(),
            'candidates' => $candidates->pluck('name', 'id')->toArray()
        ]);

        $totalParticipants = $election->total_participants;
        $votedCount = VotedParticipant::where('round_id', $round->id)->count();
        $remainingCount = $totalParticipants - $votedCount;
        $participationRate = $totalParticipants > 0 
            ? round(($votedCount / $totalParticipants) * 100, 1) 
            : 0;

        $statistics = [
            'total_participants' => $totalParticipants,
            'voted_count' => $votedCount,
            'remaining_count' => $remainingCount,
            'participation_rate' => $participationRate,
        ];

        // Debug: Log statistics
        Log::info('Statistics:', $statistics);

        return view('livewire.voting-livewire', [
            'election' => $election,
            'round' => $round,
            'participants' => $participants,
            'candidates' => $candidates,
            'statistics' => $statistics,
            'error' => null,
        ]);
    }
}