<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Participant extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    /**
     * Relasi: Participant punya banyak votes
     */
    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }

    /**
     * Relasi: Participant punya banyak voted records
     */
    public function votedRounds(): HasMany
    {
        return $this->hasMany(VotedParticipant::class);
    }

    /**
     * Cek apakah participant sudah vote di round tertentu
     */
    public function hasVotedInRound(int $roundId): bool
    {
        return $this->votedRounds()
            ->where('round_id', $roundId)
            ->exists();
    }

    /**
     * Scope: Participant yang belum vote di round tertentu
     */
    public function scopeAvailableForRound($query, int $roundId)
    {
        return $query->whereNotIn('id', function ($subQuery) use ($roundId) {
            $subQuery->select('participant_id')
                ->from('voted_participants')
                ->where('round_id', $roundId);
        })->orderBy('name');
    }
}