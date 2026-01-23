<?php

namespace Database\Seeders;

use App\Models\Participant;
use Illuminate\Database\Seeder;

class ParticipantSeeder extends Seeder
{
    public function run(): void
    {
        // Sesuaikan dengan jumlah total_participants di election Anda
        $participants = [
            'John Doe',
            'Jane Smith',
            'Michael Johnson',
            'Sarah Williams',
            'David Brown',
            'Emily Davis',
            'Robert Miller',
            'Jennifer Wilson',
            'William Moore',
            'Jessica Taylor',
            'James Anderson',
            'Mary Thomas',
            'Christopher Jackson',
            'Patricia White',
            'Daniel Harris',
            'Linda Martin',
            'Matthew Thompson',
            'Elizabeth Garcia',
            'Anthony Martinez',
            'Barbara Robinson',
        ];

        foreach ($participants as $name) {
            Participant::create(['name' => $name]);
        }

        $this->command->info('✅ ' . count($participants) . ' participants created!');
    }
}