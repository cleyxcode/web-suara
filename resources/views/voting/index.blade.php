<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voting - {{ $election->name ?? 'Pemilihan' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen py-12 px-4">
        <div class="max-w-4xl mx-auto">
            
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-lg p-8 mb-6">
                <div class="text-center">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">
                        Pemilihan Ketua Ranting Gereja
                    </h1>
                    @if($election)
                        <p class="text-xl text-gray-600 mb-2">{{ $election->name }}</p>
                        <div class="inline-block bg-blue-100 text-blue-800 px-4 py-2 rounded-full text-sm font-semibold">
                            Putaran {{ $round->round_number }}
                        </div>
                    @endif
                </div>
            </div>

            @if($statistics)
                <!-- Statistics -->
                <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                    <h3 class="text-lg font-bold mb-4">📊 Statistik Voting Real-Time</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="text-center p-4 bg-gray-50 rounded-lg">
                            <p class="text-sm text-gray-500">Total Peserta</p>
                            <p class="text-2xl font-bold text-blue-600">{{ $statistics['total_participants'] }}</p>
                        </div>
                        <div class="text-center p-4 bg-green-50 rounded-lg">
                            <p class="text-sm text-gray-500">Sudah Vote</p>
                            <p class="text-2xl font-bold text-green-600">{{ $statistics['voted_count'] }}</p>
                        </div>
                        <div class="text-center p-4 bg-orange-50 rounded-lg">
                            <p class="text-sm text-gray-500">Belum Vote</p>
                            <p class="text-2xl font-bold text-orange-600">{{ $statistics['remaining_count'] }}</p>
                        </div>
                        <div class="text-center p-4 bg-purple-50 rounded-lg">
                            <p class="text-sm text-gray-500">Partisipasi</p>
                            <p class="text-2xl font-bold text-purple-600">{{ $statistics['participation_rate'] }}%</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Alert Error -->
            @if(session('error') || $error)
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <p class="font-semibold">{{ session('error') ?? $error }}</p>
                    </div>
                </div>
            @endif

            <!-- Alert Success -->
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <p class="font-semibold">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            <!-- Form Voting -->
            @if($participants && $participants->count() > 0 && $candidates && $candidates->count() > 0)
                <form action="{{ route('voting.store') }}" method="POST" id="votingForm">
                    @csrf

                    <!-- Step 1: Pilih Nama Peserta -->
                    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                        <label class="block text-lg font-bold text-gray-700 mb-3">
                            <span class="bg-blue-600 text-white rounded-full w-8 h-8 inline-flex items-center justify-center mr-2">1</span>
                            Pilih Nama Anda
                        </label>
                        <select 
                            name="participant_id" 
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                            <option value="">-- Pilih Nama Anda --</option>
                            @foreach($participants as $participant)
                                <option value="{{ $participant->id }}">{{ $participant->name }}</option>
                            @endforeach
                        </select>
                        <p class="text-sm text-gray-500 mt-2">
                            ⚠️ Pastikan Anda memilih nama Anda sendiri. Peserta yang tersisa: <strong>{{ $participants->count() }} orang</strong>
                        </p>
                    </div>

                    <!-- Step 2: Pilih Calon -->
                    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                        <label class="block text-lg font-bold text-gray-700 mb-4">
                            <span class="bg-blue-600 text-white rounded-full w-8 h-8 inline-flex items-center justify-center mr-2">2</span>
                            Pilih Calon Ketua
                        </label>
                        
                        <div class="space-y-4">
                            @foreach($candidates as $candidate)
                                <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-500 hover:bg-blue-50 transition-all">
                                    <input 
                                        type="radio" 
                                        name="candidate_id" 
                                        value="{{ $candidate->id }}" 
                                        class="w-5 h-5 text-blue-600 focus:ring-2 focus:ring-blue-500"
                                        required
                                    >
                                    <div class="ml-4 flex items-center flex-1">
                                        @if($candidate->photo)
                                            <img 
                                                src="{{ Storage::url($candidate->photo) }}" 
                                                alt="{{ $candidate->name }}"
                                                class="w-16 h-16 rounded-full object-cover mr-4"
                                            >
                                        @else
                                            <div class="w-16 h-16 rounded-full bg-gray-300 flex items-center justify-center mr-4">
                                                <svg class="w-8 h-8 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                        @endif
                                        <span class="text-lg font-semibold text-gray-900">
                                            {{ $candidate->name }}
                                        </span>
                                    </div>
                                </label>
                            @endforeach
                        </div>

                        @error('candidate_id')
                            <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="text-center">
                        <button 
                            type="submit"
                            id="submitBtn"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 px-12 rounded-lg text-lg shadow-lg hover:shadow-xl transition-all transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            🗳️ KIRIM SUARA
                        </button>
                    </div>
                </form>
            @elseif($participants && $participants->count() === 0)
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-6 rounded-lg text-center">
                    <svg class="w-16 h-16 mx-auto mb-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <p class="text-xl font-bold mb-2">✅ Semua Peserta Sudah Memberikan Suara!</p>
                    <p class="text-sm">Silakan tunggu admin menutup putaran ini.</p>
                </div>
            @endif

            <!-- Footer Info -->
            <div class="text-center mt-8 text-sm text-gray-600">
                <p>Sistem Pemilihan Ketua Ranting Gereja</p>
                <p class="mt-1">Voting tanpa login - Satu suara per peserta per putaran</p>
            </div>

        </div>
    </div>

    <!-- Prevent double submit & Auto-hide success -->
    <script>
        const form = document.getElementById('votingForm');
        const submitBtn = document.getElementById('submitBtn');
        
        if (form) {
            form.addEventListener('submit', function() {
                submitBtn.disabled = true;
                submitBtn.textContent = 'MENGIRIM...';
            });
        }

        // Auto-hide success message after 5 seconds
        @if(session('success'))
            setTimeout(function() {
                const alert = document.querySelector('.bg-green-100');
                if (alert) {
                    alert.style.transition = 'opacity 0.5s';
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 500);
                }
            }, 5000);
        @endif
    </script>
</body>
</html>