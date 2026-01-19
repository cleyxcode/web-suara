<?php

namespace App\Observers;

use App\Models\Vote;

class VoteObserver
{
    /**
     * Handle the Vote "created" event.
     */
    public function created(Vote $vote): void
    {
        // Increment total_votes di round
        $vote->round->increment('total_votes');
    }

    /**
     * Handle the Vote "deleted" event.
     */
    public function deleted(Vote $vote): void
    {
        // Decrement total_votes di round jika vote dihapus
        $vote->round->decrement('total_votes');
    }
}