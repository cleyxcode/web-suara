<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migration untuk membuat table votes
     */
    public function up(): void
    {
        Schema::create('votes', function (Blueprint $table) {
            $table->id();
            
            // Foreign key ke table elections
            $table->foreignId('election_id')
                ->constrained('elections')
                ->onDelete('cascade');
            
            // Foreign key ke table rounds
            $table->foreignId('round_id')
                ->constrained('rounds')
                ->onDelete('cascade');
            
            // Foreign key ke table candidates
            $table->foreignId('candidate_id')
                ->constrained('candidates')
                ->onDelete('cascade');
            
            // Nama pemilih (opsional)
            $table->string('voter_name')->nullable();
            
            $table->timestamp('created_at')->useCurrent(); // Waktu voting
        });
    }

    /**
     * Rollback migration (hapus table votes)
     */
    public function down(): void
    {
        Schema::dropIfExists('votes');
    }
};