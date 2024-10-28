<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="container mx-auto px-4">
        <!-- Page Title -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Water Level Monitoring</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">Real-time water level monitoring and analysis dashboard</p>
        </div>

        <!-- Top Grid: Filters and Map -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <!-- Filters Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="p-6">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">
                        <i class="fas fa-filter mr-2"></i>Filters
                    </h2>

                    <!-- Loading States -->
                    <div wire:loading wire:target="updateSelectedStation"
                        class="mb-4 p-3 bg-blue-50 dark:bg-blue-900/30 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <div class="animate-spin rounded-full h-4 w-4 border-2 border-blue-500 border-t-transparent"></div>
                            <span class="text-sm text-blue-600 dark:text-blue-400">Loading stations...</span>
                        </div>
                    </div>

                    <div wire:loading wire:target="onChangeStation"
                        class="mb-4 p-3 bg-green-50 dark:bg-green-900/30 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <div class="animate-spin rounded-full h-4 w-4 border-2 border-green-500 border-t-transparent"></div>
                            <span class="text-sm text-green-600 dark:text-green-400">Loading map marker...</span>
                        </div>
                    </div>

                    <!-- Filter Controls -->
                    <div class="space-y-4">
                        <div>
                            <label for="date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date</label>
                            <input type="date" id="date"
                                wire:model.live="selectedDate"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label for="wilayah" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Wilayah</label>
                            <select id="wilayah" wire:model="selectedWilayah" wire:change="updateSelectedStation($event.target.value)"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select Wilayah</option>
                                @foreach($wilayah as $wil)
                                <option value="{{ $wil->id }}">{{ $wil->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="station" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Station</label>
                            <select id="station" wire:model="selectedStation" wire:change="onChangeStation($event.target.value)"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select Station</option>
                                @foreach($stations as $station)
                                <option value="{{ $station->id }}">{{ $station->location }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Map Container -->
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                    <div wire:ignore id="map" class="w-full rounded-lg" style="min-height: 400px;"></div>
                </div>
            </div>
        </div>

        <!-- Bottom Grid: Chart and Table -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Chart Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="p-6">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">
                        <i class="fas fa-chart-line mr-2"></i>Water Level Trend
                    </h2>
                    <div class="w-full" style="height: 300px;">
                        <canvas id="waterLevelChart" class="w-full h-full"></canvas>
                    </div>
                </div>
            </div>

            <!-- Table Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="p-6">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">
                        <i class="fas fa-table mr-2"></i>Recent Measurements
                    </h2>
                    <div class="overflow-x-auto">
                        <div>
                            {{ $this->table }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Styles -->
    <style>
        @keyframes loading {
            0% {
                width: 0%;
            }

            100% {
                width: 100%;
            }
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        /* Card Hover Effects */
        .bg-white {
            transition: transform 0.2s ease-in-out;
        }

        .bg-white:hover {
            transform: translateY(-2px);
        }
    </style>

    <script type="module">
        // Move map initialization outside document.ready
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

        // Listen for Livewire events
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('updateMapMarker', (data) => {
                console.log('Received data:', data); // Debug log

                // Extract coordinates from the nested structure
                const coordinates = data.coordinates;
                const station = data.station;

                if (coordinates && coordinates.lat && coordinates.lon) {
                    // console.log('Processing coordinates:', coordinates); // Debug log

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
                }
            });
        });
    </script>



</div>