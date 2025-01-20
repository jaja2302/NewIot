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
                            class="block w-full rounded-lg border-2  bg-white bg-opacity-20 py-2.5 pl-3 pr-10 text-sm text-white placeholder-white focus:border-white focus:outline-none focus:ring-2 focus:ring-white appearance-none transition duration-300 ease-in-out hover:bg-opacity-30"
                            max="{{ now()->format('Y-m-d') }}">
                    </div>
                </div>
            </div>
        </div>

        <!-- Main weather content container -->
        <div class="flex flex-wrap -mx-4">
            <div class="w-full lg:w-3/4 px-4">
                <!-- Weather Cards Grid -->
                <div class="weather-card mb-6 rounded-lg shadow-lg p-6 bg-gradient-to-br from-gray-900 to-gray-800 text-white">
                    <!-- Desktop Layout -->
                    <div class="hidden md:grid md:grid-cols-3 gap-4">
                        <!-- Temperature Card -->
                        <div class="bg-gradient-to-br from-green-500/20 to-green-600/20 rounded-xl p-4 border border-green-500/20 backdrop-blur-sm">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center">
                                    <i class="fas fa-temperature-high text-green-400 text-xl mr-2"></i>
                                    <h3 class="text-lg font-semibold text-green-400">Temperature</h3>
                                </div>
                                <span class="text-xs text-green-400 bg-green-400/20 px-2 py-1 rounded-full">Real-time</span>
                            </div>
                            <div class="flex justify-between items-end">
                                <div>
                                    <div class="text-3xl font-bold text-green-400">{{ $weather_data['temperature']['current'] }}°C</div>
                                    <div class="text-sm text-green-300">Feels like: {{ number_format($heatIndex, 1) }}°C</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm text-green-300">Min: {{ $weather_data['temperature']['min'] }}°C</div>
                                    <div class="text-sm text-green-300">Max: {{ $weather_data['temperature']['max'] }}°C</div>
                                </div>
                            </div>
                        </div>

                        <!-- Wind Card -->
                        <div class="bg-gradient-to-br from-blue-500/20 to-blue-600/20 rounded-xl p-4 border border-blue-500/20">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center">
                                    <i class="fas fa-wind text-blue-400 text-xl mr-2"></i>
                                    <h3 class="text-lg font-semibold text-blue-400">Kecepatan Angin</h3>
                                </div>
                                <span class="text-xs text-blue-400 bg-blue-400/20 px-2 py-1 rounded-full">{{ $weather_data['wind']['direction'] }}°</span>
                            </div>
                            <div class="flex justify-between items-end">
                                <div>
                                    <div class="text-3xl font-bold text-blue-400">{{ $weather_data['wind']['speed'] }}</div>
                                    <div class="text-sm text-blue-300">km/h</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm text-blue-300">Gust: {{ $weather_data['wind']['gust'] }} km/h</div>
                                </div>
                            </div>
                        </div>

                        <!-- Humidity Card -->
                        <div class="bg-gradient-to-br from-purple-500/20 to-purple-600/20 rounded-xl p-4 border border-purple-500/20">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center">
                                    <i class="fas fa-tint text-purple-400 text-xl mr-2"></i>
                                    <h3 class="text-lg font-semibold text-purple-400">Kelembapan Udara</h3>
                                </div>
                                <span class="text-xs text-purple-400 bg-purple-400/20 px-2 py-1 rounded-full">Indoor</span>
                            </div>
                            <div class="flex justify-between items-end">
                                <div>
                                    <div class="text-3xl font-bold text-purple-400">{{ $weather_data['temperature']['indoor'] }}%</div>
                                    <div class="text-sm text-purple-300">Kelembapan Udara</div>
                                </div>
                                <div class="relative w-16 h-16">
                                    <div class="absolute inset-0 rounded-full border-4 border-purple-400/30"></div>
                                    <div class="absolute inset-0 rounded-full border-4 border-purple-400"
                                        style="clip-path: polygon(0 {{ 100 - $weather_data['temperature']['indoor'] }}%, 100% {{ 100 - $weather_data['temperature']['indoor'] }}%, 100% 100%, 0% 100%);"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Rainfall Card -->
                        <div class="bg-gradient-to-br from-orange-500/20 to-orange-600/20 rounded-xl p-4 border border-orange-500/20">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center">
                                    <i class="fas fa-cloud-rain text-orange-400 text-xl mr-2"></i>
                                    <h3 class="text-lg font-semibold text-orange-400">Curah Hujan</h3>
                                </div>
                                <span class="text-xs text-orange-400 bg-orange-400/20 px-2 py-1 rounded-full">Current</span>
                            </div>
                            <div class="flex justify-between items-end">
                                <div>
                                    <div class="text-3xl font-bold text-orange-400">{{ $weather_data['rain']['rate'] }}</div>
                                    <div class="text-sm text-orange-300">mm/h</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm text-orange-300">Today: {{ $weather_data['rain']['today'] }} mm</div>
                                    <div class="text-sm text-orange-300">Week: {{ $weather_data['rain']['weekly'] }} mm</div>
                                </div>
                            </div>
                        </div>

                        <!-- Pressure Card -->
                        <div class="bg-gradient-to-br from-teal-500/20 to-teal-600/20 rounded-xl p-4 border border-teal-500/20">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center">
                                    <i class="fas fa-compress-alt text-teal-400 text-xl mr-2"></i>
                                    <h3 class="text-lg font-semibold text-teal-400">Tekanan Udara</h3>
                                </div>
                                <span class="text-xs text-teal-400 bg-teal-400/20 px-2 py-1 rounded-full">Barometric</span>
                            </div>
                            <div class="flex justify-between items-end">
                                <div>
                                    <div class="text-3xl font-bold text-teal-400">{{ $weather_data['temperature']['pressure'] }}</div>
                                    <div class="text-sm text-teal-300">mb</div>
                                </div>
                                <!-- <div class="text-right">
                                    <div class="text-sm text-teal-300">Trend: {{ $weather_data['temperature']['pressure_trend'] ?? 'Stable' }}</div>
                                </div> -->
                            </div>
                        </div>
                    </div>

                    <!-- Mobile Layout - Keep your existing Swiper implementation but update the slides -->
                    <div class="block md:hidden">
                        <div class="swiper weatherCircleSwiper w-full">
                            <div class="swiper-wrapper">
                                <!-- Temperature Slide -->
                                <div class="swiper-slide">
                                    <div class="bg-gradient-to-br from-green-500/20 to-green-600/20 rounded-xl p-4 border border-green-500/20 h-full">
                                        <!-- Copy the desktop temperature card content here -->
                                    </div>
                                </div>
                                <!-- Add other slides following the same pattern -->
                            </div>
                            <div class="swiper-pagination mt-4"></div>
                            <div class="swiper-button-next"></div>
                            <div class="swiper-button-prev"></div>
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
                <div class="h-full rounded-lg shadow-lg">

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
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm weather-card mt-4">
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
            <!-- Header Section -->
            <div class="flex flex-col space-y-4 sm:space-y-0 sm:flex-row sm:items-center sm:justify-between mb-4">
                <div class="flex items-center gap-2">
                    <i class="fas fa-chart-line text-blue-500"></i>
                    <h2 class="font-semibold text-gray-800 dark:text-white">Data Riwayat {{ $selectedDate }}</h2>
                </div>

                <!-- Toggle Buttons Container -->
                <div class="flex flex-col space-y-3 sm:flex-row sm:space-y-0 sm:gap-2 w-full sm:w-auto">
                    <!-- Data Period Toggles -->
                    <div class="flex flex-wrap gap-2 sm:mr-4">
                        <button id="todayButton"
                            class="flex-1 sm:flex-none px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 ease-in-out bg-blue-500 text-white">
                            <i class="fas fa-calendar-day mr-1"></i>Hari Ini
                        </button>
                        <button id="weekButton"
                            class="flex-1 sm:flex-none px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 ease-in-out">
                            <i class="fas fa-calendar-week mr-1"></i>Minggu Ini
                        </button>
                        <button id="monthButton"
                            class="flex-1 sm:flex-none px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 ease-in-out">
                            <i class="fas fa-calendar-alt mr-1"></i>Bulan Ini
                        </button>
                    </div>

                    <!-- Data Type Toggles -->
                    <div class="grid grid-cols-2 sm:flex gap-2">
                        <button id="tempButton"
                            class="px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 ease-in-out bg-blue-500 text-white">
                            <i class="fas fa-temperature-high mr-1"></i>Suhu
                        </button>
                        <button id="rainButton"
                            class="px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 ease-in-out">
                            <i class="fas fa-cloud-rain mr-1"></i>Curah Hujan
                        </button>
                        <button id="windButton"
                            class="px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 ease-in-out">
                            <i class="fas fa-wind mr-1"></i>Angin
                        </button>
                        <button id="humidityButton"
                            class="px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 ease-in-out">
                            <i class="fas fa-tint mr-1"></i>Kelembaban
                        </button>
                        <button id="rekapButton"
                            class="px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 ease-in-out">
                            <i class="fas fa-chart-line mr-1"></i>Rekap
                        </button>
                    </div>
                </div>
            </div>

            <!-- Chart Container -->
            <div wire:ignore class="h-[300px] sm:h-[400px]">
                <div id="combinedChart" class="w-full h-full"></div>
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

    <!-- Add this hidden div to store popup template -->
    <div id="mapPopupTemplate" class="hidden">
        <div class="weather-popup p-2 space-y-1">
            <p><span class="wind-speed"></span></p>
            <p><span class="humidity"></span></p>
        </div>
    </div>

    <script type="module">
        $(document).on('livewire:initialized', function() {
            let chart;
            let currentView = 'suhu';
            let currentPeriod = 'today';
            let tempChartData = @json($tempChartData);
            let rainChartData = @json($rainChartData);
            let windChartData = @json($windChartData);
            let humidityChartData = @json($humidityChartData);
            let tempChartData_7days = @json($tempChartData_7days);
            let rainChartData_7days = @json($rainChartData_7days);
            let windChartData_7days = @json($windChartData_7days);
            let humidityChartData_7days = @json($humidityChartData_7days);
            let tempChartData_month = @json($tempChartData_month);
            let rainChartData_month = @json($rainChartData_month);
            let windChartData_month = @json($windChartData_month);
            let humidityChartData_month = @json($humidityChartData_month);

            // Add this variable to store the current chart data
            let currentChartData = null;

            // Define style configurations for each view type
            const styleConfigs = {
                suhu: {
                    colors: ['#22c55e'], // Green
                    gradient: {
                        shade: 'dark',
                        type: "vertical",
                        shadeIntensity: 0.5,
                        gradientToColors: ['#16a34a'],
                        inverseColors: true,
                        opacityFrom: 0.8,
                        opacityTo: 0.2,
                        stops: [0, 100]
                    }
                },
                rainfall: {
                    colors: ['#3b82f6'], // Blue
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
                wind: {
                    colors: ['#8b5cf6'], // Purple
                    gradient: {
                        shade: 'dark',
                        type: "vertical",
                        shadeIntensity: 0.5,
                        gradientToColors: ['#7c3aed'],
                        inverseColors: true,
                        opacityFrom: 0.8,
                        opacityTo: 0.2,
                        stops: [0, 100]
                    }
                },
                humidity: {
                    colors: ['#06b6d4'], // Cyan
                    gradient: {
                        shade: 'dark',
                        type: "vertical",
                        shadeIntensity: 0.5,
                        gradientToColors: ['#0891b2'],
                        inverseColors: true,
                        opacityFrom: 0.8,
                        opacityTo: 0.2,
                        stops: [0, 100]
                    }
                },
                rekap: {
                    colors: ['#22c55e', '#3b82f6', '#8b5cf6', '#06b6d4'],
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
            const options = {
                series: [{
                    name: 'Temperature',
                    data: []
                }],
                chart: {
                    height: 350,
                    type: 'area',
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
                colors: ['#00E396', '#FEB019', '#FF4560', '#775DD0'],
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
                        stops: [0, 100],
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth',
                    width: 3,
                    dashArray: [0, 8, 5]
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
                    colors: ["#FFA41B"],
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
                        text: 'Temperature (°C)',
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
                    enabled: true,
                    shared: true,
                    intersect: false,
                    x: {
                        format: 'dd MMM yyyy HH:mm'
                    },
                    y: {
                        formatter: function(val) {
                            return val.toFixed(1) + "°C"
                        }
                    },
                    theme: 'dark',
                    style: {
                        fontSize: '12px',
                        fontFamily: 'Helvetica, Arial, sans-serif',
                    },
                    marker: {
                        show: true,
                    },
                    fixed: {
                        enabled: false,
                        position: 'topRight',
                        offsetX: 0,
                        offsetY: 0,
                    },
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
                    },
                },
                theme: {
                    mode: 'light',
                    palette: 'palette1',
                    monochrome: {
                        enabled: false,
                        color: '#255aee',
                        shadeTo: 'light',
                        shadeIntensity: 0.65
                    },
                }
            };

            // Initialize chart with the data from Livewire properties
            const initialChartData = {
                tempChartData: @json($tempChartData ?? []),
                rainChartData: @json($rainChartData ?? []),
                windChartData: @json($windChartData ?? []),
                humidityChartData: @json($humidityChartData ?? []),
                tempChartData_7days: @json($tempChartData_7days ?? []),
                rainChartData_7days: @json($rainChartData_7days ?? []),
                windChartData_7days: @json($windChartData_7days ?? []),
                humidityChartData_7days: @json($humidityChartData_7days ?? []),
                tempChartData_month: @json($tempChartData_month ?? []),
                rainChartData_month: @json($rainChartData_month ?? []),
                windChartData_month: @json($windChartData_month ?? []),
                humidityChartData_month: @json($humidityChartData_month ?? [])
            };

            // Initialize chart
            const $chartContainer = $("#combinedChart");
            if ($chartContainer.length) {
                chart = new ApexCharts($chartContainer[0], options);
                chart.render();

                // Update chart with initial data
                currentChartData = initialChartData;
                updateChart(initialChartData);

                // Listen for subsequent updates
                Livewire.on('chartDataUpdated', (eventData) => {
                    currentChartData = eventData[0];
                    updateChart();
                });
            }

            // Button click handlers
            $('#tempButton').on('click', () => switchView('suhu'));
            $('#rainButton').on('click', () => switchView('rainfall'));
            $('#todayButton').on('click', () => switchPeriod('today'));
            $('#weekButton').on('click', () => switchPeriod('week'));
            $('#monthButton').on('click', () => switchPeriod('month'));
            $('#windButton').on('click', () => switchView('wind'));
            $('#humidityButton').on('click', () => switchView('humidity'));
            $('#rekapButton').on('click', () => switchView('rekap'));

            function switchView(view) {
                currentView = view;
                updateButtons();
                // Use stored data when switching views
                updateChart();
            }

            function switchPeriod(period) {
                currentPeriod = period;
                updateButtons();
                // Use stored data when switching periods
                updateChart();
            }

            function updateButtons() {
                // Base classes
                const baseClasses = 'px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 ease-in-out';
                const activeClasses = `${baseClasses} bg-blue-500 text-white`;
                const inactiveClasses = `${baseClasses} bg-gray-100 text-gray-700 hover:bg-gray-200`;

                // Update view buttons
                $('#tempButton').attr('class', currentView === 'suhu' ? activeClasses : inactiveClasses);
                $('#rainButton').attr('class', currentView === 'rainfall' ? activeClasses : inactiveClasses);
                $('#windButton').attr('class', currentView === 'wind' ? activeClasses : inactiveClasses);
                $('#humidityButton').attr('class', currentView === 'humidity' ? activeClasses : inactiveClasses);
                $('#rekapButton').attr('class', currentView === 'rekap' ? activeClasses : inactiveClasses);
                // Update period buttons
                $('#todayButton').attr('class', currentPeriod === 'today' ? activeClasses : inactiveClasses);
                $('#weekButton').attr('class', currentPeriod === 'week' ? activeClasses : inactiveClasses);
                $('#monthButton').attr('class', currentPeriod === 'month' ? activeClasses : inactiveClasses);
            }

            function updateChart(data) {
                if (!chart) return;

                // Store the data if provided
                if (data) {
                    currentChartData = data;
                }

                // Use stored data if no new data provided
                const chartData = data || currentChartData;
                if (!chartData) return;

                // console.log('Using chart data:', chartData);

                let series = [];
                let yAxisArray = [];
                let yAxisTitle = ''; // Initialize yAxisTitle
                let chartType = 'line'; // Default chart type

                if (currentView === 'rekap') {
                    // Handle combined view
                    let tempData, rainData, windData, humidityData;

                    switch (currentPeriod) {
                        case 'today':
                            tempData = chartData.tempChartData;
                            rainData = chartData.rainChartData;
                            windData = chartData.windChartData;
                            humidityData = chartData.humidityChartData;
                            break;
                        case 'week':
                            tempData = chartData.tempChartData_7days;
                            rainData = chartData.rainChartData_7days;
                            windData = chartData.windChartData_7days;
                            humidityData = chartData.humidityChartData_7days;
                            break;
                        case 'month':
                            tempData = chartData.tempChartData_month;
                            rainData = chartData.rainChartData_month;
                            windData = chartData.windChartData_month;
                            humidityData = chartData.humidityChartData_month;
                            break;
                    }

                    series = [{
                            name: 'Temperature',
                            data: tempData,
                            type: 'line',
                            yAxisIndex: 0
                        },
                        {
                            name: 'Rainfall',
                            data: rainData,
                            type: 'column',
                            yAxisIndex: 1
                        },
                        {
                            name: 'Wind Speed',
                            data: windData,
                            type: 'line',
                            yAxisIndex: 2
                        },
                        {
                            name: 'Humidity',
                            data: humidityData,
                            type: 'line',
                            yAxisIndex: 3
                        }
                    ];

                    yAxisArray = [{
                            seriesName: 'Temperature',
                            title: {
                                text: 'Temperature (°C)',
                                style: {
                                    color: '#22c55e'
                                }
                            },
                            labels: {
                                style: {
                                    colors: '#22c55e'
                                }
                            },
                            axisBorder: {
                                show: true,
                                color: '#22c55e'
                            }
                        },
                        {
                            seriesName: 'Rainfall',
                            opposite: true,
                            title: {
                                text: 'Rainfall (mm/h)',
                                style: {
                                    color: '#3b82f6'
                                }
                            },
                            labels: {
                                style: {
                                    colors: '#3b82f6'
                                }
                            },
                            axisBorder: {
                                show: true,
                                color: '#3b82f6'
                            }
                        },
                        {
                            seriesName: 'Wind Speed',
                            opposite: true,
                            title: {
                                text: 'Wind Speed (km/h)',
                                style: {
                                    color: '#8b5cf6'
                                }
                            },
                            labels: {
                                style: {
                                    colors: '#8b5cf6'
                                }
                            },
                            axisBorder: {
                                show: true,
                                color: '#8b5cf6'
                            }
                        },
                        {
                            seriesName: 'Humidity',
                            opposite: true,
                            title: {
                                text: 'Humidity (%)',
                                style: {
                                    color: '#06b6d4'
                                }
                            },
                            labels: {
                                style: {
                                    colors: '#06b6d4'
                                }
                            },
                            axisBorder: {
                                show: true,
                                color: '#06b6d4'
                            },
                            max: 100
                        }
                    ];

                    chart.updateOptions({
                        series: series,
                        colors: ['#22c55e', '#3b82f6', '#8b5cf6', '#06b6d4'],
                        stroke: {
                            curve: 'smooth',
                            width: [3, 0, 3, 3], // Line width for each series (0 for column)
                            dashArray: [0, 0, 0, 0]
                        },
                        fill: {
                            type: ['gradient', 'solid', 'gradient', 'gradient'],
                            gradient: {
                                shade: 'dark',
                                type: "vertical",
                                shadeIntensity: 0.5,
                                opacityFrom: 0.8,
                                opacityTo: 0.2,
                            }
                        },
                        yaxis: yAxisArray,
                        tooltip: {
                            shared: true,
                            intersect: false,
                            y: {
                                formatter: function(val, {
                                    seriesIndex
                                }) {
                                    switch (seriesIndex) {
                                        case 0:
                                            return val.toFixed(1) + "°C";
                                        case 1:
                                            return val.toFixed(1) + " mm/h";
                                        case 2:
                                            return val.toFixed(1) + " km/h";
                                        case 3:
                                            return val.toFixed(1) + "%";
                                    }
                                }
                            }
                        }
                    }, false, true);
                } else {
                    // Select data based on current view and period
                    switch (currentPeriod) {
                        case 'today':
                            switch (currentView) {
                                case 'suhu':
                                    series = [{
                                        name: currentView.charAt(0).toUpperCase() + currentView.slice(1),
                                        data: chartData.tempChartData || []
                                    }];
                                    yAxisTitle = 'Temperature (°C)';
                                    break;
                                case 'rainfall':
                                    series = [{
                                        name: currentView.charAt(0).toUpperCase() + currentView.slice(1),
                                        data: chartData.rainChartData || []
                                    }];
                                    chartType = 'bar';
                                    yAxisTitle = 'Rainfall (mm/h)';
                                    break;
                                case 'wind':
                                    series = [{
                                        name: currentView.charAt(0).toUpperCase() + currentView.slice(1),
                                        data: chartData.windChartData || []
                                    }];
                                    yAxisTitle = 'Wind Speed (km/h)';
                                    break;
                                case 'humidity':
                                    series = [{
                                        name: currentView.charAt(0).toUpperCase() + currentView.slice(1),
                                        data: chartData.humidityChartData || []
                                    }];
                                    yAxisTitle = 'Humidity (%)';
                                    break;
                            }
                            break;
                        case 'week':
                            switch (currentView) {
                                case 'suhu':
                                    series = [{
                                        name: currentView.charAt(0).toUpperCase() + currentView.slice(1),
                                        data: chartData.tempChartData_7days || []
                                    }];
                                    yAxisTitle = 'Temperature (°C)';
                                    break;
                                case 'rainfall':
                                    series = [{
                                        name: currentView.charAt(0).toUpperCase() + currentView.slice(1),
                                        data: chartData.rainChartData_7days || []
                                    }];
                                    chartType = 'bar';
                                    yAxisTitle = 'Rainfall (mm/h)';
                                    break;
                                case 'wind':
                                    series = [{
                                        name: currentView.charAt(0).toUpperCase() + currentView.slice(1),
                                        data: chartData.windChartData_7days || []
                                    }];
                                    yAxisTitle = 'Wind Speed (km/h)';
                                    break;
                                case 'humidity':
                                    series = [{
                                        name: currentView.charAt(0).toUpperCase() + currentView.slice(1),
                                        data: chartData.humidityChartData_7days || []
                                    }];
                                    yAxisTitle = 'Humidity (%)';
                                    break;
                            }
                            break;
                        case 'month':
                            switch (currentView) {
                                case 'suhu':
                                    series = [{
                                        name: currentView.charAt(0).toUpperCase() + currentView.slice(1),
                                        data: chartData.tempChartData_month || []
                                    }];
                                    yAxisTitle = 'Temperature (°C)';
                                    break;
                                case 'rainfall':
                                    series = [{
                                        name: currentView.charAt(0).toUpperCase() + currentView.slice(1),
                                        data: chartData.rainChartData_month || []
                                    }];
                                    chartType = 'bar';
                                    yAxisTitle = 'Rainfall (mm/h)';
                                    break;
                                case 'wind':
                                    series = [{
                                        name: currentView.charAt(0).toUpperCase() + currentView.slice(1),
                                        data: chartData.windChartData_month || []
                                    }];
                                    yAxisTitle = 'Wind Speed (km/h)';
                                    break;
                                case 'humidity':
                                    series = [{
                                        name: currentView.charAt(0).toUpperCase() + currentView.slice(1),
                                        data: chartData.humidityChartData_month || []
                                    }];
                                    yAxisTitle = 'Humidity (%)';
                                    break;
                            }
                            break;
                    }

                    // Get the style config for current view
                    const currentStyle = styleConfigs[currentView];

                    // Update chart options
                    const newOptions = {
                        series: series,
                        chart: {
                            type: chartType
                        },
                        colors: currentStyle.colors,
                        fill: {
                            type: 'gradient',
                            gradient: currentStyle.gradient
                        },
                        yaxis: {
                            title: {
                                text: yAxisTitle
                            },
                            max: currentView === 'humidity' ? 100 : undefined
                        },
                        // Customize markers based on the type
                        markers: {
                            size: 4,
                            colors: currentStyle.colors,
                            strokeColors: "#fff",
                            strokeWidth: 2,
                            hover: {
                                size: 7,
                            }
                        },
                        // Customize tooltip
                        tooltip: {
                            y: {
                                formatter: function(val) {
                                    switch (currentView) {
                                        case 'suhu':
                                            return val.toFixed(1) + "°C";
                                        case 'rainfall':
                                            return val.toFixed(1) + " mm/h";
                                        case 'wind':
                                            return val.toFixed(1) + " km/h";
                                        case 'humidity':
                                            return val.toFixed(1) + "%";
                                        default:
                                            return val;
                                    }
                                }
                            }
                        }
                    };

                    chart.updateOptions(newOptions, false, true);
                }
            }
            // Listen for Livewire events
            Livewire.on('chartDataUpdated', (eventData) => {
                // Update with new data from server
                updateChart(eventData[0]);
            });

            Livewire.on('showLoadingScreen', () => {
                showLoadingScreen();
            });

            Livewire.on('hideLoadingScreen', () => {
                hideLoadingScreen();
            });

            // Update select and input elements to trigger loading screen
            $('#station').on('change', function() {
                showLoadingScreen();
            });

            $('input[type="date"]').on('change', function() {
                showLoadingScreen();
            });



            //this make error
            // maps 
            if (@json($station_lat) !== 0 && @json($station_lon) !== 0) {
                const map = L.map('weatherMap').setView([@json($station_lat), @json($station_lon)], 8);

                // Add OpenStreetMap tile layer
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors'
                }).addTo(map);

                function createWeatherPopup(data) {
                    // Clone the template
                    const $popup = $('#mapPopupTemplate .weather-popup').clone();

                    // Update content safely
                    $popup.find('.wind-speed').html(`💨 Angin: ${data.wind.speed} m/s`);
                    $popup.find('.humidity').html(`💧 Kelembaban: ${data.temperature.humidity}%`);

                    // Return HTML string
                    return $popup[0].outerHTML;
                }

                // Add marker for current location with weather info
                const marker = L.marker([@json($station_lat), @json($station_lon)])
                    .addTo(map)
                    .bindPopup(createWeatherPopup(@json($weather_data)))
                    .openPopup();

                // Create weather overlay
                const weatherCircle = L.circle([@json($station_lat), @json($station_lon)], {
                    color: 'blue',
                    fillColor: '#3b82f6',
                    fillOpacity: 0.2,
                    radius: 30000
                }).addTo(map);

                // Update map when location changes
                Livewire.on('updateMapMarker', (data) => {
                    const newLatLng = [data[0].lat, data[0].lon];
                    map.setView(newLatLng, 8);
                    marker.setLatLng(newLatLng)
                        .bindPopup(createWeatherPopup(@json($weather_data)))
                        .openPopup();
                    weatherCircle.setLatLng(newLatLng);
                });

                initLottieAnimation(@json($weatheranimation));

                Livewire.on('weatherAnimationUpdated', (data) => {
                    initLottieAnimation(data[0]);
                });
            }

            // Toggle functionality
            const $toggleButton = $('#toggleWeatherInfo');
            const $weatherOverlay = $('#weatherInfoOverlay');
            let isVisible = true;

            // Load saved state
            const savedState = localStorage.getItem('weatherOverlayVisible');
            if (savedState !== null) {
                isVisible = savedState === 'true';
                updateOverlayVisibility();
            }
            //enderror


            // Toggle functionality
            $toggleButton.on('click', function() {
                isVisible = !isVisible;
                updateOverlayVisibility();
                localStorage.setItem('weatherOverlayVisible', isVisible);
            });

            function updateOverlayVisibility() {
                if (isVisible) {
                    $weatherOverlay.css({
                        'opacity': '1',
                        'transform': 'translateX(0)',
                        'pointer-events': 'auto'
                    });
                } else {
                    $weatherOverlay.css({
                        'opacity': '0',
                        'transform': 'translateX(100%)',
                        'pointer-events': 'none'
                    });
                }
            }

            $('<style>')
                .text(`
                .temp-area {
                    transition: opacity 0.75s ease-in-out;
                }
                .rain-line {
                    transition: opacity 0.75s ease-in-out;
                }
            `)
                .appendTo('head');



            const weatherCircleSwiper = new Swiper('.weatherCircleSwiper', {
                slidesPerView: 1,
                spaceBetween: 30,
                centeredSlides: true,
                loop: true,
                watchSlidesProgress: true,
                centerInsufficientSlides: true,
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                },
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
                autoplay: {
                    delay: 3000,
                    disableOnInteraction: false,
                },
                effect: 'slide',
                grabCursor: true,
                touchEventsTarget: 'wrapper',
            });
        });

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