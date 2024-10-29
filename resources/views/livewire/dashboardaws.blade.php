<div>
    <div class="container mx-auto px-4 py-6">
        <!-- Header Section -->
        <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between mb-8 gap-4">


            <!-- Station Selector -->
            <div class="w-full lg:w-64 select-container">
                <label for="station" class="block text-sm font-medium text-gray-700 mb-1 dark:text-white">Select Station</label>
                <div class="relative">
                    <select id="station"
                        wire:model="selectedstation"
                        wire:change="updateSelectedStation($event.target.value)"
                        class="block w-full rounded-lg border border-gray-300 bg-white py-2 pl-3 pr-10 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 appearance-none">
                        <option value="">Choose a station</option>
                        @foreach($list_station as $station)
                        <option value="{{ $station->id }}">{{ $station->loc }}</option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute right-2 top-1/2 -translate-y-1/2">
                        <svg class="h-4 w-4 fill-current text-gray-700 dark:text-gray-300" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>
            </div>
            <!-- Station Info -->
            <div class="flex flex-col">
                <h1 class="text-2xl font-semibold text-gray-800 dark:text-white">Current Weather</h1>
                <span class="text-gray-500 dark:text-gray-400 text-sm">SSMS AWS</span>
            </div>


        </div>

        <!-- Main Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- First Column: Weather Cards -->
            <div class="lg:col-span-1">
                <!-- Weather Card -->
                <div class="weather-card bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow overflow-hidden relative">
                    <!-- Weather Effect Overlay -->
                    <div class="absolute inset-0 pointer-events-none weather-effects">
                        @if($weather_data['temperature']['condition'] === 'Rain')
                        <div class="rain-effect"></div>
                        @elseif($weather_data['temperature']['condition'] === 'Snow')
                        <div class="snow-effect"></div>
                        @elseif($weather_data['temperature']['condition'] === 'Cloudy')
                        <div class="clouds-effect"></div>
                        @endif
                    </div>

                    <div class="relative z-10">
                        <div class="flex items-center gap-2 mb-4">
                            <!-- Dynamic Weather Icon -->
                            <div class="weather-icon text-4xl">
                                @switch($weather_data['temperature']['condition'])
                                @case('Clear')
                                <i class="fas fa-sun text-yellow-500 animate-spin-slow"></i>
                                @break
                                @case('Rain')
                                <i class="fas fa-cloud-rain text-blue-500 animate-bounce-gentle"></i>
                                @break
                                @case('Cloudy')
                                <i class="fas fa-cloud text-gray-500 animate-pulse"></i>
                                @break
                                @case('Storm')
                                <i class="fas fa-bolt text-yellow-500 animate-flash"></i>
                                @break
                                @default
                                <i class="fas fa-cloud text-blue-500"></i>
                                @break
                                @endswitch
                            </div>
                            <span class="font-semibold text-gray-800 dark:text-white">Weather Conditions</span>
                        </div>
                        <div class="mb-6">
                            <div class="flex items-end gap-2">
                                <span class="text-4xl font-bold text-gray-800 dark:text-white">{{ $weather_data['temperature']['current'] }}°C</span>
                                <span class="text-gray-500 dark:text-white mb-1 ">
                                    H: {{ $weather_data['temperature']['max'] }}° L: {{ $weather_data['temperature']['min'] }}°
                                </span>
                            </div>
                            <div class="text-gray-600 dark:text-gray-300 mt-1">
                                {{ $weather_data['temperature']['condition'] }}
                            </div>
                        </div>
                        <div class="grid grid-cols-3 gap-3">
                            <div class="bg-gray-900 text-white rounded-xl p-3 transition-transform hover:scale-105">
                                <div class="text-xs font-medium mb-1">Pressure</div>
                                <div class="font-semibold">{{ $weather_data['temperature']['pressure'] }}mb</div>
                            </div>
                            <div class="bg-lime-100 rounded-xl p-3 transition-transform hover:scale-105">
                                <div class="text-xs font-medium mb-1 dark:text-black">Wind</div>
                                <div class="font-semibold dark:text-black">{{ $weather_data['wind']['speed'] }}km/h</div>
                            </div>
                            <div class="bg-blue-50 rounded-xl p-3 transition-transform hover:scale-105">
                                <div class="text-xs font-medium mb-1 dark:text-black">Humidity</div>
                                <div class="font-semibold dark:text-black">{{ $weather_data['temperature']['humidity'] }}%</div>
                            </div>
                        </div>
                    </div>

                    <!-- Wind Status Card -->
                    <div class="weather-card bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow overflow-hidden relative">
                        <div class="flex items-center gap-2 mb-4">
                            <i class="fas fa-wind text-blue-500"></i>
                            <span class="font-semibold text-gray-800 dark:text-white">Wind Status</span>
                        </div>
                        <div class="text-4xl font-bold text-gray-800 mb-2 dark:text-white">{{ $weather_data['wind']['speed'] }} km/h</div>
                        <div class="flex items-center gap-2 text-gray-600 mb-6">
                            <i class="fas fa-compass text-blue-500"></i>
                            <span class="dark:text-gray-400">{{ $weather_data['wind']['direction'] }}</span>
                        </div>
                        <div class="mt-4 p-4 bg-gray-50 rounded-xl">
                            <div class="text-sm font-medium text-gray-600">Max Gust Today</div>
                            <div class="text-xl font-bold text-gray-800">{{ $weather_data['wind']['gust'] }} km/h</div>
                        </div>
                    </div>

                    <!-- Solar Radiation -->
                    <div class="weather-card bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow overflow-hidden relative">
                        <div class="flex items-center gap-2 mb-6">
                            <i class="fas fa-solar-panel text-yellow-500"></i>
                            <h2 class="font-semibold text-gray-800 dark:text-white">Solar Radiation</h2>
                        </div>
                        <div class="text-4xl font-bold text-gray-800 mb-6 dark:text-white">
                            {{ $weather_data['solar']['radiation'] }} W/m²
                        </div>
                        <div class="p-4 bg-gray-50 rounded-xl">
                            <div class="flex items-center justify-between">
                                <div class="text-gray-600 font-medium">Battery Status</div>
                                <div class="text-gray-800 font-bold">{{ $weather_data['solar']['battery'] }}%</div>
                            </div>
                        </div>
                        <div class="mt-4 text-sm">
                            <div class="flex justify-between items-center">
                                <span>Average Today:</span>
                                <span>{{ $weather_data['solar']['avg_today'] }} W/m²</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2">
                <!-- UV and Rainfall Cards in one line -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- UV Index Card -->
                    <div class="weather-card bg-gray-900 text-white rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden" style="background-image: url('/img/18635.jpg'); background-size: cover; background-position: center;">
                        <div class="absolute inset-0 bg-black bg-opacity-50"></div>
                        <div class="relative z-10">
                            <div class="flex items-center gap-2 mb-4">
                                <i class="fas fa-sun text-yellow-400"></i>
                                <span class="font-semibold">UV Index</span>
                            </div>
                            <div class="text-4xl font-bold mb-3">{{ $weather_data['uv']['value'] }} UVI</div>
                            <div class="inline-block bg-green-500 text-xs px-3 py-1 rounded-full font-medium">
                                {{ $weather_data['uv']['level'] }}
                            </div>
                            <p class="text-gray-200 mt-4">{{ $weather_data['uv']['description'] }}</p>
                            <div class="mt-4 text-sm">
                                <div class="flex justify-between items-center">
                                    <span>Max UV Today:</span>
                                    <span>{{ $weather_data['uv']['max_today'] }} UVI</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Rainfall Data Card -->
                    <div class="weather-card bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow overflow-hidden relative">
                        <div class="flex items-center gap-2 mb-6">
                            <i class="fas fa-cloud-rain text-blue-500"></i>
                            <h2 class="font-semibold text-gray-800 dark:text-white">Rainfall Data</h2>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="text-center p-4 bg-blue-50 rounded-xl">
                                <div class="text-xl font-bold text-gray-800">{{ $weather_data['rain']['rate'] }} mm/h</div>
                                <div class="text-sm text-gray-600 mt-1">Current Rate</div>
                            </div>
                            <div class="text-center p-4 bg-blue-50 rounded-xl">
                                <div class="text-xl font-bold text-gray-800">{{ $weather_data['rain']['today'] }} mm</div>
                                <div class="text-sm text-gray-600 mt-1">Today</div>
                            </div>
                            <div class="text-center p-4 bg-blue-50 rounded-xl">
                                <div class="text-xl font-bold text-gray-800">{{ $weather_data['rain']['weekly'] }} mm</div>
                                <div class="text-sm text-gray-600 mt-1">This Week</div>
                            </div>
                            <div class="text-center p-4 bg-blue-50 rounded-xl">
                                <div class="text-xl font-bold text-gray-800">{{ $weather_data['rain']['monthly'] }} mm</div>
                                <div class="text-sm text-gray-600 mt-1">This Month</div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Charts Section -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Temperature Chart -->
                    <div class="weather-card bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow overflow-hidden relative">
                        <div class="flex items-center gap-2 mb-6">
                            <i class="fas fa-temperature-high text-red-500"></i>
                            <h2 class="font-semibold text-gray-800 dark:text-white">Temperature History</h2>
                        </div>
                        <div wire:ignore class="h-[400px]">
                            <div id="temperatureChart" class="w-full h-full"></div>
                        </div>
                    </div>

                    <!-- Rainfall Chart -->
                    <div class="weather-card bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow overflow-hidden relative">
                        <div class="flex items-center gap-2 mb-6">
                            <i class="fas fa-cloud-rain text-blue-500"></i>
                            <h2 class="font-semibold text-gray-800 dark:text-white">Rainfall History</h2>
                        </div>
                        <div wire:ignore class="h-[400px]">
                            <div id="rainfallChart" class="w-full h-full"></div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <script>
            document.addEventListener('livewire:initialized', function() {
                const chartOptions = {
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
                        text: null
                    },
                    xAxis: {
                        type: 'datetime',
                        labels: {
                            format: '{value:%H:%M}',
                            rotation: 0,
                            align: 'center'
                        },
                        tickInterval: 2 * 3600 * 1000, // Show tick every 2 hours
                        min: new Date(new Date().setHours(0, 0, 0, 0)).getTime(), // Start at 00:00
                        max: new Date(new Date().setHours(23, 59, 59, 999)).getTime() // End at 23:59
                    },
                    yAxis: {
                        title: {
                            text: 'Temperature (°C)'
                        },
                        labels: {
                            format: '{value}°C'
                        }
                    },
                    tooltip: {
                        headerFormat: '<b>{point.x:%Y-%m-%d %H:%M}</b><br/>',
                        pointFormat: '{point.y:.1f}°C'
                    },
                    series: [{
                        name: 'Temperature',
                        data: @js($tempChartData),
                        color: '#EF4444',
                        marker: {
                            enabled: true,
                            radius: 3
                        }
                    }]
                };

                const temperatureChart = Highcharts.chart('temperatureChart', chartOptions);

                const rainfallChart = Highcharts.chart('rainfallChart', {
                    chart: {
                        type: 'column',
                        backgroundColor: 'transparent'
                    },
                    title: {
                        text: null
                    },
                    xAxis: {
                        type: 'datetime',
                        labels: {
                            format: '{value:%H:%M}'
                        }
                    },
                    yAxis: {
                        title: {
                            text: 'Rainfall (mm/h)'
                        },
                        min: 0
                    },
                    tooltip: {
                        headerFormat: '<b>{point.x:%Y-%m-%d %H:%M}</b><br/>',
                        pointFormat: '{point.y:.1f} mm/h'
                    },
                    series: [{
                        name: 'Rainfall',
                        data: @js($rainChartData),
                        color: '#3B82F6',
                    }]
                });

                // Update charts when Livewire updates
                Livewire.on('chartDataUpdated', (eventData) => {
                    console.log(eventData);

                    const data = eventData.detail[0]; // Access the data from the event
                    temperatureChart.series[0].setData(data.tempData, true);
                    rainfallChart.series[0].setData(data.rainData, true);
                });
            });
        </script>
    </div>


</div>