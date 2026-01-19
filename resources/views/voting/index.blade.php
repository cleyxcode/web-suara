<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemilihan - {{ $election->name ?? 'Voting' }}</title>
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
            @if($candidates && $candidates->count() > 0)
                <form action="{{ route('voting.store') }}" method="POST" id="votingForm">
                    @csrf

                    <!-- Optional: Input Nama Pemilih -->
                    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Nama Pemilih (Opsional)
                        </label>
                        <input 
                            type="text" 
                            name="voter_name" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Masukkan nama Anda (opsional)"
                        >
                        <p class="text-sm text-gray-500 mt-2">
                            Anda dapat mengisi nama atau mengosongkan jika ingin anonim.
                        </p>
                    </div>

                    <!-- Daftar Calon -->
                    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-6">Pilih Calon:</h2>
                        
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
                            KIRIM SUARA
                        </button>
                    </div>
                </form>
            @endif

            <!-- Footer Info -->
            <div class="text-center mt-8 text-sm text-gray-600">
                <p>Sistem Pemilihan Ketua Ranting Gereja</p>
                <p class="mt-1">Voting dilakukan tanpa login - Satu suara per peserta</p>
            </div>

        </div>
    </div>

    <!-- Auto-hide success message after 5 seconds -->
    @if(session('success'))
        <script>
            setTimeout(function() {
                const alert = document.querySelector('.bg-green-100');
                if (alert) {
                    alert.style.transition = 'opacity 0.5s';
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 500);
                }
            }, 5000);
        </script>
    @endif

    <!-- Prevent double submit -->
    <script>
        const form = document.getElementById('votingForm');
        const submitBtn = document.getElementById('submitBtn');
        
        if (form) {
            form.addEventListener('submit', function(e) {
                // Disable button to prevent double click
                submitBtn.disabled = true;
                submitBtn.textContent = 'MENGIRIM...';
                
                // Re-enable after 3 seconds as fallback (in case of error)
                setTimeout(function() {
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'KIRIM SUARA';
                }, 3000);
            });
        }
    </script>
</body>
</html>