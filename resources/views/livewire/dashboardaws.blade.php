<div class="min-h-screen">
    <div class="container mx-auto px-4 py-6">
        <!-- Header Section -->
        <div class="flex flex-row items-center justify-between mb-8 gap-4 weather-card">
            <h1 class="text-2xl font-semibold text-gray-800 dark:text-white">Stasiun</h1>
            <div class="flex gap-4">
                <!-- Existing station selector -->
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

                <!-- New date selector -->

            </div>
        </div>

        <!-- Main Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- First Column: Weather Cards -->
            <div class="lg:col-span-1">
                <!-- Weather Card -->

                <div class="weather-card bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow overflow-hidden relative">

                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2">
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
                        <div class="relative">
                            <input
                                type="date"
                                wire:change="updateSelectedDate($event.target.value)"
                                wire:model.live="selectedDate"
                                class="block w-full rounded-lg border border-gray-300 bg-white py-2 pl-3 pr-10 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300"
                                max="{{ now()->format('Y-m-d') }}">
                        </div>
                    </div>
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
                                <div class="flex items-center gap-2 mb-1">
                                    <i class="fas fa-tachometer-alt text-blue-400"></i>
                                    <div class="text-xs font-medium">Tekanan</div>
                                </div>
                                <div class="font-semibold">{{ $weather_data['temperature']['pressure'] }}mb</div>
                            </div>
                            <div class="bg-lime-100 rounded-xl p-3 transition-transform hover:scale-105">
                                <div class="flex items-center gap-2 mb-1">
                                    <i class="fas fa-wind text-green-600"></i>
                                    <div class="text-xs font-medium dark:text-black">Angin</div>
                                </div>
                                <div class="font-semibold dark:text-black">{{ $weather_data['wind']['speed'] }}km/h</div>
                            </div>
                            <div class="bg-blue-50 rounded-xl p-3 transition-transform hover:scale-105">
                                <div class="flex items-center gap-2 mb-1">
                                    <i class="fas fa-tint text-blue-500"></i>
                                    <div class="text-xs font-medium dark:text-black">Kelembapan</div>
                                </div>
                                <div class="font-semibold dark:text-black">{{ $weather_data['temperature']['humidity'] }}%</div>
                            </div>
                        </div>
                    </div>

                    <!-- Wind Status Card -->
                    <div class="weather-card bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow overflow-hidden relative mt-4">
                        <div class="flex items-center gap-2 mb-4">
                            <i class="fas fa-wind text-blue-500"></i>
                            <span class="font-semibold text-gray-800 dark:text-white">Angin Status</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-3xl font-bold text-gray-800 mb-2 dark:text-white">{{ $weather_data['wind']['speed'] }} km/h</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ $weather_data['wind']['direction'] }}°
                                </div>
                            </div>
                            <div class="flex items-center space-x-4">
                                <div class="bg-gray-100 dark:bg-gray-700 rounded-xl p-3 text-center">
                                    <div class="text-xs font-medium mb-1 text-gray-600 dark:text-gray-400">Max Gust Today</div>
                                    <div class="text-lg font-bold text-gray-800 dark:text-white">{{ $weather_data['wind']['gust'] }} km/h</div>
                                </div>
                                <div class="relative w-24 h-24">
                                    <svg class="w-full h-full" viewBox="0 0 100 100">
                                        <circle cx="50" cy="50" r="45" fill="none" stroke="#e2e8f0" stroke-width="2" />
                                        <path d="M50 5 L50 95 M5 50 L95 50 M50 50 L50 5" stroke="#4a5568" stroke-width="1" />
                                        <text x="50" y="15" text-anchor="middle" fill="#4a5568" font-size="8">N</text>
                                        <text x="85" y="52" text-anchor="middle" fill="#4a5568" font-size="8">E</text>
                                        <text x="50" y="90" text-anchor="middle" fill="#4a5568" font-size="8">S</text>
                                        <text x="15" y="52" text-anchor="middle" fill="#4a5568" font-size="8">W</text>
                                        <line x1="50" y1="50" x2="50" y2="10" stroke="#3b82f6" stroke-width="2" transform="rotate({{ $weather_data['wind']['direction'] }} 50 50)" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Solar Radiation -->
                    <div class="weather-card bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow overflow-hidden relative mt-4">
                        <div class="flex items-center gap-2 mb-6">
                            <i class="fas fa-solar-panel text-yellow-500"></i>
                            <h2 class="font-semibold text-gray-800 dark:text-white">Radiasi Matahari</h2>
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
                                <span>Rata rata dalam hari ini:</span>
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
                    <div class="weather-card rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden" style="min-height: 250px;">
                        <!-- Weather animation container -->
                        <div id="weather-background" class="absolute inset-0"></div>

                        <!-- Content overlay -->
                        <div class="relative z-10 ">
                            <div class="flex items-center gap-2 mb-4 dark:text-white">
                                <i class="fas fa-sun text-yellow-400"></i>
                                <span class="font-semibold text-gray-800 dark:text-white">UV Index</span>
                            </div>
                            <div class="text-4xl font-bold mb-3 text-gray-800 dark:text-white">{{ $weather_data['uv']['value'] }} UVI</div>
                            <div class="inline-block bg-green-500 text-white text-xs px-3 py-1 rounded-full font-medium dark:text-white">
                                {{ $weather_data['uv']['level'] }}
                            </div>
                            <p class="text-gray-700 mt-4 dark:text-white">{{ $weather_data['uv']['description'] }}</p>
                            <div class="mt-4 text-sm text-gray-700 dark:text-white">
                                <div class="flex justify-between items-center">
                                    <span>Max UV Hari Ini:</span>
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
                                <div class="text-sm text-gray-600 mt-1">Tingkat Saat Ini</div>
                            </div>
                            <div class="text-center p-4 bg-blue-50 rounded-xl">
                                <div class="text-xl font-bold text-gray-800">{{ $weather_data['rain']['today'] }} mm</div>
                                <div class="text-sm text-gray-600 mt-1">Hari Ini</div>
                            </div>
                            <div class="text-center p-4 bg-blue-50 rounded-xl">
                                <div class="text-xl font-bold text-gray-800">{{ $weather_data['rain']['weekly'] }} mm</div>
                                <div class="text-sm text-gray-600 mt-1">Minggu Ini</div>
                            </div>
                            <div class="text-center p-4 bg-blue-50 rounded-xl">
                                <div class="text-xl font-bold text-gray-800">{{ $weather_data['rain']['monthly'] }} mm</div>
                                <div class="text-sm text-gray-600 mt-1">Bulan Ini</div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Charts Section -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Latest Data Section -->
                    <div class="weather-card bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow overflow-hidden relative">
                        <div class="flex items-center gap-2 mb-6">
                            <i class="fas fa-database text-purple-500"></i>
                            <h2 class="font-semibold text-gray-800 dark:text-white">Data hari ini</h2>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            @if($latest_data)
                            <div class="text-center p-4 bg-purple-50 rounded-xl">
                                <div class="text-xl font-bold text-gray-800">{{ $latest_data->temp_out }}°C</div>
                                <div class="text-sm text-gray-600 mt-1">Suhu</div>
                            </div>
                            <div class="text-center p-4 bg-purple-50 rounded-xl">
                                <div class="text-xl font-bold text-gray-800">{{ $latest_data->hum_out }}%</div>
                                <div class="text-sm text-gray-600 mt-1">Kelembaban</div>
                            </div>
                            <div class="text-center p-4 bg-purple-50 rounded-xl">
                                <div class="text-xl font-bold text-gray-800">{{ $latest_data->windspeedkmh }} km/h</div>
                                <div class="text-sm text-gray-600 mt-1">Kecepatan Angin</div>
                            </div>
                            <div class="text-center p-4 bg-purple-50 rounded-xl">
                                <div class="text-xl font-bold text-gray-800">{{ $latest_data->rain_rate }} mm/h</div>
                                <div class="text-sm text-gray-600 mt-1">Tingkat Hujan</div>
                            </div>
                            @else
                            <div class="col-span-2 text-center text-gray-500">No data available.</div>
                            @endif
                        </div>
                    </div>

                    <!-- UV Index Section -->
                    <div class="weather-card bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow overflow-hidden relative">
                        <div class="flex items-center gap-2 mb-6">
                            <i class="fas fa-sun text-yellow-500"></i>
                            <h2 class="font-semibold text-gray-800 dark:text-white">UV Index</h2>
                        </div>
                        @if($uv_index->isNotEmpty())
                        <div class="text-center mb-6">
                            <div class="text-4xl font-bold text-gray-800 dark:text-white mb-2">
                                {{ $uv_index->first()->uv }} UVI
                            </div>
                            <div class="inline-block bg-green-500 text-white text-xs px-3 py-1 rounded-full font-medium">
                                {{ $this->getUVLevel($uv_index->first()->uv) }}
                            </div>
                        </div>
                        <div class="p-4 bg-yellow-50 rounded-xl">
                            <div class="flex items-center justify-between">
                                <div class="text-gray-600 font-medium">UV Level Status</div>
                                <div class="text-gray-800 font-bold">{{ $this->getUVLevel($uv_index->first()->uv) }}</div>
                            </div>
                        </div>
                        @else
                        <div class="text-center text-gray-500">No UV data available.</div>
                        @endif
                    </div>
                </div>

            </div>
        </div>

        <!-- Temperature Chart -->
        <div class="weather-card bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow overflow-hidden relative">
            <div class="flex items-center gap-2 mb-6">
                <i class="fas fa-temperature-high text-red-500"></i>
                <h2 class="font-semibold text-gray-800 dark:text-white">Riwayat Suhu Hari Ini</h2>
            </div>
            <div wire:ignore class="h-[400px]">
                <div id="temperatureChart" class="w-full h-full"></div>
            </div>
        </div>

        <!-- Rainfall Chart -->
        <div class="weather-card bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow overflow-hidden relative">
            <div class="flex items-center gap-2 mb-6">
                <i class="fas fa-cloud-rain text-blue-500"></i>
                <h2 class="font-semibold text-gray-800 dark:text-white">Riwayat Curah Hujan Hari Ini</h2>
            </div>
            <div wire:ignore class="h-[400px]">
                <div id="rainfallChart" class="w-full h-full"></div>
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

    <script type="module">
        // $(document).ready(function() {
        //     hideLoadingScreen();
        // });
        document.addEventListener('livewire:initialized', function() {
            const chartOptions = {
                chart: {
                    type: 'line',
                    height: 400,
                    zoomType: 'x',
                    panning: true,
                    panKey: 'shift',
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
                    tickInterval: 2 * 3600 * 1000,
                    min: new Date(new Date().setHours(0, 0, 0, 0)).getTime(),
                    max: new Date(new Date().setHours(23, 59, 59, 999)).getTime()
                },
                yAxis: {
                    title: {
                        text: 'Temperature (°C)'
                    },
                    labels: {
                        format: '{value}°C'
                    },
                    plotBands: [{
                        from: -20,
                        to: 20,
                        color: 'rgba(68, 170, 213, 0.1)',
                        label: {
                            text: 'Cold',
                            style: {
                                color: '#606060'
                            }
                        }
                    }, {
                        from: 20,
                        to: 30,
                        color: 'rgba(255, 170, 0, 0.1)',
                        label: {
                            text: 'Moderate',
                            style: {
                                color: '#606060'
                            }
                        }
                    }, {
                        from: 30,
                        to: 50,
                        color: 'rgba(255, 0, 0, 0.1)',
                        label: {
                            text: 'Hot',
                            style: {
                                color: '#606060'
                            }
                        }
                    }]
                },
                tooltip: {
                    headerFormat: '<b>{point.x:%Y-%m-%d %H:%M}</b><br/>',
                    pointFormat: '<span style="color:{point.color}">\u25CF</span> Temperature: <b>{point.y:.1f}°C</b><br/>{point.icon}',
                    useHTML: true
                },
                series: [{
                    name: 'Temperature',
                    data: @js($tempChartData).map(point => ({
                        x: point[0],
                        y: point[1],
                        marker: {
                            symbol: 'circle',
                            radius: 6,
                            fillColor: point[1] < 20 ? '#44AAD5' : point[1] < 30 ? '#FFAA00' : '#FF0000'
                        },
                        icon: point[1] < 20 ?
                            '<i class="fas fa-snowflake text-blue-500"></i> Cold' : point[1] < 30 ?
                            '<i class="fas fa-sun text-yellow-500"></i> Moderate' : '<i class="fas fa-fire text-red-500"></i> Hot'
                    })),
                    color: '#EF4444',
                    marker: {
                        enabled: true,
                        radius: 6
                    }
                }],
                plotOptions: {
                    series: {
                        cursor: 'pointer',
                        point: {
                            events: {
                                mouseOver: function() {
                                    this.series.chart.container.style.cursor = 'pointer';
                                }
                            }
                        },
                        marker: {
                            lineWidth: 1,
                            lineColor: '#ffffff',
                            enabledThreshold: 0
                        }
                    }
                }
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
            Livewire.on('chartDataUpdated', (data) => {
                // console.log('Received chart data:', data);
                const chartData = data[0];

                if (chartData && chartData.tempData && chartData.rainData) {
                    // console.log('Updating charts with:', chartData);
                    temperatureChart.series[0].setData(chartData.tempData, true);
                    rainfallChart.series[0].setData(chartData.rainData, true);
                } else {
                    console.warn('Invalid chart data received:', chartData);
                }
            });

            // Initial weather animation load
            loadWeatherAnimation('{{ $weather_data["temperature"]["condition"] }}', 'weather-background');

            // Listen for weather updates
            Livewire.on('weatherDataUpdated', () => {
                loadWeatherAnimation('{{ $weather_data["temperature"]["condition"] }}', 'weather-background');
            });
            Livewire.on('changepage', () => {
                changePage()

            });
            Livewire.on('hideLoadingScreen', () => {
                hideLoadingScreen()
            });
        });
    </script>
</div>