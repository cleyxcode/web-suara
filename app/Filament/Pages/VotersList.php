<?php

namespace App\Filament\Pages;

use App\Models\Candidate;
use App\Models\Election;
use App\Models\Round;
use App\Models\Vote;
use Filament\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class VotersList extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    
    protected static string $view = 'filament.pages.voters-list';
    
    protected static ?string $navigationLabel = 'Daftar Pemilih';
    
    protected static ?string $title = 'Daftar Pemilih per Kandidat';
    
    protected static ?int $navigationSort = 11;

    // Custom URL (opsional)
    protected static string $routePath = 'daftar-pemilih';

    public ?int $selectedElectionId = null;
    public ?int $selectedRoundId = null;
    public ?int $selectedCandidateId = null;

    public function mount(): void
    {
        // Default: pilih election pertama
        $firstElection = Election::first();
        $this->selectedElectionId = $firstElection?->id;

        // Default: pilih round terakhir dari election
        if ($this->selectedElectionId) {
            $lastRound = Round::where('election_id', $this->selectedElectionId)
                ->orderByDesc('round_number')
                ->first();
            $this->selectedRoundId = $lastRound?->id;
        }
    }

    public function getElections()
    {
        return Election::orderByDesc('created_at')->get();
    }

    public function getRounds()
    {
        if (!$this->selectedElectionId) {
            return collect([]);
        }

        return Round::where('election_id', $this->selectedElectionId)
            ->orderBy('round_number')
            ->get();
    }

    public function getCandidates()
    {
        if (!$this->selectedElectionId) {
            return collect([]);
        }

        return Candidate::where('election_id', $this->selectedElectionId)
            ->orderBy('name')
            ->get();
    }

    public function getVotersByCandidate()
    {
        if (!$this->selectedRoundId) {
            return collect([]);
        }

        $round = Round::find($this->selectedRoundId);
        if (!$round) {
            return collect([]);
        }

        // Get all candidates for this election
        $candidates = Candidate::where('election_id', $round->election_id)
            ->with(['votes' => function ($query) use ($round) {
                $query->where('round_id', $round->id)
                      ->orderBy('created_at', 'asc');
            }])
            ->get();

        $result = [];

        foreach ($candidates as $candidate) {
            $voters = $candidate->votes->map(function ($vote) {
                return [
                    'voter_name' => $vote->voter_name,
                    'voted_at' => $vote->created_at,
                ];
            });

            $result[] = [
                'candidate_id' => $candidate->id,
                'candidate_name' => $candidate->name,
                'candidate_photo' => $candidate->photo,
                'candidate_status' => $candidate->status,
                'total_votes' => $voters->count(),
                'voters' => $voters,
            ];
        }

        // Sort by total votes descending
        usort($result, function ($a, $b) {
            return $b['total_votes'] <=> $a['total_votes'];
        });

        return collect($result);
    }

    public function updatedSelectedElectionId($value): void
    {
        if ($value) {
            $lastRound = Round::where('election_id', $value)
                ->orderByDesc('round_number')
                ->first();
            $this->selectedRoundId = $lastRound?->id;
        } else {
            $this->selectedRoundId = null;
        }
        $this->selectedCandidateId = null;
    }

    public function updatedSelectedRoundId($value): void
    {
        $this->selectedCandidateId = null;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Vote::query()
                    ->when($this->selectedRoundId, fn (Builder $query) => 
                        $query->where('round_id', $this->selectedRoundId)
                    )
                    ->when($this->selectedCandidateId, fn (Builder $query) => 
                        $query->where('candidate_id', $this->selectedCandidateId)
                    )
            )
            ->columns([
                TextColumn::make('voter_name')
                    ->label('Nama Pemilih')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('candidate.name')
                    ->label('Memilih')
                    ->badge()
                    ->color('success')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Waktu Vote')
                    ->dateTime('d M Y, H:i:s')
                    ->sortable()
                    ->description(fn (Vote $record) => $record->created_at->diffForHumans()),
            ])
            ->filters([
                SelectFilter::make('candidate_id')
                    ->label('Filter Kandidat')
                    ->options(
                        Candidate::when($this->selectedElectionId, fn ($query) => 
                            $query->where('election_id', $this->selectedElectionId)
                        )
                        ->pluck('name', 'id')
                    )
                    ->searchable()
                    ->preload()
                    ->native(false),
            ])
            ->defaultSort('created_at', 'asc');
    }
}