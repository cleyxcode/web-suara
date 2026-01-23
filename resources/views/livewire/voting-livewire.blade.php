<div wire:poll.3s class="min-h-screen py-8 px-4 bg-gradient-to-br from-blue-50 via-white to-blue-50">
    <div class="max-w-5xl mx-auto">
        
        <!-- Header -->
        <div class="bg-white rounded-2xl shadow-xl p-8 mb-8 border border-blue-100">
            <div class="text-center">
                <!-- Logo AMGPM -->
                <div class="mb-4">
                    <img src="https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEh-KAKSykFAZb2sojO0RD_6lJ__wqPE3aMhb5b-EUNS6TuD2g5-NXOwhYSr1TPc04ASGlMnoD6HBA25AQdiSXlLPbsv-Ymdw9M8IcoafsiGQJ9DyiQAmGe0X8lt2__xWk4_oZ-csyk0hkTP/s1600/LOGO+AMGPM+TERBARU+TRANS.png" 
                         alt="Logo AMGPM" 
                         class="h-24 mx-auto"
                         onerror="this.style.display='none'">
                </div>
                
                <h1 class="text-4xl font-bold bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent mb-3">
                    PEMILIHAN KETUA AMGPM CABANG PNIEL
                </h1>
                @if($election)
                    <p class="text-xl text-gray-600 mb-3 font-medium">{{ $election->name }}</p>
                    <div class="inline-block bg-gradient-to-r from-blue-500 to-blue-600 text-white px-6 py-2 rounded-full text-sm font-semibold shadow-md">
                        Putaran {{ $round->round_number }}
                    </div>
                    <!-- Live Indicator -->
                    <div class="mt-3 flex items-center justify-center gap-2">
                        <span class="relative flex h-3 w-3">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-green-400"></span>
                        </span>
                        <span class="text-sm text-blue-600 font-medium">Live Update Setiap 3 Detik</span>
                    </div>
                @endif
            </div>
        </div>

        @if($statistics)
            <!-- Statistics - Auto Update -->
            <div class="bg-white rounded-2xl shadow-lg p-6 mb-8 border border-blue-100" wire:loading.class="opacity-50">
                <h3 class="text-lg font-bold mb-4 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    Statistik Voting Real-Time
                </h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="text-center p-4 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl transition-all hover:shadow-md">
                        <p class="text-sm text-blue-600 font-medium">Total Peserta</p>
                        <p class="text-3xl font-bold text-blue-700">{{ $statistics['total_participants'] }}</p>
                    </div>
                    <div class="text-center p-4 bg-gradient-to-br from-green-50 to-green-100 rounded-xl transition-all hover:shadow-md">
                        <p class="text-sm text-green-600 font-medium">Sudah Vote</p>
                        <p class="text-3xl font-bold text-green-700">{{ $statistics['voted_count'] }}</p>
                    </div>
                    <div class="text-center p-4 bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl transition-all hover:shadow-md">
                        <p class="text-sm text-orange-600 font-medium">Belum Vote</p>
                        <p class="text-3xl font-bold text-orange-700">{{ $statistics['remaining_count'] }}</p>
                    </div>
                    <div class="text-center p-4 bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl transition-all hover:shadow-md">
                        <p class="text-sm text-purple-600 font-medium">Partisipasi</p>
                        <p class="text-3xl font-bold text-purple-700">{{ $statistics['participation_rate'] }}%</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Alert Error -->
        @if($errorMessage || $error)
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-5 mb-6 rounded-xl shadow-md animate-pulse">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="w-6 h-6 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <p class="ml-3 font-semibold">{{ $errorMessage ?? $error }}</p>
                </div>
            </div>
        @endif

        <!-- Alert Success -->
        @if($successMessage)
            <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-5 mb-6 rounded-xl shadow-md animate-pulse">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="w-6 h-6 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <p class="ml-3 font-semibold">{{ $successMessage }}</p>
                </div>
            </div>
        @endif

        <!-- Form Voting -->
        @if($participants && $participants->count() > 0 && $candidates && $candidates->count() > 0)
            <form wire:submit.prevent="submitVote">
                <!-- Step 1: Pilih Nama Peserta -->
                <div class="bg-white rounded-2xl shadow-lg p-6 mb-8 border border-blue-100">
                    <label class="block text-sm font-semibold text-gray-700 mb-3 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Pilih Nama Anda
                    </label>
                    <select 
                        wire:model="participantId"
                        required
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all outline-none"
                        @if($isSubmitting) disabled @endif
                    >
                        <option value="">-- Pilih Nama Anda --</option>
                        @foreach($participants as $participant)
                            <option value="{{ $participant->id }}">{{ $participant->name }}</option>
                        @endforeach
                    </select>
                    <p class="text-sm text-gray-500 mt-3 flex items-center">
                        <svg class="w-4 h-4 mr-1 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        Pastikan Anda memilih nama Anda sendiri. Tersisa: <strong class="ml-1">{{ $participants->count() }} orang</strong>
                    </p>
                    @error('participantId') 
                        <p class="text-red-600 text-sm mt-2 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Step 2: Pilih Calon -->
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
                            <label class="cursor-pointer block group">
                                <input 
                                    type="radio" 
                                    wire:model="candidateId"
                                    value="{{ $candidate->id }}" 
                                    class="hidden peer"
                                    required
                                    @if($isSubmitting) disabled @endif
                                >
                                <div class="flex items-center p-5 border-2 border-gray-200 rounded-xl transition-all hover:border-blue-400 hover:shadow-lg peer-checked:border-blue-500 peer-checked:bg-gradient-to-br peer-checked:from-blue-50 peer-checked:to-blue-100 peer-checked:shadow-xl">
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

                    @error('candidateId')
                        <p class="text-red-600 text-sm mt-3 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div class="text-center">
                    <button 
                        type="submit"
                        class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-bold py-4 px-12 rounded-xl text-lg shadow-xl hover:shadow-2xl transition-all transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed"
                        wire:loading.attr="disabled"
                        wire:target="submitVote"
                    >
                        <span wire:loading.remove wire:target="submitVote" class="flex items-center justify-center">
                            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            🗳️ KIRIM SUARA
                        </span>
                        <span wire:loading wire:target="submitVote" class="flex items-center justify-center">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            MENGIRIM...
                        </span>
                    </button>
                    <p class="text-sm text-gray-500 mt-4">
                        Suara yang telah dikirim tidak dapat diubah
                    </p>
                </div>
            </form>
        @elseif($participants && $participants->count() === 0)
            <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-6 rounded-xl shadow-md text-center">
                <svg class="w-16 h-16 mx-auto mb-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <p class="text-xl font-bold mb-2">✅ Semua Peserta Sudah Memberikan Suara!</p>
                <p class="text-sm">Silakan tunggu admin menutup putaran ini.</p>
            </div>
        @else
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-6 rounded-xl shadow-md">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-yellow-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <p class="font-semibold text-yellow-800">Tidak ada putaran yang sedang aktif saat ini</p>
                </div>
            </div>
        @endif

        <!-- Footer Info -->
        <div class="text-center mt-10 space-y-2">
            <p class="text-sm text-gray-600 font-medium">Sistem Pemilihan AMGPM Cabang Pniel</p>
            <p class="text-xs text-gray-500">Voting Real-Time - Satu suara per peserta per putaran</p>
            <p class="text-xs text-gray-400">Powered by AMGPM Online</p>
        </div>

    </div>

    <!-- Loading Overlay -->
    <div wire:loading.flex wire:target="submitVote" class="fixed inset-0 bg-black bg-opacity-50 items-center justify-center z-50">
        <div class="bg-white rounded-2xl p-8 flex flex-col items-center shadow-2xl">
            <svg class="animate-spin h-12 w-12 text-blue-600 mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <p class="text-lg font-semibold text-gray-900">Mengirim suara Anda...</p>
        </div>
    </div>
</div>