<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Pemilihan - {{ $selectedElection->name ?? 'Statistik' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen py-8 px-4">
        <div class="max-w-6xl mx-auto">
            
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                <h1 class="text-3xl font-bold text-gray-900 text-center">
                    📊 Hasil Pemilihan Ketua Ranting Gereja
                </h1>
                @if($selectedElection)
                    <p class="text-xl text-gray-600 text-center mt-2">{{ $selectedElection->name }}</p>
                @endif
            </div>

            <!-- Filter Section -->
            <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                <form method="GET" action="{{ url('/hasil') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Pilih Pemilihan:</label>
                        <select 
                            name="election_id" 
                            onchange="this.form.submit()"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        >
                            @foreach($elections as $election)
                                <option value="{{ $election->id }}" {{ $selectedElection && $selectedElection->id == $election->id ? 'selected' : '' }}>
                                    {{ $election->name }} ({{ ucfirst($election->status) }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    @if($rounds->count() > 0)
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Pilih Putaran:</label>
                            <select 
                                name="round_id" 
                                onchange="this.form.submit()"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                            >
                                @foreach($rounds as $round)
                                    <option value="{{ $round->id }}" {{ $selectedRound && $selectedRound->id == $round->id ? 'selected' : '' }}>
                                        Putaran {{ $round->round_number }} - {{ ucfirst($round->status) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                </form>
            </div>

            @if($winner)
                <!-- Winner Card -->
                <div class="bg-gradient-to-r from-yellow-400 via-yellow-500 to-yellow-600 rounded-lg shadow-xl p-8 mb-6 text-white">
                    <div class="flex flex-col md:flex-row items-center justify-center gap-6">
                        <div class="text-8xl animate-bounce">🏆</div>
                        <div class="text-center md:text-left">
                            <h2 class="text-3xl font-bold">KETUA TERPILIH</h2>
                            <p class="text-5xl font-bold mt-3">{{ $winner['name'] }}</p>
                            <p class="text-2xl mt-2">{{ $winner['total_votes'] }} suara · Putaran {{ $winner['round_number'] }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if($statistics)
                <!-- Round Info -->
                <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                    <h3 class="text-xl font-bold mb-4">Informasi Putaran {{ $statistics['round']['round_number'] }}</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="text-center p-4 bg-gray-50 rounded-lg">
                            <p class="text-sm text-gray-500">Status</p>
                            <p class="text-2xl font-bold text-gray-900 mt-1">{{ ucfirst($statistics['round']['status']) }}</p>
                        </div>
                        <div class="text-center p-4 bg-gray-50 rounded-lg">
                            <p class="text-sm text-gray-500">Total Suara</p>
                            <p class="text-2xl font-bold text-blue-600 mt-1">{{ $statistics['round']['total_votes'] }}</p>
                        </div>
                        <div class="text-center p-4 bg-gray-50 rounded-lg">
                            <p class="text-sm text-gray-500">Jumlah Calon</p>
                            <p class="text-2xl font-bold text-green-600 mt-1">{{ count($statistics['statistics']) }}</p>
                        </div>
                        <div class="text-center p-4 bg-gray-50 rounded-lg">
                            <p class="text-sm text-gray-500">Eliminasi</p>
                            <p class="text-2xl font-bold text-purple-600 mt-1">{{ $statistics['round']['is_eliminated'] ? 'Sudah' : 'Belum' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Results Table -->
                <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                    <h3 class="text-xl font-bold mb-4">Hasil Suara</h3>
                    <div class="space-y-4">
                        @foreach($statistics['statistics'] as $index => $stat)
                            <div class="flex items-center justify-between p-4 border-2 {{ $index === 0 ? 'border-yellow-400 bg-yellow-50' : 'border-gray-200 bg-gray-50' }} rounded-lg">
                                <div class="flex items-center gap-4">
                                    <div class="text-3xl font-bold text-gray-400">{{ $index + 1 }}</div>
                                    @if($stat['candidate_photo'])
                                        <img 
                                            src="{{ Storage::url($stat['candidate_photo']) }}" 
                                            alt="{{ $stat['candidate_name'] }}"
                                            class="w-16 h-16 rounded-full object-cover"
                                        >
                                    @else
                                        <div class="w-16 h-16 rounded-full bg-gray-300 flex items-center justify-center">
                                            <svg class="w-8 h-8 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                    @endif
                                    <div>
                                        <p class="text-xl font-bold text-gray-900">{{ $stat['candidate_name'] }}</p>
                                        <p class="text-sm font-semibold">
                                            @if($stat['is_eliminated'])
                                                <span class="text-red-600">❌ Gugur pada putaran ini</span>
                                            @else
                                                <span class="text-green-600">✅ Aktif</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-4xl font-bold text-blue-600">{{ $stat['total_votes'] }}</p>
                                    <p class="text-lg text-gray-500">{{ $stat['percentage'] }}%</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Charts -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Bar Chart -->
                    <div class="bg-white rounded-lg shadow-lg p-6">
                        <h3 class="text-xl font-bold mb-4">Grafik Batang</h3>
                        <canvas id="barChart"></canvas>
                    </div>

                    <!-- Pie Chart -->
                    <div class="bg-white rounded-lg shadow-lg p-6">
                        <h3 class="text-xl font-bold mb-4">Grafik Lingkaran</h3>
                        <canvas id="pieChart"></canvas>
                    </div>
                </div>

                <script>
                    const stats = @json($statistics);
                    
                    const colors = [
                        'rgb(251, 191, 36)',
                        'rgb(59, 130, 246)',
                        'rgb(34, 197, 94)',
                        'rgb(239, 68, 68)',
                        'rgb(168, 85, 247)',
                        'rgb(236, 72, 153)',
                    ];

                    // Bar Chart
                    const barCtx = document.getElementById('barChart').getContext('2d');
                    new Chart(barCtx, {
                        type: 'bar',
                        data: {
                            labels: stats.statistics.map(s => s.candidate_name),
                            datasets: [{
                                label: 'Jumlah Suara',
                                data: stats.statistics.map(s => s.total_votes),
                                backgroundColor: colors,
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: { display: false }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: { stepSize: 1 }
                                }
                            }
                        }
                    });

                    // Pie Chart
                    const pieCtx = document.getElementById('pieChart').getContext('2d');
                    new Chart(pieCtx, {
                        type: 'pie',
                        data: {
                            labels: stats.statistics.map(s => s.candidate_name),
                            datasets: [{
                                data: stats.statistics.map(s => s.total_votes),
                                backgroundColor: colors,
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: { position: 'bottom' }
                            }
                        }
                    });
                </script>
            @else
                <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded-lg">
                    <p class="font-semibold">Belum ada hasil yang dapat ditampilkan.</p>
                    <p class="text-sm">Hasil akan muncul setelah putaran ditutup.</p>
                </div>
            @endif

            <!-- Footer -->
            <div class="text-center mt-8 text-sm text-gray-600">
                <p>Sistem Pemilihan Ketua Ranting Gereja</p>
                <p class="mt-1">Data bersifat read-only dan transparan</p>
            </div>

        </div>
    </div>
</body>
</html>