<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('voted_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('round_id')->constrained('rounds')->onDelete('cascade');
            $table->foreignId('participant_id')->constrained('participants')->onDelete('cascade');
            $table->timestamp('voted_at')->useCurrent();
            
            // UNIQUE: Satu participant hanya bisa vote 1x per round
            $table->unique(['round_id', 'participant_id']);
            $table->index(['round_id', 'participant_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('voted_participants');
    }
};