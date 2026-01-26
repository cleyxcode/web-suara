<?php

namespace Database\Seeders;

use App\Models\Election;
use Illuminate\Database\Seeder;

class ElectionSeeder extends Seeder
{
    public function run(): void
    {
        Election::create([
            'name' => 'Konferensi Cabang Istimewa-I',
            'total_participants' => 39,
            'status' => 'active',
            // Hapus atau sesuaikan field yang tidak ada di tabel
            // 'election_date' => '2025-01-25', // ← hapus jika tidak ada
        ]);

        $this->command->info('✅ Election created!');
    }
}