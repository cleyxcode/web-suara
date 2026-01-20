<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemilihan - {{ $election->name ?? 'Voting' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#2563eb',
                        'primary-dark': '#1d4ed8',
                        'primary-light': '#3b82f6',
                    },
                    fontFamily: {
                        'sans': ['Poppins', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        * {
            font-family: 'Poppins', sans-serif;
        }
        .card-hover {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .card-hover:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 25px -5px rgba(37, 99, 235, 0.1), 0 10px 10px -5px rgba(37, 99, 235, 0.04);
        }
        input[type="radio"]:checked + .candidate-card {
            border-color: #2563eb;
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.2);
        }
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .pulse-animation {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: .8; }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 via-white to-blue-50 min-h-screen">
    <div class="min-h-screen py-8 px-4">
        <div class="max-w-5xl mx-auto">
            
            <!-- Header -->
            <div class="bg-white rounded-2xl shadow-xl p-8 mb-8 border border-blue-100">
                <div class="text-center">
                    <div class="inline-block mb-4">
                        <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg">
                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <h1 class="text-4xl font-bold bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent mb-3">
                        Pemilihan Ketua Ranting Gereja
                    </h1>
                    @if($election)
                        <p class="text-xl text-gray-600 mb-3 font-medium">{{ $election->name }}</p>
                        <div class="inline-block bg-gradient-to-r from-blue-500 to-blue-600 text-white px-6 py-2 rounded-full text-sm font-semibold shadow-md">
                            Putaran {{ $round->round_number }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Alert Error -->
            @if(session('error') || isset($error))
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-5 mb-6 rounded-xl shadow-md">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <p class="ml-3 font-semibold">{{ session('error') ?? $error }}</p>
                    </div>
                </div>
            @endif

            <!-- Alert Success -->
            @if(session('success'))
                <div id="successAlert" class="bg-green-50 border-l-4 border-green-500 text-green-700 p-5 mb-6 rounded-xl shadow-md">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <p class="ml-3 font-semibold">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            <!-- Form Voting -->
            @if($candidates && $candidates->count() > 0)
                <form action="{{ route('voting.store') }}" method="POST" id="votingForm">
                    @csrf

                    <!-- Input Nama Pemilih -->
                    <div class="bg-white rounded-2xl shadow-lg p-6 mb-8 border border-blue-100">
                        <label class="block text-sm font-semibold text-gray-700 mb-3 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Nama Pemilih <span class="text-gray-400 font-normal ml-1">(Opsional)</span>
                        </label>
                        <input 
                            type="text" 
                            name="voter_name" 
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all outline-none"
                            placeholder="Masukkan nama Anda"
                        >
                        <p class="text-sm text-gray-500 mt-3 flex items-center">
                            <svg class="w-4 h-4 mr-1 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                            </svg>
                            Identitas Anda terlindungi sepenuhnya
                        </p>
                    </div>

                    <!-- Daftar Calon -->
                    <div class="bg-white rounded-2xl shadow-lg p-6 mb-8 border border-blue-100">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-2xl font-bold text-gray-900 flex items-center">
                                <svg class="w-7 h-7 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                Pilih Kandidat
                            </h2>
                            <span class="bg-blue-100 text-blue-700 px-4 py-1 rounded-full text-sm font-semibold">
                                {{ $candidates->count() }} Kandidat
                            </span>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($candidates as $index => $candidate)
                                <label class="card-hover cursor-pointer block">
                                    <input 
                                        type="radio" 
                                        name="candidate_id" 
                                        value="{{ $candidate->id }}" 
                                        class="hidden peer"
                                        required
                                    >
                                    <div class="candidate-card flex items-center p-5 border-2 border-gray-200 rounded-xl transition-all">
                                        @if($candidate->photo)
                                            <img 
                                                src="{{ Storage::url($candidate->photo) }}" 
                                                alt="{{ $candidate->name }}"
                                                class="w-20 h-20 rounded-full object-cover border-4 border-white shadow-md"
                                            >
                                        @else
                                            <div class="w-20 h-20 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center border-4 border-white shadow-md">
                                                <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                        @endif
                                        <div class="ml-5 flex-1">
                                            <span class="text-xs font-semibold text-blue-600 bg-blue-50 px-3 py-1 rounded-full inline-block mb-2">
                                                No. {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                                            </span>
                                            <h3 class="text-lg font-bold text-gray-900">
                                                {{ $candidate->name }}
                                            </h3>
                                        </div>
                                        <div class="ml-4 hidden peer-checked:block">
                                            <svg class="w-8 h-8 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>

                        @error('candidate_id')
                            <p class="text-red-600 text-sm mt-3 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="text-center">
                        <button 
                            type="submit"
                            id="submitBtn"
                            class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-bold py-4 px-12 rounded-xl text-lg shadow-xl hover:shadow-2xl transition-all transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <span class="flex items-center justify-center">
                                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span id="btnText">KIRIM SUARA</span>
                            </span>
                        </button>
                        <p class="text-sm text-gray-500 mt-4">
                            Suara yang telah dikirim tidak dapat diubah
                        </p>
                    </div>
                </form>
            @else
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-6 rounded-xl shadow-md">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-yellow-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <p class="font-semibold text-yellow-800">Belum ada kandidat tersedia</p>
                    </div>
                </div>
            @endif

            <!-- Footer Info -->
            <div class="text-center mt-10 space-y-2">
                <p class="text-sm text-gray-600 font-medium">Sistem Pemilihan Ketua Ranting Gereja</p>
                <p class="text-xs text-gray-500">© 2024 - Hak Suara Terlindungi</p>
            </div>

        </div>
    </div>

    <!-- Auto-hide success message after 5 seconds -->
    @if(session('success'))
        <script>
            setTimeout(function() {
                const alert = document.getElementById('successAlert');
                if (alert) {
                    alert.style.transition = 'opacity 0.5s ease-out';
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
        const btnText = document.getElementById('btnText');
        
        if (form) {
            form.addEventListener('submit', function(e) {
                submitBtn.disabled = true;
                btnText.textContent = 'MENGIRIM...';
                
                setTimeout(function() {
                    submitBtn.disabled = false;
                    btnText.textContent = 'KIRIM SUARA';
                }, 3000);
            });
        }
    </script>
</body>
</html>