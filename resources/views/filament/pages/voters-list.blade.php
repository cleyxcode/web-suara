<x-filament-panels::page>
    <div class="space-y-6">
        
        <!-- Filter Section -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-bold mb-4">Filter Data</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Select Election -->
                <div>
                    <label class="block text-sm font-medium mb-2">Pilih Pemilihan</label>
                    <select 
                        wire:model.live="selectedElectionId"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700"
                    >
                        <option value="">-- Pilih Pemilihan --</option>
                        @foreach($this->getElections() as $election)
                            <option value="{{ $election->id }}">{{ $election->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Select Round -->
                <div>
                    <label class="block text-sm font-medium mb-2">Pilih Putaran</label>
                    <select 
                        wire:model.live="selectedRoundId"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700"
                        @if(!$selectedElectionId) disabled @endif
                    >
                        <option value="">-- Pilih Putaran --</option>
                        @foreach($this->getRounds() as $round)
                            <option value="{{ $round->id }}">
                                Putaran {{ $round->round_number }} 
                                ({{ ucfirst($round->status) }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        @if($selectedRoundId)
            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                @php
                    $votersByCandidate = $this->getVotersByCandidate();
                    $totalVotes = $votersByCandidate->sum('total_votes');
                    $totalCandidates = $votersByCandidate->count();
                    $round = \App\Models\Round::find($selectedRoundId);
                    $election = $round?->election;
                @endphp

                <div class="bg-blue-50 dark:bg-blue-900 rounded-lg p-4">
                    <div class="text-sm text-blue-600 dark:text-blue-300">Total Kandidat</div>
                    <div class="text-2xl font-bold text-blue-700 dark:text-blue-200">{{ $totalCandidates }}</div>
                </div>

                <div class="bg-green-50 dark:bg-green-900 rounded-lg p-4">
                    <div class="text-sm text-green-600 dark:text-green-300">Total Suara</div>
                    <div class="text-2xl font-bold text-green-700 dark:text-green-200">{{ $totalVotes }}</div>
                </div>

                <div class="bg-purple-50 dark:bg-purple-900 rounded-lg p-4">
                    <div class="text-sm text-purple-600 dark:text-purple-300">Target Peserta</div>
                    <div class="text-2xl font-bold text-purple-700 dark:text-purple-200">
                        {{ $election?->total_participants ?? 0 }}
                    </div>
                </div>

                <div class="bg-orange-50 dark:bg-orange-900 rounded-lg p-4">
                    <div class="text-sm text-orange-600 dark:text-orange-300">Partisipasi</div>
                    <div class="text-2xl font-bold text-orange-700 dark:text-orange-200">
                        {{ $election && $election->total_participants > 0 ? number_format(($totalVotes / $election->total_participants) * 100, 1) : 0 }}%
                    </div>
                </div>
            </div>

            <!-- Voters by Candidate -->
            <div class="space-y-4">
                @foreach($votersByCandidate as $candidateData)
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
                        <!-- Candidate Header -->
                        <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-6">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    @if($candidateData['candidate_photo'])
                                        <img 
                                            src="{{ Storage::url($candidateData['candidate_photo']) }}" 
                                            alt="{{ $candidateData['candidate_name'] }}"
                                            class="w-16 h-16 rounded-full border-4 border-white shadow-lg object-cover"
                                        >
                                    @else
                                        <div class="w-16 h-16 rounded-full bg-white flex items-center justify-center border-4 border-white shadow-lg">
                                            <svg class="w-10 h-10 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                    @endif
                                    
                                    <div>
                                        <h3 class="text-2xl font-bold text-white">
                                            {{ $candidateData['candidate_name'] }}
                                        </h3>
                                        <div class="flex items-center space-x-2 mt-1">
                                            <span class="px-3 py-1 bg-white/20 backdrop-blur rounded-full text-white text-sm font-semibold">
                                                {{ $candidateData['total_votes'] }} Suara
                                            </span>
                                            @if($candidateData['candidate_status'] === 'eliminated')
                                                <span class="px-3 py-1 bg-red-500 rounded-full text-white text-sm font-semibold">
                                                    ❌ Tersingkir
                                                </span>
                                            @else
                                                <span class="px-3 py-1 bg-green-500 rounded-full text-white text-sm font-semibold">
                                                    ✅ Aktif
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                @if($election && $election->total_participants > 0)
                                    <div class="text-right">
                                        <div class="text-white/80 text-sm">Persentase</div>
                                        <div class="text-3xl font-bold text-white">
                                            {{ number_format(($candidateData['total_votes'] / $election->total_participants) * 100, 1) }}%
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Voters List -->
                        <div class="p-6">
                            @if($candidateData['voters']->count() > 0)
                                <h4 class="font-bold text-gray-700 dark:text-gray-300 mb-3">
                                    📋 Daftar Pemilih ({{ $candidateData['voters']->count() }} orang)
                                </h4>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                    @foreach($candidateData['voters'] as $index => $voter)
                                        <div class="flex items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition">
                                            <div class="flex-shrink-0 w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center font-bold text-sm mr-3">
                                                {{ $index + 1 }}
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="font-semibold text-gray-900 dark:text-white truncate">
                                                    {{ $voter['voter_name'] }}
                                                </p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $voter['voted_at']->format('d M Y, H:i') }}
                                                </p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8 text-gray-500">
                                    <svg class="w-16 h-16 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    <p class="font-semibold">Belum ada pemilih</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Detailed Table -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-bold">📊 Tabel Detail Semua Pemilih</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        Gunakan filter dan pencarian untuk menemukan pemilih tertentu
                    </p>
                </div>
                
                <div class="p-6">
                    {{ $this->table }}
                </div>
            </div>
        @else
            <div class="bg-yellow-50 dark:bg-yellow-900 border-l-4 border-yellow-400 p-6 rounded-lg">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-300 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <p class="font-semibold text-yellow-800 dark:text-yellow-200">
                        Silakan pilih pemilihan dan putaran terlebih dahulu
                    </p>
                </div>
            </div>
        @endif

    </div>
</x-filament-panels::page>