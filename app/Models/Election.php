<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
    ];

    /**
     * Casting tipe data untuk kolom tertentu
     */
    protected $casts = [
        'total_participants' => 'integer',
    ];
}