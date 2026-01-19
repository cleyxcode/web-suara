<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Round extends Model
{
    use HasFactory;

    /**
     * Kolom yang boleh diisi secara mass-assignment
     */
    protected $fillable = [
        'election_id',
        'round_number',
        'status',
        'total_votes',
        'is_eliminated',
        'eliminated_at',
    ];

    /**
     * Casting tipe data untuk kolom tertentu
     */
    protected $casts = [
        'election_id' => 'integer',
        'round_number' => 'integer',
        'total_votes' => 'integer',
        'is_eliminated' => 'boolean',
        'eliminated_at' => 'datetime',
    ];

    /**
     * Relasi: Round milik satu Election
     */
    public function election(): BelongsTo
    {
        return $this->belongsTo(Election::class);
    }

    /**
     * Relasi: Satu Round memiliki banyak Vote
     */
    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }

    /**
     * Boot method untuk auto-generate round_number
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-generate round_number saat create
        static::creating(function ($round) {
            if (!$round->round_number) {
                // Ambil round terakhir dari election ini
                $lastRound = static::where('election_id', $round->election_id)
                    ->orderBy('round_number', 'desc')
                    ->first();

                // Set round_number = last + 1, atau 1 jika belum ada
                $round->round_number = $lastRound ? $lastRound->round_number + 1 : 1;
            }
        });
    }
}