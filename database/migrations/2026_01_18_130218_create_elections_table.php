<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migration untuk membuat table elections
     */
    public function up(): void
    {
        Schema::create('elections', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama pemilihan
            $table->integer('total_participants'); // Jumlah total peserta
            $table->enum('status', ['draft', 'active', 'finished'])->default('draft'); // Status pemilihan
            $table->timestamps(); // created_at & updated_at
        });
    }

    /**
     * Rollback migration (hapus table elections)
     */
    public function down(): void
    {
        Schema::dropIfExists('elections');
    }
};