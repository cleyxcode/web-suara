<x-filament-panels::page>
    <div class="space-y-6">
        
        <!-- Filter Section -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Election Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Pilih Pemilihan
                </label>
                <select 
                    wire:model.live="selectedElectionId"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                >
                    <option value="">-- Pilih Pemilihan --</option>
                    @foreach($this->getElections() as $election)
                        <option value="{{ $election->id }}">
                            {{ $election->name }} ({{ ucfirst($election->status) }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Round Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Pilih Putaran
                </label>
                <select 
                    wire:model.live="selectedRoundId"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                    @if(!$selectedElectionId) disabled @endif
                >
                    <option value="">-- Pilih Putaran --</option>
                    @foreach($this->getRounds() as $round)
                        <option value="{{ $round->id }}">
                            Putaran {{ $round->round_number }} - {{ ucfirst($round->status) }}
                            @if($round->is_eliminated) (Sudah Eliminasi) @endif
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        @if($this->getWinner())
            <!-- Winner Card -->
            @php $winner = $this->getWinner(); @endphp
            <div class="bg-gradient-to-r from-yellow-400 to-yellow-600 rounded-lg p-6 text-white">
                <div class="flex items-center gap-4">
                    <div class="text-6xl">🏆</div>
                    <div>
                        <h2 class="text-2xl font-bold">KETUA TERPILIH</h2>
                        <p class="text-3xl font-bold mt-2">{{ $winner['name'] }}</p>
                        <p class="text-lg mt-1">{{ $winner['total_votes'] }} suara - Putaran {{ $winner['round_number'] }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if($this->getStatistics())
            @php $stats = $this->getStatistics(); @endphp
            
            <!-- Round Info Card -->
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow">
                <h3 class="text-lg font-semibold mb-4">Informasi Putaran {{ $stats['round']['round_number'] }}</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Status</p>
                        <p class="text-xl font-bold">{{ ucfirst($stats['round']['status']) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Total Suara</p>
                        <p class="text-xl font-bold">{{ $stats['round']['total_votes'] }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Jumlah Calon</p>
                        <p class="text-xl font-bold">{{ count($stats['statistics']) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Eliminasi</p>
                        <p class="text-xl font-bold">{{ $stats['round']['is_eliminated'] ? 'Sudah' : 'Belum' }}</p>
                    </div>
                </div>
            </div>

            <!-- Table & Chart Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                
                <!-- Table Results -->
                <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow">
                    <h3 class="text-lg font-semibold mb-4">Hasil Suara</h3>
                    <div class="space-y-3">
                        @foreach($stats['statistics'] as $stat)
                            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-900 rounded-lg">
                                <div class="flex items-center gap-3">
                                    @if($stat['candidate_photo'])
                                        <img 
                                            src="{{ Storage::url($stat['candidate_photo']) }}" 
                                            alt="{{ $stat['candidate_name'] }}"
                                            class="w-12 h-12 rounded-full object-cover"
                                        >
                                    @else
                                        <div class="w-12 h-12 rounded-full bg-gray-300 dark:bg-gray-700 flex items-center justify-center">
                                            <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                    @endif
                                    <div>
                                        <p class="font-semibold">{{ $stat['candidate_name'] }}</p>
                                        <p class="text-sm text-gray-500">
                                            @if($stat['eliminated_this_round'])
                                                <span class="text-red-600 font-semibold">❌ Gugur di putaran ini</span>
                                            @elseif($stat['is_eliminated'])
                                                <span class="text-gray-500">Gugur sebelumnya</span>
                                            @else
                                                <span class="text-green-600">✅ Aktif</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-2xl font-bold">{{ $stat['total_votes'] }}</p>
                                    <p class="text-sm text-gray-500">{{ $stat['percentage'] }}%</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Bar Chart -->
                <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow">
                    <h3 class="text-lg font-semibold mb-4">Grafik Batang</h3>
                    <canvas id="barChart"></canvas>
                </div>

            </div>

            <!-- Pie Chart -->
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow">
                <h3 class="text-lg font-semibold mb-4">Grafik Lingkaran</h3>
                <div class="max-w-md mx-auto">
                    <canvas id="pieChart"></canvas>
                </div>
            </div>

        @endif

        @if($this->getLineChartData() && count($this->getLineChartData()['datasets']) > 0)
            <!-- Line Chart (Comparison) -->
            @php $lineData = $this->getLineChartData(); @endphp
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow">
                <h3 class="text-lg font-semibold mb-4">Perbandingan Antar Putaran</h3>
                <canvas id="lineChart"></canvas>
            </div>
        @endif

    </div>

    @if($this->getStatistics())
        @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
        <script>
            // Tunggu sampai DOM dan Livewire ready
            document.addEventListener('livewire:init', () => {
                renderCharts();
            });

            // Fallback jika livewire:init tidak dipanggil
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', renderCharts);
            } else {
                renderCharts();
            }

            // Re-render saat Livewire update
            Livewire.hook('morph.updated', () => {
                renderCharts();
            });

            function renderCharts() {
                const stats = @json($this->getStatistics());
                
                if (!stats || !stats.statistics || stats.statistics.length === 0) {
                    console.log('No statistics data available');
                    return;
                }

                // Destroy existing charts to prevent duplicates
                if (window.barChartInstance) window.barChartInstance.destroy();
                if (window.pieChartInstance) window.pieChartInstance.destroy();
                if (window.lineChartInstance) window.lineChartInstance.destroy();

                // Colors
                const colors = [
                    'rgb(59, 130, 246)',
                    'rgb(239, 68, 68)',
                    'rgb(34, 197, 94)',
                    'rgb(251, 146, 60)',
                    'rgb(168, 85, 247)',
                    'rgb(236, 72, 153)',
                ];

                // Bar Chart
                const barCanvas = document.getElementById('barChart');
                if (barCanvas) {
                    const barCtx = barCanvas.getContext('2d');
                    window.barChartInstance = new Chart(barCtx, {
                        type: 'bar',
                        data: {
                            labels: stats.statistics.map(s => s.candidate_name),
                            datasets: [{
                                label: 'Jumlah Suara',
                                data: stats.statistics.map(s => s.total_votes),
                                backgroundColor: stats.statistics.map((s, i) => colors[i % colors.length]),
                                borderColor: stats.statistics.map((s, i) => colors[i % colors.length]),
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: true,
                            plugins: {
                                legend: {
                                    display: false
                                },
                                title: {
                                    display: false
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        stepSize: 1,
                                        precision: 0
                                    }
                                }
                            }
                        }
                    });
                }

                // Pie Chart
                const pieCanvas = document.getElementById('pieChart');
                if (pieCanvas) {
                    const pieCtx = pieCanvas.getContext('2d');
                    window.pieChartInstance = new Chart(pieCtx, {
                        type: 'pie',
                        data: {
                            labels: stats.statistics.map(s => s.candidate_name),
                            datasets: [{
                                data: stats.statistics.map(s => s.total_votes),
                                backgroundColor: stats.statistics.map((s, i) => colors[i % colors.length]),
                                borderColor: '#ffffff',
                                borderWidth: 2
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: true,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        padding: 10,
                                        font: {
                                            size: 12
                                        }
                                    }
                                }
                            }
                        }
                    });
                }

                // Line Chart
                @if($this->getLineChartData())
                const lineData = @json($this->getLineChartData());
                const lineCanvas = document.getElementById('lineChart');
                
                if (lineCanvas && lineData && lineData.datasets && lineData.datasets.length > 0) {
                    const lineCtx = lineCanvas.getContext('2d');
                    window.lineChartInstance = new Chart(lineCtx, {
                        type: 'line',
                        data: {
                            labels: lineData.labels,
                            datasets: lineData.datasets.map((dataset, index) => ({
                                label: dataset.label,
                                data: dataset.data,
                                borderColor: colors[index % colors.length],
                                backgroundColor: colors[index % colors.length] + '20',
                                tension: 0.1,
                                borderWidth: 2,
                                pointRadius: 4,
                                pointHoverRadius: 6
                            }))
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: true,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        padding: 10,
                                        font: {
                                            size: 12
                                        }
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        stepSize: 1,
                                        precision: 0
                                    }
                                }
                            }
                        }
                    });
                }
                @endif

                console.log('Charts rendered successfully');
            }
        </script>
        @endpush
    @endif

</x-filament-panels::page>