<div>
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
        <div class="grid grid-cols-1 gap-6">
            <!-- Chart Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="p-6">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">
                        <i class="fas fa-chart-line mr-2"></i>Water Level Trend
                    </h2>
                    <!-- Add a wrapper div with scrolling -->
                    <div class="overflow-x-auto">
                        <div class="w-full" style="height: 400px;">
                            <!-- Set minimum width for the chart container -->
                            <div wire:ignore id="container" style="min-width: 1200px; width: 100%;"></div>
                        </div>
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
            Livewire.on('updateChartData', (data) => {
                // console.log('Received data:', data); // Debug log

                // Format the data for Highcharts
                const formattedData = data[0]; // Since your data is in an array
                const timestamps = formattedData.datetime.map(time => {
                    // Convert time string to timestamp
                    const today = new Date();
                    const [hours, minutes, seconds] = time.split(':');
                    today.setHours(hours, minutes, seconds);
                    return today.getTime();
                });

                // Create series data arrays
                const levelInData = timestamps.map((time, index) => [time, formattedData.levelIn[index]]);
                const levelOutData = timestamps.map((time, index) => [time, formattedData.levelOut[index]]);
                const levelActualData = timestamps.map((time, index) => [time, formattedData.levelActual[index]]);

                // Update each series with new data
                chart.series[0].setData(levelInData, false);
                chart.series[1].setData(levelOutData, false);
                chart.series[2].setData(levelActualData, true); // true to redraw chart once after all series are updated
            });
        });

        // Initialize the chart with proper configuration
        const chart = Highcharts.chart('container', {
            chart: {
                type: 'line',
                height: 400,
                zoomType: 'x', // Adds horizontal zoom capability
                panning: true,
                panKey: 'shift', // Enable panning while holding shift key
                style: {
                    fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif'
                },
                animation: {
                    duration: 1500,
                    easing: 'easeOutBounce'
                },
                reflow: true
            },
            title: {
                text: 'Water Level Measurements',
                style: {
                    fontSize: '16px',
                    fontWeight: 'bold'
                }
            },
            xAxis: {
                type: 'datetime',
                labels: {
                    format: '{value:%Y-%m-%d %H:%M}',
                    rotation: -45,
                    align: 'right'
                },
                title: {
                    text: 'Date & Time'
                },
                gridLineWidth: 1,
                tickInterval: 3600 * 1000, // Show ticks every hour
                scrollbar: {
                    enabled: true // Adds a scrollbar to the x-axis
                },
                min: null, // Allow dynamic range
                max: null
            },
            yAxis: {
                title: {
                    text: 'Water Level (m)'
                },
                gridLineWidth: 1
            },
            series: [{
                name: 'Level In',
                data: [],
                color: '#2196F3',
                marker: {
                    enabled: true,
                    radius: 4
                }
            }, {
                name: 'Level Out',
                data: [],
                color: '#4CAF50',
                marker: {
                    enabled: true,
                    radius: 4
                }
            }, {
                name: 'Level Actual',
                data: [],
                color: '#FFC107',
                marker: {
                    enabled: true,
                    radius: 4
                }
            }],
            tooltip: {
                shared: true,
                crosshairs: true,
                formatter: function() {
                    let tooltip = '<b>' + Highcharts.dateFormat('%Y-%m-%d %H:%M', this.x) + '</b><br/>';
                    this.points.forEach(function(point) {
                        tooltip += '<span style="color:' + point.series.color + '">●</span> ' +
                            point.series.name + ': <b>' + point.y.toFixed(2) + ' m</b><br/>';
                    });
                    return tooltip;
                }
            },
            legend: {
                enabled: true,
                align: 'center',
                verticalAlign: 'bottom'
            },
            plotOptions: {
                series: {
                    animation: {
                        duration: 1500,
                        // Custom animation for each line
                        events: {
                            afterAnimate: function() {
                                // Optional: Add any post-animation effects here
                            }
                        }
                    },
                    lineWidth: 2,
                    // Add line drawing animation
                    animation: {
                        duration: 1500,
                        defer: 500 // Delay between series
                    }
                }
            },
            credits: {
                enabled: false
            },
            accessibility: {
                enabled: false
            }
        });
    </script>
</div>