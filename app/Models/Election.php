<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Election extends Model
{
    use HasFactory;

    /**
     * Kolom yang boleh diisi secara mass-assignment
     */
    protected $fillable = [
        'name',
        'total_participants',
        'status',
        'elimination_threshold',
    ];

    /**
     * Casting tipe data untuk kolom tertentu
     */
    protected $casts = [
        'total_participants' => 'integer',
        'elimination_threshold' => 'integer',
    ];

    /**
     * Relasi: Satu Election memiliki banyak Candidate
     */
    public function candidates(): HasMany
    {
        return $this->hasMany(Candidate::class);
    }

    /**
     * Relasi: Satu Election memiliki banyak Round
     */
    public function rounds(): HasMany
    {
        return $this->hasMany(Round::class);
    }

    /**
     * Relasi: Satu Election memiliki banyak Vote
     */
    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }
}