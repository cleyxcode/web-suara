<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migration untuk membuat table candidates
     */
    public function up(): void
    {
        Schema::create('candidates', function (Blueprint $table) {
            $table->id();
            
            // Foreign key ke table elections
            $table->foreignId('election_id')
                ->constrained('elections')
                ->onDelete('cascade'); // Jika election dihapus, candidate ikut terhapus
            
            $table->string('name'); // Nama calon
            $table->string('photo')->nullable(); // Path foto (opsional)
            $table->enum('status', ['active', 'eliminated'])->default('active'); // Status calon
            
            $table->timestamps(); // created_at & updated_at
        });
    }

    /**
     * Rollback migration (hapus table candidates)
     */
    public function down(): void
    {
        Schema::dropIfExists('candidates');
    }
};