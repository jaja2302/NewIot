<div class="min-h-screen">
    <div class="container mx-auto px-4 py-6">

        <!-- Search Bar Container -->
        <div class="max-w-4xl mx-auto px-4 pt-4 mb-8">
            <div class="bg-gray-50 rounded-full shadow flex items-center px-4 py-2.5 transition-all duration-200 hover:shadow-lg">
                <input
                    wire:model.live="searchEstate"
                    type="text"
                    placeholder="Search estate..."
                    class="w-full bg-transparent focus:outline-none">
                <div class="ml-auto">
                    <svg class="w-5 h-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                </div>
            </div>

            <!-- Date Subtitle -->
            <p class="text-center text-gray-500 text-sm my-4">Level Air (update : {{ now()->format('d F Y') }})</p>

            <!-- Estate List Container -->
            <div class="grid grid-cols-5 gap-4 mb-8">
                @forelse($filteredEstates as $estate)
                <button
                    wire:click="selectEstate({{ $estate['id'] }})"
                    class="text-left focus:outline-none estate-item {{ $estate['is_active'] ? 'active' : '' }}">
                    <div class="@if($estate['is_active']) bg-blue-50 border-2 border-blue-500 @else bg-white @endif rounded-xl p-4 transition-all duration-200 hover:bg-gray-50">
                        <p class="text-sm @if($estate['is_active']) text-blue-700 @else text-gray-500 @endif mb-1">
                            {{ $estate['name'] }}
                        </p>
                        <p class="text-xl font-bold @if($estate['is_active']) text-blue-900 @else text-gray-900 @endif">
                            {{ number_format($estate['level_blok'], 2) }}<span class="text-sm font-normal ml-1">cm</span>
                        </p>
                    </div>
                </button>
                @empty
                @if($searchEstate)
                <div class="col-span-5 text-center text-gray-500">
                    No estates found matching "{{ $searchEstate }}"
                </div>
                @endif
                @endforelse
            </div>
        </div>

        <!-- Chart Card Section -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8 mt-8">
            <!-- Temperature Chart -->
            <div class="mt-4 weather-card bg-white dark:bg-gray-800 rounded-2xl p-4 shadow-sm hover:shadow-md transition-shadow overflow-hidden relative">
                <!-- Header Section -->
                <div class="flex flex-col space-y-4 sm:space-y-0 sm:flex-row sm:items-center sm:justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-chart-line text-blue-500"></i>
                        <h2 class="font-semibold text-gray-800 dark:text-white">Data Riwayat {{ $selectedStation }}</h2>
                    </div>

                    <!-- Toggle Buttons Container -->
                    <div wire:ignore class="flex flex-col space-y-3 sm:flex-row sm:space-y-0 sm:gap-2 w-full sm:w-auto">
                        <!-- Data Period Toggles -->
                        <div class="flex flex-wrap gap-2 sm:mr-4">
                            <button id="todayButton"
                                class="flex-1 sm:flex-none px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 ease-in-out bg-blue-500 text-white">
                                <i class="fas fa-calendar-day mr-1"></i>Hari Ini
                            </button>
                            <button id="weekButton"
                                class="flex-1 sm:flex-none px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 ease-in-out bg-gray-200 text-gray-700">
                                <i class="fas fa-calendar-week mr-1"></i>Minggu Ini
                            </button>
                            <button id="monthButton"
                                class="flex-1 sm:flex-none px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 ease-in-out bg-gray-200 text-gray-700">
                                <i class="fas fa-calendar-alt mr-1"></i>Bulan Ini
                            </button>
                        </div>

                        <!-- Data Type Toggles -->
                        <div wire:ignore class="grid grid-cols-2 sm:flex gap-2">
                            <button id="blokButton"
                                class="px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 ease-in-out bg-blue-500 text-white">
                                <i class="fas fa-water mr-1"></i>Level Blok
                            </button>
                            <button id="paritButton"
                                class="px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 ease-in-out bg-gray-200 text-gray-700">
                                <i class="fas fa-stream mr-1"></i>Level Parit
                            </button>
                            <button id="sensorButton"
                                class="px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 ease-in-out bg-gray-200 text-gray-700">
                                <i class="fas fa-ruler-vertical mr-1"></i>Sensor Distance
                            </button>
                            <button id="rekapButton"
                                class="px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 ease-in-out bg-gray-200 text-gray-700">
                                <i class="fas fa-chart-bar mr-1"></i>Rekap
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Chart Container -->
                <div wire:ignore class="h-[300px] sm:h-[400px]">
                    <div id="combinedChart" class="w-full h-full"></div>
                </div>
            </div>

        </div>

        <!-- Bottom Section with Cards and Map -->
        <!-- Main Container -->
        <div class="bg-white p-6">
            <h2 class="text-lg font-medium mb-4">Lokasi Titik Water Level</h2>

            <!-- Main Grid: 2 Columns -->
            <div class="grid grid-cols-2 gap-6">
                <!-- Left Column: Location, Map and Gallery -->
                <div class="space-y-4">
                    <!-- Location Info Card -->
                    <div class="bg-blue-50 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <!-- Location Info -->
                            <div class="space-y-2">
                                <div class="flex items-center space-x-2">
                                    <svg class="h-5 w-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    </svg>
                                    <span class="text-sm text-gray-700">-2.2745234, 111.61404248</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <svg class="h-5 w-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                                    </svg>
                                    <span class="text-sm text-gray-700">Natal Baru Estate, PT MBAL</span>
                                </div>
                            </div>
                            @if (SuperAdmin())
                            <x-filament::modal :close-by-clicking-away="false" id="mapscordinates" width="5xl">
                                <x-slot name="trigger">
                                    @if (SuperAdmin())
                                    <x-filament::button
                                        icon="heroicon-o-map-pin"
                                        class="bg-indigo-500 text-white rounded-lg px-4 py-2 text-sm hover:bg-indigo-600"
                                        :disabled="!$selectedWilayah || !$selectedStation">
                                        Update Maps
                                    </x-filament::button>
                                    @endif
                                </x-slot>
                                <x-slot name="heading">
                                    Insert/Update Maps Coordinates
                                </x-slot>
                                <x-slot name="description">
                                    Click on the map to select coordinates or search for a location
                                </x-slot>
                                <form wire:submit="updateStationCoordinates">
                                    <div class="space-y-4">
                                        @if (!$selectedWilayah || !$selectedStation)
                                        <div class="p-4 bg-yellow-50 text-yellow-700 rounded-lg">
                                            Please select both Wilayah and Station before updating coordinates.
                                        </div>
                                        @endif

                                        <!-- Coordinates Display -->
                                        <div class="grid grid-cols-2 gap-4 mb-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Latitude</label>
                                                <input type="number"
                                                    step="any"
                                                    wire:model="selectedLat"
                                                    class="w-full rounded-lg border-gray-300"
                                                    placeholder="Enter latitude">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Longitude</label>
                                                <input type="number"
                                                    step="any"
                                                    wire:model="selectedLon"
                                                    class="w-full rounded-lg border-gray-300"
                                                    placeholder="Enter longitude">
                                            </div>
                                        </div>

                                        <!-- Add a note about input methods -->
                                        <div class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                            <p>You can update coordinates by:</p>
                                            <ul class="list-disc list-inside">
                                                <li>Clicking directly on the map</li>
                                                <li>Manually entering the coordinates above</li>
                                                <li>Using the search function on the map</li>
                                            </ul>
                                        </div>

                                        <!-- Submit Button -->
                                        <div
                                            <x-filament::button
                                            type="submit"
                                            :disabled="!$selectedWilayah || !$selectedStation">
                                            Save Coordinates
                                            </x-filament::button>
                                        </div>
                                    </div>
                                </form>
                            </x-filament::modal>
                            @endif
                        </div>
                    </div>

                    <!-- Map Section -->
                    <div class="relative">
                        <div wire:ignore id="map" class="w-full rounded-lg z-10" style="min-height: 400px;"></div>
                    </div>

                    <!-- Photo Gallery Section -->
                    <div class="bg-blue-50 rounded-lg p-4">
                        <!-- Upload Button -->
                        <div class="mb-4 flex">
                            <button class="bg-indigo-500 text-white rounded-lg px-4 py-2 text-sm hover:bg-indigo-600 ml-auto">
                                Upload foto
                            </button>
                        </div>

                        <!-- Gallery Grid -->
                        <div class="grid grid-cols-5 gap-2">
                            <div class="aspect-square bg-gray-100 rounded"></div>
                            <div class="aspect-square bg-gray-100 rounded"></div>
                            <div class="aspect-square bg-gray-100 rounded"></div>
                            <div class="aspect-square bg-gray-100 rounded"></div>
                            <div class="aspect-square bg-gray-100 rounded"></div>
                        </div>

                        <!-- Gallery Navigation Dots -->
                        <div class="flex justify-center space-x-1 mt-4">
                            <div class="w-2 h-2 rounded-full bg-indigo-500"></div>
                            <div class="w-2 h-2 rounded-full bg-gray-300"></div>
                            <div class="w-2 h-2 rounded-full bg-gray-300"></div>
                            <div class="w-2 h-2 rounded-full bg-gray-300"></div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Table Card -->
                <div class="bg-white rounded-lg shadow">
                    <div class="p-6">
                        <div class="mb-4 flex">
                            @if (SuperAdmin())
                            <x-filament::modal :close-by-clicking-away="false" id="waterlevel-modal">
                                <x-slot name="trigger">
                                    <x-filament::button
                                        icon="heroicon-o-arrow-up-tray"
                                        class="bg-indigo-500 text-white rounded-lg px-4 py-2 text-sm hover:bg-indigo-600 ml-auto">
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
    </div>

    @script
    <script type="module">
        let map = L.map('map', {
            preferCanvas: true,
        }).setView([-2.2745234, 111.61404248], 13);

        // Add tile layer
        L.tileLayer('http://{s}.google.com/vt?lyrs=s&x={x}&y={y}&z={z}', {
            maxZoom: 20,
            subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
        }).addTo(map);

        let layerGroup = L.layerGroup().addTo(map);


        // Add search control functionality
        const searchControl = L.Control.geocoder({
            defaultMarkGeocode: false
        }).addTo(map);
        let isUpdateMode = false;


        // Handle search results
        searchControl.on('markgeocode', function(e) {
            const bbox = e.geocode.bbox;
            const poly = L.polygon([
                bbox.getSouthEast(),
                bbox.getNorthEast(),
                bbox.getNorthWest(),
                bbox.getSouthWest()
            ]);
            map.fitBounds(poly.getBounds());

            const latlng = e.geocode.center;
            updateMarkerPosition(latlng);
        });

        // Handle map clicks
        map.on('click', function(e) {
            // Check if user is SuperAdmin and has selected both wilayah and station
            const hasRequiredSelections = @json(SuperAdmin() && !empty($selectedWilayah) && !empty($selectedStation));

            if (isUpdateMode && hasRequiredSelections) {
                updateMarkerPosition(e.latlng);
            }
        });

        let currentMarker = null;

        function updateMarkerPosition(latlng) {
            // Validate coordinates
            if (!isValidLatLng(latlng.lat, latlng.lng)) {
                console.warn('Invalid coordinates:', latlng);
                return;
            }

            // Clear existing marker
            if (currentMarker) {
                map.removeLayer(currentMarker);
            }

            // Add new marker
            currentMarker = L.marker(latlng, {
                draggable: true
            }).addTo(map);

            // Update coordinates in Livewire
            @this.dispatch('set-coordinates', {
                lat: latlng.lat,
                lng: latlng.lng
            });

            // Handle marker drag
            currentMarker.on('dragend', function(event) {
                const marker = event.target;
                const position = marker.getLatLng();
                @this.dispatch('set-coordinates', {
                    lat: position.lat,
                    lng: position.lng
                });
            });
        }

        // Add coordinate validation function
        function isValidLatLng(lat, lng) {
            return lat >= -90 && lat <= 90 && lng >= -180 && lng <= 180;
        }

        // Update the Livewire event listener to handle manual input



        $wire.on('set-coordinates', ({
            lat,
            lng
        }) => {
            if (lat && lng) {
                updateMarkerPosition(L.latLng(lat, lng));
                // If coordinates are valid, center the map on the new position
                map.setView([lat, lng], 15);
            }
        });

        // maps for station
        $wire.on('updateMapMarker', (eventData) => {
            // console.log(eventData);
            const data = Array.isArray(eventData) ? eventData[0] : eventData;
            const coordinates = data.coordinates;
            const station = data.station;
            // console.log(station);

            if (coordinates && coordinates.lat && coordinates.lon) {
                // Clear previous markers
                layerGroup.clearLayers();

                // Create marker with popup
                const marker = L.marker([coordinates.lat, coordinates.lon], {
                    title: station.location
                }).bindPopup(`
                        <div class="text-center">
                        <b>Tanggal: ${station.datetime ? station.datetime : 'No Data'}</b><br>
                        <b>Water Station: ${station.location}</b><br>
                        Level In Terakhir: ${station.level_in ? station.level_in : 'No Data'}<br>
                        Level Out Terakhir: ${station.level_out ? station.level_out : 'No Data'}<br>
                        Level Actual Terakhir: ${station.level_actual ? station.level_actual : 'No Data'}<br>
                        Rata rata Level In: ${station.level_in_avg ? station.level_in_avg : 'No Data'}<br>
                        Rata rata Level Out: ${station.level_out_avg ? station.level_out_avg : 'No Data'}<br>
                        Rata rata Level Actual: ${station.level_actual_avg ? station.level_actual_avg : 'No Data'}<br>
                        Batas Atas Air: ${station.batas_atas_air ? station.batas_atas_air : 'No Data'}<br>
                        Batas Bawah Air: ${station.batas_bawah_air ? station.batas_bawah_air : 'No Data'} 
                        </div>
                    `);

                // Add marker to layer group
                layerGroup.addLayer(marker);

                // Fit bounds with padding
                map.setView([coordinates.lat, coordinates.lon], 15);
                marker.openPopup();
            }
        });


        // untuk futur search 
        // Search functionality
        // Tambahkan pengecekan elemen sebelum menambahkan event listener
        const estateSearchElement = document.getElementById('estateSearch');
        if (estateSearchElement) {
            estateSearchElement.addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase();
                document.querySelectorAll('.estate-item').forEach(item => {
                    const estateName = item.querySelector('p').textContent.toLowerCase();
                    if (estateName.includes(searchTerm)) {
                        item.style.display = '';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        }
        // Toggle active state
        function toggleEstate(estateId) {
            document.querySelectorAll('.estate-item').forEach(item => {
                const isTarget = item.dataset.estate === estateId;
                const cardDiv = item.querySelector('div');

                if (isTarget) {
                    item.classList.add('active');
                    cardDiv.classList.remove('bg-white', 'shadow');
                    cardDiv.classList.add('bg-blue-50', 'border-2', 'border-blue-500');
                    const text = cardDiv.querySelectorAll('p');
                    text[0].classList.remove('text-gray-500');
                    text[0].classList.add('text-blue-700');
                    text[1].classList.remove('text-gray-900');
                    text[1].classList.add('text-blue-900');
                } else {
                    item.classList.remove('active');
                    cardDiv.classList.remove('bg-blue-50', 'border-2', 'border-blue-500');
                    cardDiv.classList.add('bg-white', 'shadow');
                    const text = cardDiv.querySelectorAll('p');
                    text[0].classList.remove('text-blue-700');
                    text[0].classList.add('text-gray-500');
                    text[1].classList.remove('text-blue-900');
                    text[1].classList.add('text-gray-900');
                }
            });
        }

        // Initialize variables
        let chart;
        let currentView = 'blok';
        let currentPeriod = 'today';

        // Define style configurations for each view type
        const styleConfigs = {
            blok: {
                colors: ['#3B82F6'], // Blue
                gradient: {
                    shade: 'dark',
                    type: "vertical",
                    shadeIntensity: 0.5,
                    gradientToColors: ['#2563eb'],
                    inverseColors: true,
                    opacityFrom: 0.8,
                    opacityTo: 0.2,
                    stops: [0, 100]
                }
            },
            parit: {
                colors: ['#10B981'], // Green
                gradient: {
                    shade: 'dark',
                    type: "vertical",
                    shadeIntensity: 0.5,
                    gradientToColors: ['#059669'],
                    inverseColors: true,
                    opacityFrom: 0.8,
                    opacityTo: 0.2,
                    stops: [0, 100]
                }
            },
            sensor: {
                colors: ['#F59E0B'], // Orange
                gradient: {
                    shade: 'dark',
                    type: "vertical",
                    shadeIntensity: 0.5,
                    gradientToColors: ['#D97706'],
                    inverseColors: true,
                    opacityFrom: 0.8,
                    opacityTo: 0.2,
                    stops: [0, 100]
                }
            },
            rekap: {
                colors: ['#3B82F6', '#10B981', '#F59E0B'],
                gradient: {
                    shade: 'dark',
                    type: "vertical",
                    shadeIntensity: 0.5,
                    opacityFrom: 0.8,
                    opacityTo: 0.2,
                    stops: [0, 100]
                }
            }
        };

        // Chart options
        const chartOptions = {
            chart: {
                type: 'area',
                height: '100%',
                animations: {
                    enabled: true,
                    easing: 'easeinout',
                    dynamicAnimation: {
                        speed: 1000
                    }
                },
                toolbar: {
                    show: true
                },
                zoom: {
                    enabled: true
                }
            },
            stroke: {
                curve: 'smooth',
                width: 2
            },
            xaxis: {
                type: 'datetime',
                labels: {
                    datetimeUTC: false,
                    format: 'dd MMM HH:mm'
                },
                title: {
                    text: 'Waktu'
                }
            },
            yaxis: {
                title: {
                    text: 'Level (cm)'
                },
                labels: {
                    formatter: (value) => value.toFixed(2)
                }
            },
            tooltip: {
                x: {
                    format: 'dd MMM yyyy HH:mm'
                },
                y: {
                    formatter: (value) => `${value.toFixed(2)} cm`
                }
            },
            legend: {
                position: 'top',
                horizontalAlign: 'center'
            },
            grid: {
                borderColor: '#f1f1f1'
            },
            noData: {
                text: 'No Data...'
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shade: 'dark',
                    type: "vertical",
                    shadeIntensity: 0.5,
                    opacityFrom: 0.8,
                    opacityTo: 0.2,
                    stops: [0, 100]
                }
            }
        };

        function updateChart(data, type = currentView, period = currentPeriod) {
            currentView = type;
            currentPeriod = period;

            // Update button states
            updateButtonStates();

            // Update chart options with new style config
            const newOptions = {
                ...chartOptions,
                colors: styleConfigs[type].colors,
                fill: {
                    ...chartOptions.fill,
                    gradient: styleConfigs[type].gradient
                },
                series: data.series
            };

            if (chart) {
                chart.destroy();
            }

            chart = new ApexCharts(document.querySelector("#combinedChart"), newOptions);
            chart.render();
        }

        function updateButtonStates() {
            // Update period buttons
            ['today', 'week', 'month'].forEach(period => {
                const button = document.getElementById(`${period}Button`);
                if (button) {
                    if (period === currentPeriod) {
                        button.classList.add('bg-blue-500', 'text-white');
                        button.classList.remove('bg-gray-200', 'text-gray-700');
                    } else {
                        button.classList.remove('bg-blue-500', 'text-white');
                        button.classList.add('bg-gray-200', 'text-gray-700');
                    }
                }
            });

            // Update type buttons
            ['blok', 'parit', 'sensor', 'rekap'].forEach(type => {
                const button = document.getElementById(`${type}Button`);
                if (button) {
                    if (type === currentView) {
                        button.classList.add('bg-blue-500', 'text-white');
                        button.classList.remove('bg-gray-200', 'text-gray-700');
                    } else {
                        button.classList.remove('bg-blue-500', 'text-white');
                        button.classList.add('bg-gray-200', 'text-gray-700');
                    }
                }
            });
        }

        // Button click handlers
        document.getElementById('todayButton').addEventListener('click', () => {
            currentPeriod = 'today';
            updateButtonStates();
            $wire.updateChart('today', currentView);
        });

        document.getElementById('weekButton').addEventListener('click', () => {
            currentPeriod = 'week';
            updateButtonStates();
            $wire.updateChart('week', currentView);
        });

        document.getElementById('monthButton').addEventListener('click', () => {
            currentPeriod = 'month';
            updateButtonStates();
            $wire.updateChart('month', currentView);
        });

        document.getElementById('blokButton').addEventListener('click', () => {
            currentView = 'blok';
            updateButtonStates();
            $wire.updateChart(currentPeriod, 'blok');
        });

        document.getElementById('paritButton').addEventListener('click', () => {
            currentView = 'parit';
            updateButtonStates();
            $wire.updateChart(currentPeriod, 'parit');
        });

        document.getElementById('sensorButton').addEventListener('click', () => {
            currentView = 'sensor';
            updateButtonStates();
            $wire.updateChart(currentPeriod, 'sensor');
        });

        document.getElementById('rekapButton').addEventListener('click', () => {
            currentView = 'rekap';
            updateButtonStates();
            $wire.updateChart(currentPeriod, 'rekap');
        });

        // Listen for chart updates from Livewire
        $wire.on('updateChart', (response) => {
            if (Array.isArray(response)) {
                response = response[0];
            }
            updateChart(response.data, response.type, response.period);
        });

        // Listen for initial state
        $wire.on('initChartState', (state) => {
            if (Array.isArray(state)) {
                state = state[0];
            }
            currentView = state.type;
            currentPeriod = state.period;
            updateButtonStates();
        });
    </script>
    @endscript
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
@endpush