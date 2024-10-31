<div class="min-h-screen">
    <div class="scroll-section">
        <div class="container mx-auto px-4 py-6">
            <!-- Header Section -->
            <div class="flex flex-row items-center justify-between mb-8 gap-4 weather-card">
                <h1 class="text-2xl font-semibold text-gray-800 dark:text-white">Stasiun Cuaca</h1>
                <!-- Station Info and Selector -->
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
                                <span class="font-semibold text-gray-800 dark:text-white">Kondisi Cuaca</span>
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
                                    <div class="text-xs font-medium mb-1">Tekanan</div>
                                    <div class="font-semibold">{{ $weather_data['temperature']['pressure'] }}mb</div>
                                </div>
                                <div class="bg-lime-100 rounded-xl p-3 transition-transform hover:scale-105">
                                    <div class="text-xs font-medium mb-1 dark:text-black">Angin</div>
                                    <div class="font-semibold dark:text-black">{{ $weather_data['wind']['speed'] }}km/h</div>
                                </div>
                                <div class="bg-blue-50 rounded-xl p-3 transition-transform hover:scale-105">
                                    <div class="text-xs font-medium mb-1 dark:text-black">Kelembapan</div>
                                    <div class="font-semibold dark:text-black">{{ $weather_data['temperature']['humidity'] }}%</div>
                                </div>
                            </div>
                        </div>

                        <!-- Wind Status Card -->
                        <div class="weather-card bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow overflow-hidden relative mt-4">
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
                        <div class="weather-card bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow overflow-hidden relative mt-4">
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

            <!-- Latest Data Section -->
            <div class="weather-card bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow overflow-hidden relative mt-6">
                <h2 class="font-semibold text-gray-800 dark:text-white">Latest Data</h2>
                <div>
                    @if($latest_data)
                    <p>Temperature: {{ $latest_data->temp_out }}°C</p>
                    <p>Humidity: {{ $latest_data->hum_out }}%</p>
                    <p>Wind Speed: {{ $latest_data->windspeedkmh }} km/h</p>
                    <p>Rain Rate: {{ $latest_data->rain_rate }} mm/h</p>
                    @else
                    <p>No data available.</p>
                    @endif
                </div>
            </div>

            <!-- Today's Data Section -->
            <div class="weather-card bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow overflow-hidden relative mt-6">
                <h2 class="font-semibold text-gray-800 dark:text-white">Today's Data</h2>
                <div>
                    @if($today_data->isNotEmpty())
                    @foreach($today_data as $data)
                    <p>Time: {{ \Carbon\Carbon::parse($data->date)->format('H:i') }} - Temp: {{ $data->temp_out }}°C</p>
                    @endforeach
                    @else
                    <p>No data available for today.</p>
                    @endif
                </div>
            </div>

            <!-- 5 Days Ahead Data Section -->
            <div class="weather-card bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow overflow-hidden relative mt-6">
                <h2 class="font-semibold text-gray-800 dark:text-white">5 Days Ahead</h2>
                <div>
                    @if($five_days_ahead_data->isNotEmpty())
                    @foreach($five_days_ahead_data as $data)
                    <p>Date: {{ \Carbon\Carbon::parse($data->date)->format('Y-m-d') }} - Temp: {{ $data->temp_out }}°C</p>
                    @endforeach
                    @else
                    <p>No data available for the next 5 days.</p>
                    @endif
                </div>
            </div>

            <!-- Wind Statistics Section -->
            <div class="weather-card bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow overflow-hidden relative mt-6">
                <h2 class="font-semibold text-gray-800 dark:text-white">Wind Statistics</h2>
                <div>
                    @if($wind_statistics->isNotEmpty())
                    <p>Average Wind Speed: {{ $wind_statistics->avg('windspeedkmh') }} km/h</p>
                    <p>Max Gust: {{ $wind_statistics->max('wind_gust') }} km/h</p>
                    <p>Wind Direction: {{ $wind_statistics->first()->winddir }}°</p>
                    @else
                    <p>No wind data available.</p>
                    @endif
                </div>
            </div>

            <!-- Humidity Levels Section -->
            <div class="weather-card bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow overflow-hidden relative mt-6">
                <h2 class="font-semibold text-gray-800 dark:text-white">Humidity Levels</h2>
                <div>
                    @if($humidity_levels->isNotEmpty())
                    <p>Indoor Humidity: {{ $humidity_levels->first()->hum_in }}%</p>
                    <p>Outdoor Humidity: {{ $humidity_levels->first()->hum_out }}%</p>
                    @else
                    <p>No humidity data available.</p>
                    @endif
                </div>
            </div>

            <!-- Pressure Levels Section -->
            <div class="weather-card bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow overflow-hidden relative mt-6">
                <h2 class="font-semibold text-gray-800 dark:text-white">Pressure Levels</h2>
                <div>
                    @if($pressure_levels->isNotEmpty())
                    <p>Current Pressure: {{ $pressure_levels->first()->air_press_rel }} hPa</p>
                    @else
                    <p>No pressure data available.</p>
                    @endif
                </div>
            </div>

            <!-- Rainfall Statistics Section -->
            <div class="weather-card bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow overflow-hidden relative mt-6">
                <h2 class="font-semibold text-gray-800 dark:text-white">Rainfall Statistics</h2>
                <div>
                    @if($rainfall_statistics->isNotEmpty())
                    <p>Total Rainfall Today: {{ $rainfall_statistics->sum('rain_rate') }} mm</p>
                    <p>Total Rainfall This Week: {{ $rainfall_statistics->sum('rain_today') }} mm</p>
                    <p>Total Rainfall This Month: {{ $rainfall_statistics->sum('monthlyrainmm') }} mm</p>
                    @else
                    <p>No rainfall data available.</p>
                    @endif
                </div>
            </div>

            <!-- UV Index Section -->
            <div class="weather-card bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow overflow-hidden relative mt-6">
                <h2 class="font-semibold text-gray-800 dark:text-white">UV Index</h2>
                <div>
                    @if($uv_index->isNotEmpty())
                    <p>Current UV Index: {{ $uv_index->first()->uv }}</p>
                    <p>UV Level: {{ $this->getUVLevel($uv_index->first()->uv) }}</p>
                    @else
                    <p>No UV data available.</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="container mx-auto px-4 py-6">
            <div class="weather-card bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow overflow-hidden relative">
                <div class="flex items-center gap-2 mb-6">
                    <i class="fas fa-cloud-rain text-blue-500"></i>
                    <h2 class="font-semibold text-gray-800 dark:text-white">Rainfall History</h2>
                </div>
                {{$this->table}}
            </div>

        </div>
    </div>

    <div class="scroll-indicator scroll-up">
        <i class="fas fa-chevron-up"></i>
    </div>

    <div class="scroll-indicator scroll-down">
        <i class="fas fa-chevron-down"></i>
    </div>

    <script type="module">
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
                // console.log(eventData);

                const data = eventData.detail[0]; // Access the data from the event
                temperatureChart.series[0].setData(data.tempData, true);
                rainfallChart.series[0].setData(data.rainData, true);
            });




            // initializeScrollNavigation(
            //     "{{ route('waterlevel') }}", // Up route
            //     "{{ route('dashboard') }}" // Down route
            // );
        });
    </script>
</div>