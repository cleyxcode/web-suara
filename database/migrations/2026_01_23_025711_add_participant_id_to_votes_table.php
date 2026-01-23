<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('votes', function (Blueprint $table) {
            // Tambah participant_id setelah round_id
            $table->foreignId('participant_id')
                ->nullable() // Nullable dulu untuk data lama
                ->after('round_id')
                ->constrained('participants')
                ->onDelete('cascade');
            
            // Unique constraint
            $table->unique(['round_id', 'participant_id'], 'unique_vote_per_round');
        });
    }

    public function down(): void
    {
        Schema::table('votes', function (Blueprint $table) {
            $table->dropForeign(['participant_id']);
            $table->dropUnique('unique_vote_per_round');
            $table->dropColumn('participant_id');
        });
    }
};