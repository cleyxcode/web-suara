<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vote extends Model
{
    use HasFactory;

    // Hanya gunakan created_at, tidak ada updated_at
    const UPDATED_AT = null;

    /**
     * Kolom yang boleh diisi secara mass-assignment
     */
    protected $fillable = [
        'election_id',
        'round_id',
        'candidate_id',
        'participant_id',
        'voter_name',
    ];

    /**
     * Casting tipe data untuk kolom tertentu
     */
    protected $casts = [
        'election_id' => 'integer',
        'round_id' => 'integer',
        'candidate_id' => 'integer',
        'participant_id' => 'integer',
        'created_at' => 'datetime',
    ];

    /**
     * Relasi: Vote milik satu Election
     */
    public function election(): BelongsTo
    {
        return $this->belongsTo(Election::class);
    }

    /**
     * Relasi: Vote milik satu Round
     */
    public function round(): BelongsTo
    {
        return $this->belongsTo(Round::class);
    }

    /**
     * Relasi: Vote milik satu Candidate
     */
    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }
}