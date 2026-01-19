<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah kolom untuk tracking eliminasi
     */
    public function up(): void
    {
        Schema::table('rounds', function (Blueprint $table) {
            // Flag untuk menandai apakah round ini sudah dilakukan eliminasi
            $table->boolean('is_eliminated')->default(false)->after('total_votes');
            
            // Timestamp kapan eliminasi dilakukan
            $table->timestamp('eliminated_at')->nullable()->after('is_eliminated');
        });
    }

    /**
     * Rollback migration
     */
    public function down(): void
    {
        Schema::table('rounds', function (Blueprint $table) {
            $table->dropColumn(['is_eliminated', 'eliminated_at']);
        });
    }
};