<?php

namespace Database\Seeders;

use App\Models\Candidate;
use Illuminate\Database\Seeder;

class CandidateSeeder extends Seeder
{
    public function run(): void
    {
        // Data kandidat dari RANCANGAN PESERTA KONFERENSI CABANG ISTIMEWA-I
        // 25 Januari 2025 - Peserta Biasa (39 orang)
        $candidates = [
            
            'Joseph R. Loiwatu',
            'Mario Soenarko',
            'Frangklien Lewerissa',
            'Steven Thenu',
            'Laura S. Nunumete',
            'Fitzgerald Toule',
            'Jozzy Dangeubun',
            'Maygie S. Amanupunnjo',
            'Eirene Sinay',
            'Gloria Renmaur',
            'Debby A. Talaperu',
            'Jenesis M. Titahelu',
            'Azri R. Nendissa',
            'Arkelino Hunihua',
            'Vira Talaperu',
            'Clovdia G. Hunihua',
            'Astrid Lelapary',
            'Hizkia Afdan',
            'Firstnoel Maukary',
            'Yulyans Mayaut',
            'Samuel R. Latupeirissa',
            'Loudrik Talabessy',
            'Veritas C. Ayal',
            'Stevanus Huwae',
            'Debby Souhoka',
            'Elsye Hunihua',
            'Hendrik Theis',
            'Iankov Latuheru',
            'Rani Putri Sormin',
            'Stephen Paulus',
            'Godelva Amanupunnjo',
            'Marvel Saununu',
            'Monic Mairuhu',
            'Dkn Ny F.J. Maukary/M',
            
            'Pnt P. M. Sahetapy',
            'Dkn. W. da Costa',
        ];

        // Asumsikan election_id = 1 (sesuaikan dengan ID election Anda)
        $electionId = 1;

        foreach ($candidates as $name) {
            Candidate::create([
                'election_id' => $electionId,
                'name' => $name,
                'photo' => null, // Atau bisa diisi path foto jika ada
                'status' => 'active',
            ]);
        }

        $this->command->info('✅ ' . count($candidates) . ' candidates created!');
    }
}