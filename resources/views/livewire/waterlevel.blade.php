<div class="min-h-screen">
    @section('title', 'Water Level Monitoring')
    <div class="container mx-auto px-4 py-6">
        <!-- Header Section with Gradient -->
        <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-3xl shadow-lg p-6 mb-8">
            <div class="flex flex-col lg:flex-row items-center justify-between gap-4">
                <!-- Title -->
                <h1 class="text-3xl font-bold text-white">
                    <i class="fas fa-water mr-2"></i>Water Level Monitoring
                </h1>

                <!-- Insert Data Button for SuperAdmin -->
                @if (SuperAdmin())
                <x-filament::modal :close-by-clicking-away="false" id="waterlevel-modal">
                    <x-slot name="trigger">
                        <x-filament::button icon="heroicon-o-arrow-up-tray" class="bg-white bg-opacity-20 hover:bg-opacity-30 transition-all duration-300">
                            Insert Data
                        </x-filament::button>
                    </x-slot>
                    <x-slot name="heading">
                        Excel Water Level
                    </x-slot>
                    <x-slot name="description">
                        Insert Data Excel Water Level here
                    </x-slot>
                    <form wire:submit="saveForm" wire:loading.attr="disabled">
                        {{ $this->form }}

                        <div class="flex justify-end gap-x-3 mt-6">
                            <x-filament::button
                                type="submit"
                                wire:loading.attr="disabled"
                                wire:loading.class="opacity-50 cursor-wait">
                                <span wire:loading.remove>Upload</span>
                                <span wire:loading>Processing...</span>
                            </x-filament::button>
                        </div>
                    </form>
                </x-filament::modal>
                @endif
            </div>
        </div>

        <!-- Top Grid: Filters and Map -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <!-- Filters Card -->
            <div class="weather-card bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4 flex items-center">
                    <i class="fas fa-filter mr-2 text-blue-500"></i>Filters
                </h2>

                <!-- Loading States with improved styling -->
                <div wire:loading wire:target="updateSelectedStation"
                    class="mb-4 p-3 bg-blue-50 dark:bg-blue-900/30 rounded-lg animate-pulse">
                    <div class="flex items-center space-x-3">
                        <div class="animate-spin rounded-full h-4 w-4 border-2 border-blue-500 border-t-transparent"></div>
                        <span class="text-sm text-blue-600 dark:text-blue-400">Loading stations...</span>
                    </div>
                </div>

                <!-- Filter Controls with improved styling -->
                <div class="space-y-4">
                    <div class="relative">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date</label>
                        <input type="date" wire:model.live="selectedDate"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                    </div>

                    <div class="relative">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Wilayah</label>
                        <select wire:model="selectedWilayah" wire:change="updateSelectedStation($event.target.value)"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                            <option value="">Select Wilayah</option>
                            @foreach($wilayah as $wil)
                            <option value="{{ $wil->id }}">{{ $wil->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="relative">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Station</label>
                        <select wire:model="selectedStation" wire:change="onChangeStation($event.target.value)"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                            <option value="">Select Station</option>
                            @foreach($stations as $station)
                            <option value="{{ $station->id }}">{{ $station->location }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Map Container with improved styling -->
            <div class="lg:col-span-2">
                <div class="weather-card bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4 flex items-center">
                        <i class="fas fa-map-marked-alt mr-2 text-blue-500"></i>Location Map
                    </h2>
                    <div wire:ignore id="map" class="w-full rounded-lg" style="min-height: 400px;"></div>
                </div>
            </div>
        </div>

        <!-- Bottom Grid: Chart and Table with improved styling -->
        <div class="grid grid-cols-1 gap-6">
            <!-- Chart Card -->
            <div class="weather-card bg-white dark:bg-gray-800 rounded-xl shadow-lg">
                <div class="p-6">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4 flex items-center">
                        <i class="fas fa-chart-line mr-2 text-blue-500"></i>Water Level Trend
                    </h2>

                    <!-- Chart Controls -->
                    <div class="flex flex-wrap gap-2 mb-4">
                        <button type="button" data-period="today" class="period-btn px-4 py-2 rounded-lg text-sm font-medium bg-blue-500 text-white hover:bg-blue-600 transition-colors duration-200">
                            <i class="fas fa-calendar-day mr-1"></i>Today
                        </button>
                        <button type="button" data-period="week" class="period-btn px-4 py-2 rounded-lg text-sm font-medium bg-gray-200 text-gray-700 hover:bg-gray-300 transition-colors duration-200">
                            <i class="fas fa-calendar-week mr-1"></i>Week
                        </button>
                        <button type="button" data-period="month" class="period-btn px-4 py-2 rounded-lg text-sm font-medium bg-gray-200 text-gray-700 hover:bg-gray-300 transition-colors duration-200">
                            <i class="fas fa-calendar-alt mr-1"></i>Month
                        </button>
                    </div>


                    <!-- Chart Container -->
                    <div wire:ignore>
                        <div id="container" class="w-full h-full"></div>
                    </div>
                </div>
            </div>

            <!-- Table Card -->
            <div class="weather-card bg-white dark:bg-gray-800 rounded-xl shadow-lg">
                <div class="p-6">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4 flex items-center">
                        <i class="fas fa-table mr-2 text-blue-500"></i>Recent Measurements
                    </h2>
                    <div class="overflow-x-auto">
                        {{ $this->table }}
                    </div>
                </div>
            </div>
        </div>
    </div>



    <script type="module">
        let map = L.map('map', {
            preferCanvas: true,
        }).setView([-2.2745234, 111.61404248], 13);

        // Add tile layer
        L.tileLayer('http://{s}.google.com/vt?lyrs=s&x={x}&y={y}&z={z}', {
            maxZoom: 20,
            subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
        }).addTo(map);

        // Initialize layerGroup
        let layerGroup = L.layerGroup().addTo(map);
        $(document).on('livewire:initialized', function() {
            let chart;
            let chartData = {};
            let currentView = 'today';

            // Chart configuration
            const options = {
                series: [{
                    name: 'Level In',
                    data: []
                }, {
                    name: 'Level Out',
                    data: []
                }, {
                    name: 'Level Actual',
                    data: []
                }, {
                    name: 'Batas Atas',
                    data: []
                }, {
                    name: 'Batas Bawah',
                    data: []
                }],
                chart: {
                    type: 'line',
                    height: 400,
                    zoom: {
                        enabled: true
                    },
                    animations: {
                        enabled: true,
                        easing: 'easeinout',
                        speed: 800,
                        animateGradually: {
                            enabled: true,
                            delay: 150
                        },
                        dynamicAnimation: {
                            enabled: true,
                            speed: 350
                        }
                    },
                    toolbar: {
                        show: true,
                        tools: {
                            download: true,
                            selection: true,
                            zoom: true,
                            zoomin: true,
                            zoomout: true,
                            pan: true,
                            reset: true
                        }
                    }
                },
                colors: ['#2196F3', '#4CAF50', '#FFC107', '#FF5252', '#FF9800'],
                stroke: {
                    curve: 'smooth',
                    width: [3, 3, 3, 2, 2],
                    dashArray: [0, 0, 0, 5, 5]
                },
                markers: {
                    size: 4,
                    strokeColors: "#fff",
                    strokeWidth: 2,
                    hover: {
                        size: 7,
                    }
                },
                xaxis: {
                    type: 'datetime',
                    labels: {
                        datetimeUTC: false,
                        datetimeFormatter: {
                            year: 'yyyy',
                            month: "MMM 'yy",
                            day: 'dd MMM',
                            hour: 'HH:mm'
                        }
                    }
                },
                yaxis: {
                    title: {
                        text: 'Water Level (m)'
                    },
                    labels: {
                        formatter: (value) => value?.toFixed(2) ?? 0
                    }
                },
                tooltip: {
                    shared: true,
                    x: {
                        format: 'dd MMM yyyy HH:mm'
                    },
                    y: {
                        formatter: function(val) {
                            return val?.toFixed(2) + ' m' ?? '0 m';
                        }
                    }
                },
                legend: {
                    position: 'top',
                    horizontalAlign: 'right'
                }
            };

            // Initialize chart
            const chartContainer = document.querySelector('#container');
            if (chartContainer) {
                chart = new ApexCharts(chartContainer, options);
                chart.render();
            }

            // Listen for chart data updates
            Livewire.on('updateChartData', (data) => {
                chartData = data[0];
                updateChartWithPeriod(currentView);
            });

            // Period button click handlers
            document.querySelectorAll('.period-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const period = this.dataset.period;
                    currentView = period;
                    updateChartWithPeriod(period);
                    updateButtonStates();
                });
            });

            function updateChartWithPeriod(period) {
                if (!chartData || !chartData[period]) return;

                const periodData = chartData[period];
                const seriesData = [{
                    name: 'Level In',
                    data: periodData.levelIn || []
                }, {
                    name: 'Level Out',
                    data: periodData.levelOut || []
                }, {
                    name: 'Level Actual',
                    data: periodData.levelActual || []
                }, {
                    name: 'Batas Atas',
                    data: periodData.batasAtas || []
                }, {
                    name: 'Batas Bawah',
                    data: periodData.batasBawah || []
                }];

                chart.updateSeries(seriesData);
            }

            function updateButtonStates() {
                document.querySelectorAll('.period-btn').forEach(btn => {
                    const period = btn.dataset.period;
                    if (period === currentView) {
                        btn.classList.remove('bg-gray-200', 'text-gray-700');
                        btn.classList.add('bg-blue-500', 'text-white');
                    } else {
                        btn.classList.remove('bg-blue-500', 'text-white');
                        btn.classList.add('bg-gray-200', 'text-gray-700');
                    }
                });
            }

            // Initial button state
            updateButtonStates();


            // maps 
            Livewire.on('updateMapMarker', (eventData) => {
                // Debug logging
                // console.log('Raw event data:', eventData);

                // Extract data from the first element if it's an array
                const data = Array.isArray(eventData) ? eventData[0] : eventData;
                // console.log('Processed data:', data);

                const coordinates = data.coordinates;
                const station = data.station;

                if (coordinates && coordinates.lat && coordinates.lon) {
                    // Clear previous markers
                    layerGroup.clearLayers();

                    // Create marker with popup
                    const marker = L.marker([coordinates.lat, coordinates.lon], {
                        title: station.location
                    }).bindPopup(`
                        <b>Water Station: ${station.location}</b><br>
                        Level In Terakhir: ${station.level_in}<br>
                        Level Out Terakhir: ${station.level_out}<br>
                        Level Actual Terakhir: ${station.level_actual}<br>
                        Rata rata Level In: ${station.level_in_avg}<br>
                        Rata rata Level Out: ${station.level_out_avg}<br>
                        Rata rata Level Actual: ${station.level_actual_avg}<br>
                        Batas Atas Air: ${station.batas_atas_air}<br>
                        Batas Bawah Air: ${station.batas_bawah_air} 
                    `);

                    // Add marker to layer group
                    layerGroup.addLayer(marker);

                    // Fit bounds with padding
                    map.setView([coordinates.lat, coordinates.lon], 15);
                    marker.openPopup();
                } else {
                    console.error('Invalid coordinates:', coordinates);
                }
            });
        });
    </script>
</div>