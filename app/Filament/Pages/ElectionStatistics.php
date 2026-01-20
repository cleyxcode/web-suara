<?php

namespace App\Filament\Pages;

use App\Models\Election;
use App\Models\Round;
use App\Services\ElectionStatisticsService;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;

class ElectionStatistics extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static string $view = 'filament.pages.election-statistics';

    protected static ?string $navigationLabel = 'Statistik & Grafik';

    protected static ?string $title = 'Statistik & Grafik Pemilihan';

    protected static ?int $navigationSort = 10;

    public ?int $selectedElectionId = null;
    public ?int $selectedRoundId = null;

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

    public function updatedSelectedElectionId($value): void
    {
        // Reset round selection saat election berubah
        if ($value) {
            $lastRound = Round::where('election_id', $value)
                ->orderByDesc('round_number')
                ->first();
            $this->selectedRoundId = $lastRound?->id;
        } else {
            $this->selectedRoundId = null;
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

    public function getStatistics()
    {
        if (!$this->selectedRoundId) {
            return null;
        }

        $round = Round::find($this->selectedRoundId);
        if (!$round) {
            return null;
        }

        $service = new ElectionStatisticsService();
        return $service->getRoundStatistics($round);
    }

    public function getLineChartData()
    {
        if (!$this->selectedElectionId) {
            return null;
        }

        $election = Election::find($this->selectedElectionId);
        if (!$election) {
            return null;
        }

        $service = new ElectionStatisticsService();
        return $service->getLineChartData($election);
    }

    public function getWinner()
    {
        if (!$this->selectedElectionId) {
            return null;
        }

        $election = Election::find($this->selectedElectionId);
        if (!$election) {
            return null;
        }

        $service = new ElectionStatisticsService();
        return $service->getWinner($election);
    }
}