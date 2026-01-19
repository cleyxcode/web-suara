<?php

namespace App\Console\Commands;

use App\Models\Round;
use Illuminate\Console\Command;

class SyncRoundTotalVotes extends Command
{
    protected $signature = 'rounds:sync-votes';
    protected $description = 'Sync total_votes untuk semua round';

    public function handle()
    {
        $rounds = Round::all();

        foreach ($rounds as $round) {
            $totalVotes = $round->votes()->count();
            $round->update(['total_votes' => $totalVotes]);
            
            $this->info("Round {$round->id}: {$totalVotes} votes synced");
        }

        $this->info('✅ All rounds synced!');
        return 0;
    }
}