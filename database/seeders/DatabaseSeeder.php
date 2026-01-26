<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
    

        // 🔥 PANGGIL SEEDER PESERTA
        $this->call([
            ElectionSeeder::class,
            ParticipantSeeder::class,
            CandidateSeeder::class,
        ]);
    }
}
