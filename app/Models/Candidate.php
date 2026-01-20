<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Candidate extends Model
{
    use HasFactory;

    /**
     * Kolom yang boleh diisi secara mass-assignment
     */
    protected $fillable = [
        'election_id',
        'name',
        'photo',
        'status',
        'eliminated_at_round_id',
    ];

    /**
     * Casting tipe data untuk kolom tertentu
     */
    protected $casts = [
        'election_id' => 'integer',
        'eliminated_at_round_id' => 'integer',
    ];

    /**
     * Relasi: Candidate milik satu Election
     */
    public function election(): BelongsTo
    {
        return $this->belongsTo(Election::class);
    }

    /**
     * Relasi: Satu Candidate memiliki banyak Vote
     */
    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }
}