<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migration untuk membuat table rounds
     */
    public function up(): void
    {
        Schema::create('rounds', function (Blueprint $table) {
            $table->id();
            
            // Foreign key ke table elections
            $table->foreignId('election_id')
                ->constrained('elections')
                ->onDelete('cascade'); // Jika election dihapus, round ikut terhapus
            
            $table->integer('round_number'); // Nomor putaran (1, 2, 3, ...)
            
            $table->enum('status', ['draft', 'active', 'invalid', 'completed'])
                ->default('draft'); // Status putaran
            
            $table->integer('total_votes')->default(0); // Placeholder untuk total suara
            
            $table->timestamps(); // created_at & updated_at
            
            // Unique constraint: 1 election tidak boleh punya 2 putaran dengan nomor sama
            $table->unique(['election_id', 'round_number']);
        });
    }

    /**
     * Rollback migration (hapus table rounds)
     */
    public function down(): void
    {
        Schema::dropIfExists('rounds');
    }
};