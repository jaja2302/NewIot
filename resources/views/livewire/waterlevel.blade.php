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
                <x-filament::modal :close-by-clicking-away="false" id="mapscordinates" width="5xl">
                    <x-slot name="trigger">
                        @if (SuperAdmin())
                        <x-filament::button
                            icon="heroicon-o-map-pin"
                            class="bg-white bg-opacity-20 hover:bg-opacity-30 transition-all duration-300"
                            :disabled="!$selectedWilayah || !$selectedStation">
                            Insert/Update Maps Coordinates
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
                            <div class="flex justify-end mt-4">
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
        // for chart 
        $wire.on('updateChartData', (eventData) => {
            let currentPeriod = 'today'; // Default period
            let chart = null;
            // console.log(eventData);
            // Chart options
            const chartOptions = {
                series: [{
                        name: 'Level In',
                        data: []
                    },
                    {
                        name: 'Level Out',
                        data: []
                    },
                    {
                        name: 'Level Actual',
                        data: []
                    },
                    {
                        name: 'Batas Atas',
                        data: []
                    },
                    {
                        name: 'Batas Bawah',
                        data: []
                    }
                ],
                chart: {
                    type: 'line',
                    height: 400,
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
                        },
                        autoSelected: 'zoom'
                    },
                    dropShadow: {
                        enabled: true,
                        color: '#000',
                        top: 18,
                        left: 7,
                        blur: 10,
                        opacity: 0.2
                    },
                    background: 'transparent'
                },
                colors: ['#2563eb', '#16a34a', '#dc2626', '#ea580c', '#ca8a04'],
                stroke: {
                    curve: 'smooth',
                    width: 3
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shade: 'dark',
                        type: "vertical",
                        shadeIntensity: 0.5,
                        gradientToColors: undefined,
                        inverseColors: true,
                        opacityFrom: 0.8,
                        opacityTo: 0.2,
                        stops: [0, 100]
                    }
                },
                dataLabels: {
                    enabled: false
                },
                grid: {
                    borderColor: '#e0e0e0',
                    strokeDashArray: 5,
                    xaxis: {
                        lines: {
                            show: true
                        }
                    },
                    yaxis: {
                        lines: {
                            show: true
                        }
                    },
                    padding: {
                        top: 0,
                        right: 0,
                        bottom: 0,
                        left: 0
                    },
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
                        style: {
                            colors: '#666',
                            fontSize: '12px',
                            fontFamily: 'Helvetica, Arial, sans-serif',
                            fontWeight: 400,
                        },
                        datetimeFormatter: {
                            year: 'yyyy',
                            month: "MMM 'yy",
                            day: 'dd MMM',
                            hour: 'HH:mm'
                        }
                    },
                    tooltip: {
                        enabled: false
                    }
                },
                yaxis: {
                    title: {
                        text: 'Water Level',
                        style: {
                            fontSize: '14px',
                            fontWeight: 600,
                            fontFamily: 'Helvetica, Arial, sans-serif',
                        }
                    },
                    labels: {
                        style: {
                            colors: '#666',
                            fontSize: '12px',
                            fontFamily: 'Helvetica, Arial, sans-serif',
                            fontWeight: 400,
                        }
                    }
                },
                tooltip: {
                    shared: true,
                    intersect: false,
                    x: {
                        format: 'dd MMM yyyy HH:mm'
                    },
                    y: {
                        formatter: function(val) {
                            return val.toFixed(2)
                        }
                    },
                    theme: 'dark',
                    style: {
                        fontSize: '12px',
                        fontFamily: 'Helvetica, Arial, sans-serif',
                    },
                    marker: {
                        show: true,
                    }
                },
                legend: {
                    show: true,
                    position: 'top',
                    horizontalAlign: 'right',
                    floating: true,
                    offsetY: -25,
                    offsetX: -5,
                    markers: {
                        width: 12,
                        height: 12,
                        strokeWidth: 0,
                        strokeColor: '#fff',
                        radius: 12,
                        customHTML: undefined,
                        onClick: undefined,
                        offsetX: 0,
                        offsetY: 0
                    }
                },
                theme: {
                    mode: 'light',
                    palette: 'palette1',
                    monochrome: {
                        enabled: false,
                        color: '#255aee',
                        shadeTo: 'light',
                        shadeIntensity: 0.65
                    }
                },
                responsive: [{
                    breakpoint: 640,
                    options: {
                        chart: {
                            height: 300 // Adjust height for mobile
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }]
            };

            // Initialize chart
            chart = new ApexCharts(document.querySelector("#container"), chartOptions);
            chart.render();

            // Function to update chart data
            const updateChart = (period) => {
                const data = eventData[0][period];
                chart.updateSeries([{
                        name: 'Level In',
                        data: data.levelIn
                    },
                    {
                        name: 'Level Out',
                        data: data.levelOut
                    },
                    {
                        name: 'Level Actual',
                        data: data.levelActual
                    },
                    {
                        name: 'Batas Atas',
                        data: data.batasAtas
                    },
                    {
                        name: 'Batas Bawah',
                        data: data.batasBawah
                    }
                ]);
            };

            // Initial chart update
            updateChart(currentPeriod);

            // Add click handlers for period buttons
            document.querySelectorAll('.period-btn').forEach(button => {
                button.addEventListener('click', (e) => {
                    // Remove active class from all buttons
                    document.querySelectorAll('.period-btn').forEach(btn => {
                        btn.classList.remove('bg-blue-500', 'text-white');
                        btn.classList.add('bg-gray-200', 'text-gray-700');
                    });

                    // Add active class to clicked button
                    e.target.classList.remove('bg-gray-200', 'text-gray-700');
                    e.target.classList.add('bg-blue-500', 'text-white');

                    // Update chart
                    const period = e.target.dataset.period;
                    currentPeriod = period;
                    updateChart(period);
                });
            });
        });
    </script>
    @endscript
</div>