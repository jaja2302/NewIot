<div class="min-h-screen">
    @section('title', 'AWS Dashboard')
    <div class="container mx-auto px-4 py-6">

        <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-3xl shadow-lg p-6 mb-8">
            <div class="flex flex-col lg:flex-row items-center justify-between gap-4">
                <!-- Title -->
                <h1 class="text-3xl font-bold text-white">
                    <i class="fas fa-map-marker-alt mr-2"></i>Stasiun Cuaca
                </h1>

                <!-- Filters Container -->
                <div class="flex flex-col sm:flex-row gap-4 w-full lg:w-auto">
                    <!-- Station Dropdown -->
                    <div class="relative flex-1 sm:max-w-[250px]">
                        <select id="station"
                            wire:model="selectedstation"
                            wire:change="updateSelectedStation($event.target.value)"
                            class="block w-full rounded-lg border-2 border-white bg-white bg-opacity-20 py-2.5 pl-3 pr-10 text-sm text-white placeholder-white focus:border-white focus:outline-none focus:ring-2 focus:ring-white appearance-none transition duration-300 ease-in-out hover:bg-opacity-30">
                            <option value="" class="text-gray-700">Choose a station</option>
                            @foreach($list_station as $station)
                            <option value="{{ $station->id }}" class="text-gray-700">{{ $station->loc }}</option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute right-2 top-1/2 -translate-y-1/2">
                            <svg class="h-4 w-4 fill-current text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>

                    <!-- Date Picker -->
                    <div class="relative flex-1 sm:max-w-[200px]">
                        <input
                            type="date"
                            wire:change="updateSelectedDate($event.target.value)"
                            wire:model.live="selectedDate"
                            class="block w-full rounded-lg border-2 border-white bg-white bg-opacity-20 py-2.5 pl-3 pr-10 text-sm text-white placeholder-white focus:border-white focus:outline-none focus:ring-2 focus:ring-white appearance-none transition duration-300 ease-in-out hover:bg-opacity-30"
                            max="{{ now()->format('Y-m-d') }}">
                    </div>
                </div>
            </div>
        </div>

        <!-- Main weather content container -->
        <div class="flex flex-wrap -mx-4">
            <div class="w-full lg:w-3/4 px-4">
                <!-- Circle cards -->
                <div class="weather-card mb-6 rounded-lg shadow-lg p-2 sm:p-4 md:p-6 bg-black text-white">
                    <div class="flex flex-wrap justify-center">

                        <div class="flex flex-wrap justify-center w-full mb-4 sm:mb-6">
                            <div class="flex flex-col items-center w-1/3 px-1 sm:px-2">
                                <div class="relative w-20 h-20 sm:w-24 sm:h-24 md:w-32 md:h-32 rounded-full border-4 sm:border-8 border-yellow-400 flex items-center justify-center">
                                    <div class="text-center">
                                        <div class="text-[10px] sm:text-xs text-yellow-300">Terasa Seperti</div>
                                        <div class="text-lg sm:text-2xl md:text-3xl font-bold text-yellow-400">{{ $weather_data['temperature']['min'] }}°</div>
                                        <div class="text-[10px] sm:text-xs text-yellow-300">Aktual: {{ $weather_data['temperature']['current'] }}°C</div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex flex-col items-center w-1/3 px-1 sm:px-2">
                                <div class="relative w-20 h-20 sm:w-24 sm:h-24 md:w-32 md:h-32 rounded-full border-4 sm:border-8 border-green-400 flex items-center justify-center">
                                    <div class="text-center">
                                        <div class="text-[10px] sm:text-xs text-green-300">Suhu</div>
                                        <div class="text-lg sm:text-2xl md:text-3xl font-bold text-green-400">{{ $weather_data['temperature']['current'] }}°</div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex flex-col items-center w-1/3 px-1 sm:px-2">
                                <div class="relative w-20 h-20 sm:w-24 sm:h-24 md:w-32 md:h-32 rounded-full border-4 sm:border-8 border-blue-400 flex items-center justify-center">
                                    <div class="text-center">
                                        <div class="text-[10px] sm:text-xs text-blue-300">Kecepatan Angin</div>
                                        <div class="text-[10px] sm:text-2xl md:text- font-bold text-blue-400">{{ $weather_data['wind']['speed'] }}km/h</div>
                                        <div class="text-[10px] sm:text-xs text-blue-300">Arah: {{ $weather_data['wind']['direction'] }}°</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Bottom row with 3 circles -->
                        <div class="flex flex-wrap justify-center w-full">
                            <div class="flex flex-col items-center w-1/3 px-1 sm:px-2">
                                <div class="relative w-20 h-20 sm:w-24 sm:h-24 md:w-32 md:h-32 rounded-full border-4 sm:border-8 border-purple-400 flex items-center justify-center overflow-hidden">
                                    <div class="absolute bottom-0 left-0 right-0 bg-purple-400/40 dark:bg-purple-300/40 transition-all duration-500" style="height: {{ $weather_data['temperature']['indoor'] }}%; transform-origin: bottom;">
                                        <div class="absolute top-0 left-0 right-0 h-2 bg-purple-200/30 dark:bg-purple-100/30 animate-wave"></div>
                                        <div class="absolute top-1 left-1 right-1 h-1 bg-purple-100/20 dark:bg-purple-50/20 animate-wave-delayed"></div>
                                    </div>
                                    <div class="text-center relative z-10">
                                        <div class="text-[10px] sm:text-xs text-purple-300 dark:text-purple-200">Kelembaban<br><span>Dalam Ruangan</span></div>
                                        <div class="text-lg sm:text-2xl md:text-3xl font-bold text-purple-400 dark:text-purple-300">{{ $weather_data['temperature']['indoor'] }}%</div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex flex-col items-center w-1/3 px-1 sm:px-2">
                                <div class="relative w-20 h-20 sm:w-24 sm:h-24 md:w-32 md:h-32 rounded-full border-4 sm:border-8 border-orange-400 flex items-center justify-center">
                                    <div class="text-center">
                                        <div class="text-[10px] sm:text-xs text-orange-300 dark:text-orange-200">Hujan/Hari</div>
                                        <div class="text-lg sm:text-2xl md:text-3xl font-bold text-orange-400 dark:text-orange-300">{{ $weather_data['rain']['rate'] }}</div>
                                        <div class="text-[10px] sm:text-xs text-orange-300 dark:text-orange-200">mm/h</div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex flex-col items-center w-1/3 px-1 sm:px-2">
                                <div class="relative w-20 h-20 sm:w-24 sm:h-24 md:w-32 md:h-32 rounded-full border-4 sm:border-8 border-teal-400 flex items-center justify-center">
                                    <div class="text-center">
                                        <div class="text-[10px] sm:text-xs text-teal-300 dark:text-teal-200">Tekanan Udara</div>
                                        <div class="text-lg sm:text-2xl md:text-3xl font-bold text-teal-400 dark:text-teal-300"> {{ $weather_data['temperature']['pressure'] }} </div>
                                        <div class="text-[10px] sm:text-xs text-teal-300 dark:text-teal-200">mb</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 relative">
                        <div class="grid grid-cols-3 gap-4">
                            <!-- Comfort Level -->
                            <div class="bg-gradient-to-br from-indigo-500/10 to-purple-500/10 rounded-xl p-4">
                                <div class="text-sm font-medium mb-2">Comfort Level</div>
                                @php
                                $comfort = $this->calculateComfortLevel($weather_data['temperature']['current'], $weather_data['temperature']['humidity']);
                                @endphp
                                <div class="flex items-center justify-between">
                                    <span class="text-2xl font-bold {{ $comfort['color'] }}">
                                        {{ $comfort['icon'] }}
                                    </span>
                                    <span class="text-sm">{{ $comfort['label'] }}</span>
                                </div>
                            </div>

                            <!-- Dew Point -->
                            <div class="bg-gradient-to-br from-blue-500/10 to-cyan-500/10 rounded-xl p-4">
                                <div class="text-sm font-medium mb-2">Dew Point</div>
                                @php
                                $dewPoint = $this->calculateDewPoint($weather_data['temperature']['current'], $weather_data['temperature']['humidity']);
                                @endphp
                                <div class="flex items-center justify-between">
                                    <span class="text-2xl font-bold text-blue-500">💧</span>
                                    <span class="text-sm">{{ number_format($dewPoint, 1) }}°C</span>
                                </div>
                            </div>

                            <!-- Heat Index -->
                            <div class="bg-gradient-to-br from-orange-500/10 to-red-500/10 rounded-xl p-4">
                                <div class="text-sm font-medium mb-2">Heat Index</div>
                                @php
                                $heatIndex = $this->calculateHeatIndex($weather_data['temperature']['current'], $weather_data['temperature']['humidity']);
                                @endphp
                                <div class="flex items-center justify-between">
                                    <span class="text-2xl font-bold text-orange-500">🌡️</span>
                                    <span class="text-sm">{{ number_format($heatIndex, 1) }}°C</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- maps  -->
                <div class="weather-card rounded-lg shadow-lg p-6 col-span-full mt-4">
                    <h2 class="text-2xl font-bold mb-4 flex items-center">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                        </svg>
                        Peta Cuaca
                    </h2>
                    <div class="relative">

                        <div wire:ignore id="weatherMap" class="h-[400px] rounded-lg z-10"></div>

                        <!-- Toggle Button - Added -->
                        <button id="toggleWeatherInfo"
                            class="absolute top-4 right-4 bg-white dark:bg-gray-800 p-2 rounded-lg shadow-lg z-20 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <i class="fas fa-layer-group"></i>
                        </button>

                        <!-- Weather info overlay - Updated with dynamic data -->
                        <div id="weatherInfoOverlay" class="absolute bottom-4 right-4 bg-white dark:bg-gray-800 p-4 rounded-lg shadow-lg z-20 w-full max-w-[280px] sm:max-w-[320px] md:max-w-[360px] transition-all duration-300 transform">

                            <!-- Rainfall Data Card -->
                            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 sm:p-6 mt-4 shadow-sm weather-card">
                                <div class="flex items-center gap-2 mb-4">
                                    <i class="fas fa-cloud-rain text-blue-500 text-sm sm:text-base"></i>
                                    <span class="font-semibold text-sm sm:text-base">Rainfall Data</span>
                                </div>
                                <div class="grid grid-cols-2 gap-2 sm:gap-4">
                                    <div class="text-center p-2 sm:p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                        <div class="text-base sm:text-lg font-bold">{{ $weather_data['rain']['rate'] }} mm/h</div>
                                        <div class="text-[10px] sm:text-xs text-gray-600 dark:text-gray-400">Tingkat Saat Ini</div>
                                    </div>
                                    <div class="text-center p-2 sm:p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                        <div class="text-base sm:text-lg font-bold">{{ $weather_data['rain']['today'] }} mm</div>
                                        <div class="text-[10px] sm:text-xs text-gray-600 dark:text-gray-400">Hari Ini</div>
                                    </div>
                                    <div class="text-center p-2 sm:p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                        <div class="text-base sm:text-lg font-bold">{{ $weather_data['rain']['weekly'] }} mm</div>
                                        <div class="text-[10px] sm:text-xs text-gray-600 dark:text-gray-400">Minggu Ini</div>
                                    </div>
                                    <div class="text-center p-2 sm:p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                        <div class="text-base sm:text-lg font-bold">{{ $weather_data['rain']['monthly'] }} mm</div>
                                        <div class="text-[10px] sm:text-xs text-gray-600 dark:text-gray-400">Bulan Ini</div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <!-- Right column -->
            <div class="w-full lg:w-1/4 px-4 mt-4 lg:mt-0">
                <div class="h-full bg-gradient-to-br from-blue-50 to-blue-100 dark:from-gray-800 dark:to-gray-900 rounded-lg shadow-lg">

                    <div class="weather-card rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden" style="min-height: 250px;">
                        <h2 class="text-xl font-bold mb-4 text-gray-800 dark:text-white flex items-center">
                            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"></path>
                            </svg>
                            Cuaca Saat Ini
                        </h2>

                        <!-- Weather Animation -->
                        <div id="weatherAnimationContainer" wire:ignore class="nav-icon lottie-animation h-40 mb-4"></div>
                        <!-- <div id="weather-background" class="absolute inset-0"></div> -->

                        <!-- Current Conditions -->
                        <div class="space-y-3 mb-6">
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
                                    <span>Max UV Hari Ini {{ $selectedDate }}:</span>
                                    <span>{{ $weather_data['uv']['max_today'] }} UVI</span>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- Wind Status Card -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm weather-card">
                        <div class="flex items-center gap-2 mb-6">
                            <i class="fas fa-wind text-blue-500 text-xl"></i>
                            <span class="font-semibold text-lg">Angin Status</span>
                        </div>

                        <!-- Wind Stats Grid -->
                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <!-- Max Gust -->
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <div class="text-sm text-gray-600 dark:text-gray-400 mb-1">Max Gust</div>
                                <div class="text-2xl font-bold text-blue-500">{{ $weather_data['wind']['gust'] }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">km/h</div>
                            </div>

                            <!-- Wind Direction -->
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <div class="text-sm text-gray-600 dark:text-gray-400 mb-1">Direction</div>
                                <div class="text-2xl font-bold text-blue-500">{{ $weather_data['wind']['direction'] }}°</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">degrees</div>
                            </div>
                        </div>

                        <!-- Compass -->
                        <div class="relative w-48 h-48 mx-auto ">
                            <!-- Compass Circle -->
                            <div class="absolute inset-0 border-4 border-gray-200 dark:border-gray-600 rounded-full"></div>
                            <div class="absolute inset-2 border-2 border-gray-100 dark:border-gray-700 rounded-full"></div>

                            <!-- Direction Labels -->
                            <div class="absolute inset-0">
                                <span class="absolute top-2 left-1/2 -translate-x-1/2 font-bold text-gray-700 dark:text-gray-300">N</span>
                                <span class="absolute right-2 top-1/2 -translate-y-1/2 font-bold text-gray-700 dark:text-gray-300">E</span>
                                <span class="absolute bottom-2 left-1/2 -translate-x-1/2 font-bold text-gray-700 dark:text-gray-300">S</span>
                                <span class="absolute left-2 top-1/2 -translate-y-1/2 font-bold text-gray-700 dark:text-gray-300">W</span>
                            </div>

                            <!-- Direction Arrow -->
                            <div class="absolute inset-0 transition-transform duration-300" style="transform: rotate({{ $weather_data['wind']['direction'] }}deg)">
                                <div class="absolute top-1/2 left-1/2 w-1 h-24 -translate-x-1/2 -translate-y-1/2 origin-center">
                                    <div class="w-4 h-4 -mt-1 mx-auto bg-red-500 rounded-full"></div>
                                    <div class="w-1 h-full bg-gradient-to-b from-red-500 to-transparent"></div>
                                </div>
                            </div>

                            <!-- Center Speed Display -->
                            <div class="absolute inset-0 flex items-center justify-center">
                                <div class="text-center bg-white dark:bg-gray-800 rounded-full p-3">
                                    <div class="text-xl font-bold text-gray-800 dark:text-gray-200">{{ $weather_data['wind']['speed'] }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">km/h</div>
                                </div>
                            </div>
                        </div>

                        <!-- Status Indicator -->
                        <div class="mt-6 flex justify-center">
                            @php
                            $windSpeed = (float)$weather_data['wind']['speed'];
                            $statusClass = match(true) {
                            $windSpeed >= 30 => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                            $windSpeed >= 15 => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                            default => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                            };
                            $statusText = match(true) {
                            $windSpeed >= 30 => 'Strong Wind',
                            $windSpeed >= 15 => 'Moderate Wind',
                            default => 'Light Wind'
                            };
                            @endphp
                            <span class="px-4 py-2 rounded-full text-sm font-medium {{ $statusClass }}">
                                {{ $statusText }}
                            </span>
                        </div>
                    </div>


                </div>
            </div>
        </div>




        <!-- Temperature Chart -->
        <div class="mt-4 weather-card bg-white dark:bg-gray-800 rounded-2xl p-4 shadow-sm hover:shadow-md transition-shadow overflow-hidden relative">
            <div class="flex items-center gap-2 mb-4">
                <i class="fas fa-temperature-high text-red-500"></i>
                <h2 class="font-semibold text-gray-800 dark:text-white">Riwayat Suhu {{ $selectedDate }}</h2>
            </div>
            <div wire:ignore class="h-[250px]">
                <div id="temperatureChart" class="w-full h-full"></div>
            </div>
        </div>

        <!-- Rainfall Chart -->
        <div class="mt-4 weather-card bg-white dark:bg-gray-800 rounded-2xl p-4 shadow-sm hover:shadow-md transition-shadow overflow-hidden relative">
            <div class="flex items-center gap-2 mb-4">
                <i class="fas fa-cloud-rain text-blue-500"></i>
                <h2 class="font-semibold text-gray-800 dark:text-white">Riwayat Curah Hujan {{ $selectedDate }}</h2>
            </div>
            <div wire:ignore class="h-[250px]">
                <div id="rainfallChart" class="w-full h-full"></div>
            </div>
        </div>

        <!-- tabel data  -->
        <div class="mt-4">
            <h2 class="flex items-center gap-2 mb-4 font-semibold text-gray-800 dark:text-white">
                <i class="fas fa-history text-blue-500"></i>
                Riwayat Data Tabel
            </h2>
            {{$this->table}}
        </div>

    </div>
</div>
<script type="module">
    document.addEventListener('livewire:initialized', function() {


        //charts temperature
        const chartOptions = {
            chart: {
                type: 'line',
                height: 300,
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
                reflow: true,
                backgroundColor: 'transparent'
            },
            title: {
                text: null
            },
            xAxis: {
                type: 'datetime',
                labels: {
                    format: '{value:%H:%M}',
                    rotation: 0,
                    align: 'center',
                    style: {
                        color: document.documentElement.classList.contains('dark') ? '#FFFFFF' : '#666666'
                    }
                },
                tickInterval: 2 * 3600 * 1000,
                min: new Date(new Date().setHours(0, 0, 0, 0)).getTime(),
                max: new Date(new Date().setHours(23, 59, 59, 999)).getTime(),
                lineColor: document.documentElement.classList.contains('dark') ? '#FFFFFF' : '#666666',
                tickColor: document.documentElement.classList.contains('dark') ? '#FFFFFF' : '#666666'
            },
            yAxis: {
                title: {
                    text: 'Temperature (°C)',
                    style: {
                        color: document.documentElement.classList.contains('dark') ? '#FFFFFF' : '#666666'
                    }
                },
                labels: {
                    format: '{value}°C',
                    style: {
                        color: document.documentElement.classList.contains('dark') ? '#FFFFFF' : '#666666'
                    }
                },
                gridLineColor: document.documentElement.classList.contains('dark') ? '#4B5563' : '#E0E0E0',
                plotBands: [{
                    from: -20,
                    to: 20,
                    color: 'rgba(68, 170, 213, 0.1)',
                    label: {
                        text: 'Cold',
                        style: {
                            color: document.documentElement.classList.contains('dark') ? '#FFFFFF' : '#666666'
                        }
                    }
                }, {
                    from: 20,
                    to: 30,
                    color: 'rgba(255, 170, 0, 0.1)',
                    label: {
                        text: 'Moderate',
                        style: {
                            color: document.documentElement.classList.contains('dark') ? '#FFFFFF' : '#666666'
                        }
                    }
                }, {
                    from: 30,
                    to: 50,
                    color: 'rgba(255, 0, 0, 0.1)',
                    label: {
                        text: 'Hot',
                        style: {
                            color: document.documentElement.classList.contains('dark') ? '#FFFFFF' : '#666666'
                        }
                    }
                }]
            },
            tooltip: {
                headerFormat: '<b>{point.x:%Y-%m-%d %H:%M}</b><br/>',
                pointFormat: '<span style="color:{point.color}">\u25CF</span> Temperature: <b>{point.y:.1f}°C</b><br/>{point.icon}',
                useHTML: true,
                backgroundColor: document.documentElement.classList.contains('dark') ? 'rgba(31, 41, 55, 0.8)' : 'rgba(255, 255, 255, 0.8)',
                style: {
                    color: document.documentElement.classList.contains('dark') ? '#FFFFFF' : '#333333'
                }
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
                        lineColor: document.documentElement.classList.contains('dark') ? '#FFFFFF' : '#666666',
                        enabledThreshold: 0
                    }
                }
            },
            legend: {
                itemStyle: {
                    color: document.documentElement.classList.contains('dark') ? '#FFFFFF' : '#666666'
                },
                itemHoverStyle: {
                    color: document.documentElement.classList.contains('dark') ? '#F3F4F6' : '#333333'
                }
            }
        };

        const temperatureChart = Highcharts.chart('temperatureChart', chartOptions);

        //chart rainfall
        const rainfallChartOptions = {
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
                    format: '{value:%H:%M}',
                    style: {
                        color: document.documentElement.classList.contains('dark') ? '#FFFFFF' : '#666666'
                    }
                },
                lineColor: document.documentElement.classList.contains('dark') ? '#FFFFFF' : '#666666',
                tickColor: document.documentElement.classList.contains('dark') ? '#FFFFFF' : '#666666'
            },
            yAxis: {
                title: {
                    text: 'Rainfall (mm/h)',
                    style: {
                        color: document.documentElement.classList.contains('dark') ? '#FFFFFF' : '#666666'
                    }
                },
                min: 0,
                labels: {
                    style: {
                        color: document.documentElement.classList.contains('dark') ? '#FFFFFF' : '#666666'
                    }
                },
                gridLineColor: document.documentElement.classList.contains('dark') ? '#4B5563' : '#E0E0E0'
            },
            tooltip: {
                headerFormat: '<b>{point.x:%Y-%m-%d %H:%M}</b><br/>',
                pointFormat: '{point.y:.1f} mm/h',
                backgroundColor: document.documentElement.classList.contains('dark') ? 'rgba(31, 41, 55, 0.8)' : 'rgba(255, 255, 255, 0.8)',
                style: {
                    color: document.documentElement.classList.contains('dark') ? '#FFFFFF' : '#333333'
                }
            },
            series: [{
                name: 'Rainfall',
                data: @js($rainChartData),
                color: '#3B82F6',
            }],
            legend: {
                itemStyle: {
                    color: document.documentElement.classList.contains('dark') ? '#FFFFFF' : '#666666'
                },
                itemHoverStyle: {
                    color: document.documentElement.classList.contains('dark') ? '#F3F4F6' : '#333333'
                }
            },
            plotOptions: {
                column: {
                    borderColor: 'transparent'
                }
            },
            credits: {
                style: {
                    color: document.documentElement.classList.contains('dark') ? '#FFFFFF' : '#666666'
                }
            }
        };

        const rainfallChart = Highcharts.chart('rainfallChart', rainfallChartOptions);

        // Update charts when Livewire updates
        Livewire.on('chartDataUpdated', (data) => {
            const chartData = data[0];

            if (chartData && chartData.tempData && chartData.rainData) {
                temperatureChart.series[0].setData(chartData.tempData, true);
                rainfallChart.series[0].setData(chartData.rainData, true);
            } else {
                console.warn('Invalid chart data received:', chartData);
            }
        });

        // // Initial weather animation load
        // loadWeatherAnimation('{{ $weather_data["temperature"]["condition"] }}', 'weather-background');

        // // Listen for weather updates
        // Livewire.on('weatherDataUpdated', () => {
        //     loadWeatherAnimation('{{ $weather_data["temperature"]["condition"] }}', 'weather-background');
        // });
        Livewire.on('changepage', () => {
            changePage()

        });
        Livewire.on('showLoadingScreen', () => {
            showLoadingScreen();
        });
        Livewire.on('hideLoadingScreen', () => {
            hideLoadingScreen();
        });

        // Update your select and input elements to trigger loading screen immediately
        document.getElementById('station').addEventListener('change', function() {
            showLoadingScreen();
        });

        const dateInput = document.querySelector('input[type="date"]');
        if (dateInput) {
            dateInput.addEventListener('change', function() {
                showLoadingScreen();
            });
        }
        if (@json($station_lat) !== 0 && @json($station_lon) !== 0) {

            const map = L.map('weatherMap').setView([@json($station_lat), @json($station_lon)], 8);

            // Add OpenStreetMap tile layer
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            // Create a custom popup with weather information
            function createWeatherPopup(data) {
                return `
                <div class="p-2">
                    <div class="space-y-1">
                        <p>💨 Angin: ${data.wind} m/s</p>
                        <p>💧 Kelembaban: ${data.temperature.humidity}%</p>
                    </div>
                </div>
            `;
            }

            // Add marker for current location with weather info
            const marker = L.marker([@json($station_lat), @json($station_lon)])
                .addTo(map)
                .bindPopup(createWeatherPopup(@json($weather_data)))
                .openPopup();

            // Create weather overlay using the current weather data
            const weatherCircle = L.circle([@json($station_lat), @json($station_lon)], {
                color: 'blue',
                fillColor: '#3b82f6',
                fillOpacity: 0.2,
                radius: 30000 // 30km radius
            }).addTo(map);

            // Update map when location changes
            Livewire.on('updateMapMarker', (data) => {
                // console.log(data);dad 

                const newLatLng = [data[0].lat, data[0].lon];
                map.setView(newLatLng, 8);
                marker.setLatLng(newLatLng)
                    .bindPopup(createWeatherPopup(@json($weather_data)))
                    .openPopup();
                weatherCircle.setLatLng(newLatLng);
            });

            initLottieAnimation(@json($weatheranimation));


            Livewire.on('weatherAnimationUpdated', (data) => {
                const animationName = data[0];

                initLottieAnimation(animationName);
            });

        }

        // Add this new section for toggle functionality
        const toggleButton = document.getElementById('toggleWeatherInfo');
        const weatherOverlay = document.getElementById('weatherInfoOverlay');
        let isVisible = true;

        // Load saved state from localStorage
        const savedState = localStorage.getItem('weatherOverlayVisible');
        if (savedState !== null) {
            isVisible = savedState === 'true';
            updateOverlayVisibility();
        }

        toggleButton.addEventListener('click', function() {
            isVisible = !isVisible;
            updateOverlayVisibility();
            // Save state to localStorage
            localStorage.setItem('weatherOverlayVisible', isVisible);
        });

        function updateOverlayVisibility() {
            if (isVisible) {
                weatherOverlay.style.opacity = '1';
                weatherOverlay.style.transform = 'translateX(0)';
                weatherOverlay.style.pointerEvents = 'auto';
            } else {
                weatherOverlay.style.opacity = '0';
                weatherOverlay.style.transform = 'translateX(100%)';
                weatherOverlay.style.pointerEvents = 'none';
            }
        }
    });

    // Move these functions outside the event listener so they're accessible
    function showLoadingScreen() {
        const loadingScreen = document.getElementById('loading-screen');
        loadingScreen.style.display = 'flex';
        loadingScreen.classList.add('visible');
        document.body.style.overflow = 'hidden';
    }

    function hideLoadingScreen() {
        const loadingScreen = document.getElementById('loading-screen');
        loadingScreen.classList.remove('visible');
        setTimeout(() => {
            loadingScreen.style.display = 'none';
            document.body.style.overflow = '';
        }, 300);
    }

    function initLottieAnimation(animationName) {
        // console.log(animationName);

        const container = document.getElementById('weatherAnimationContainer');

        // Destroy the previous animation if it exists
        if (container.lottie) {
            container.lottie.destroy();
        }

        // Load the new animation
        container.lottie = lottie.loadAnimation({
            container: container,
            renderer: 'svg',
            loop: true,
            autoplay: true,
            path: `/weather/${animationName}.json`
        });
    }
</script>
</div>