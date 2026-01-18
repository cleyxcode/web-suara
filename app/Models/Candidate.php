<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
    ];

    /**
     * Casting tipe data untuk kolom tertentu
     */
    protected $casts = [
        'election_id' => 'integer',
    ];

    /**
     * Relasi: Candidate milik satu Election
     */
    public function election(): BelongsTo
    {
        return $this->belongsTo(Election::class);
    }
}