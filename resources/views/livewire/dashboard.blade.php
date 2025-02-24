<div class="min-h-screen">

    <div class="container mx-auto px-4 py-6">
        <!-- City Search Input -->
        <div class="mb-4 relative">
            <input type="text"
                wire:model.defer="searchQuery"
                wire:keydown.enter="searchCity"
                placeholder="Cari kota di Indonesia"
                class="w-full px-4 py-2 border rounded-lg dark:text-black"
                wire:loading.attr="disabled"
                wire:target="searchCity">
        </div>

        <!-- Loading Indicator for City Search -->
        <div wire:loading wire:target="searchCity">
            <div class="mb-4">
                <p class="text-center">Memuat...</p>
            </div>
        </div>

        <!-- Search Results -->
        @if(!empty($searchResults))
        <div class="mb-4">
            <ul class="bg-white border rounded-lg shadow-lg">
                @foreach($searchResults as $result)
                <li class="px-4 py-2 hover:bg-gray-100 cursor-pointer" wire:click="setLocation('{{ $result['geometry']['lat'] }}', '{{ $result['geometry']['lng'] }}', '{{ $result['formatted'] }}')">
                    {{ $result['formatted'] }}
                </li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- Location Error Message -->
        @if($locationError)
        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4" role="alert">
            <p>{{ $locationError }}</p>
            <p>Menggunakan lokasi: {{ $searchQuery ?: 'Default' }}</p>
        </div>
        @endif

        <!-- Loading Indicator -->
        <div wire:loading wire:target="fetchWeatherData" class="text-center py-8">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-blue-500"></div>
            <p class="mt-2 text-gray-600">Memuat data cuaca...</p>
        </div>

        <!-- Weather Content -->
        <div wire:loading.remove wire:target="fetchWeatherData">
            @if(!empty($weatherData))

            <!-- Main weather content container -->
            <div class="flex flex-wrap -mx-4">
                <!-- Left column: Circle cards and Location Info + Today's Highlights -->
                <div class="w-full lg:w-3/4 px-4">
                    <!-- Weather Cards Grid -->
                    <div class="weather-card mb-6 rounded-lg shadow-lg p-6 bg-gradient-to-br from-white to-gray-100 dark:from-gray-900 dark:to-gray-800 text-gray-800 dark:text-white">
                        <!-- Desktop Layout -->
                        <div class="hidden md:grid md:grid-cols-3 gap-4">
                            <!-- Temperature Card -->
                            <div class="bg-gradient-to-br from-green-500/10 to-green-600/10 dark:from-green-500/20 dark:to-green-600/20 rounded-xl p-4 border border-green-500/20">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center">
                                        <i class="fas fa-temperature-high text-green-600 dark:text-green-400 text-xl mr-2"></i>
                                        <h3 class="text-lg font-semibold text-green-600 dark:text-green-400">Suhu</h3>
                                    </div>
                                    <span class="text-xs text-green-600 dark:text-green-400 bg-green-500/10 dark:bg-green-400/20 px-2 py-1 rounded-full">Real-time</span>
                                </div>
                                <div class="flex justify-between items-end">
                                    <div>
                                        <div class="text-3xl font-bold text-green-600 dark:text-green-400">{{ $weatherData['current']['temperature_2m'] }}°C</div>
                                        <div class="text-sm text-green-500 dark:text-green-300">Terasa seperti: {{ $weatherData['current']['apparent_temperature'] }}°C</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Wind Card -->
                            <div class="bg-gradient-to-br from-blue-500/10 to-blue-600/10 dark:from-blue-500/20 dark:to-blue-600/20 rounded-xl p-4 border border-blue-500/20">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center">
                                        <i class="fas fa-wind text-blue-600 dark:text-blue-400 text-xl mr-2"></i>
                                        <h3 class="text-lg font-semibold text-blue-600 dark:text-blue-400">Angin</h3>
                                    </div>
                                    <span class="text-xs text-blue-600 dark:text-blue-400 bg-blue-500/10 dark:bg-blue-400/20 px-2 py-1 rounded-full">{{ $weatherData['current']['wind_direction_10m'] }}°</span>
                                </div>
                                <div class="flex justify-between items-end">
                                    <div>
                                        <div class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ $weatherData['current']['wind_speed_10m'] }}</div>
                                        <div class="text-sm text-blue-500 dark:text-blue-300">m/s</div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm text-blue-500 dark:text-blue-300">Hembusan: {{ $weatherData['current']['wind_gusts_10m'] }} m/s</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Humidity Card -->
                            <div class="bg-gradient-to-br from-purple-500/10 to-purple-600/10 dark:from-purple-500/20 dark:to-purple-600/20 rounded-xl p-4 border border-purple-500/20">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center">
                                        <i class="fas fa-tint text-purple-600 dark:text-purple-400 text-xl mr-2"></i>
                                        <h3 class="text-lg font-semibold text-purple-600 dark:text-purple-400">Kelembaban</h3>
                                    </div>
                                    <span class="text-xs text-purple-600 dark:text-purple-400 bg-purple-500/10 dark:bg-purple-400/20 px-2 py-1 rounded-full">Relatif</span>
                                </div>
                                <div class="flex justify-between items-end">
                                    <div>
                                        <div class="text-3xl font-bold text-purple-600 dark:text-purple-400">{{ $weatherData['current']['relative_humidity_2m'] }}%</div>
                                        <div class="text-sm text-purple-500 dark:text-purple-300">Kelembaban Relatif</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Rainfall Card -->
                            <div class="bg-gradient-to-br from-orange-500/10 to-orange-600/10 dark:from-orange-500/20 dark:to-orange-600/20 rounded-xl p-4 border border-orange-500/20">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center">
                                        <i class="fas fa-cloud-rain text-orange-600 dark:text-orange-400 text-xl mr-2"></i>
                                        <h3 class="text-lg font-semibold text-orange-600 dark:text-orange-400">Curah Hujan</h3>
                                    </div>
                                    <span class="text-xs text-orange-600 dark:text-orange-400 bg-orange-500/10 dark:bg-orange-400/20 px-2 py-1 rounded-full">Hari Ini</span>
                                </div>
                                <div class="flex justify-between items-end">
                                    <div>
                                        <div class="text-3xl font-bold text-orange-600 dark:text-orange-400">{{ $weatherData['daily']['precipitation_sum'][0] ?? 'N/A' }}</div>
                                        <div class="text-sm text-orange-500 dark:text-orange-300">mm</div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm text-orange-500 dark:text-orange-300">Hujan: {{ $weatherData['current']['rain'] }} mm/h</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Pressure Card -->
                            <div class="bg-gradient-to-br from-teal-500/10 to-teal-600/10 dark:from-teal-500/20 dark:to-teal-600/20 rounded-xl p-4 border border-teal-500/20">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center">
                                        <i class="fas fa-compress-alt text-teal-600 dark:text-teal-400 text-xl mr-2"></i>
                                        <h3 class="text-lg font-semibold text-teal-600 dark:text-teal-400">Tekanan Udara</h3>
                                    </div>
                                    <span class="text-xs text-teal-600 dark:text-teal-400 bg-teal-500/10 dark:bg-teal-400/20 px-2 py-1 rounded-full">Barometrik</span>
                                </div>
                                <div class="flex justify-between items-end">
                                    <div>
                                        <div class="text-3xl font-bold text-teal-600 dark:text-teal-400">{{ $weatherData['current']['pressure_msl'] }}</div>
                                        <div class="text-sm text-teal-500 dark:text-teal-300">hPa</div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm text-teal-500 dark:text-teal-300">Permukaan: {{ $weatherData['current']['surface_pressure'] }} hPa</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Mobile Layout -->
                        <div class="block md:hidden">
                            <div class="swiper weatherCircleSwiper w-full">
                                <div class="swiper-wrapper">
                                    <!-- Temperature Slide -->
                                    <div class="swiper-slide">
                                        <div class="bg-gradient-to-br from-green-500/20 to-green-600/20 rounded-xl p-4 border border-green-500/20 h-full">
                                            <div class="flex items-center justify-between mb-4">
                                                <div class="flex items-center">
                                                    <i class="fas fa-temperature-high text-green-400 text-xl mr-2"></i>
                                                    <h3 class="text-lg font-semibold text-green-400">Suhu</h3>
                                                </div>
                                                <span class="text-xs text-green-400 bg-green-400/20 px-2 py-1 rounded-full">Real-time</span>
                                            </div>
                                            <div class="flex justify-between items-end">
                                                <div>
                                                    <div class="text-3xl font-bold text-green-400">{{ $weatherData['current']['temperature_2m'] }}°C</div>
                                                    <div class="text-sm text-green-300">Terasa seperti: {{ $weatherData['current']['apparent_temperature'] }}°C</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Add similar slides for other metrics -->
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
                            <div class="absolute bottom-4 right-4 bg-white dark:bg-gray-800 p-2 rounded-lg shadow-lg z-20">
                                <div class="text-sm font-semibold mb-2">Cuaca Saat Ini</div>
                                <div class="space-y-1">
                                    <div class="flex items-center">
                                        <div class="w-4 h-4 bg-blue-500/20 rounded mr-2"></div>
                                        <span class="text-xs">Suhu: {{ $weatherData['current']['temperature_2m'] }}°C</span>
                                    </div>
                                    <div class="flex items-center">
                                        <div class="w-4 h-4 bg-red-500/20 rounded mr-2"></div>
                                        <span class="text-xs">Hujan: {{ $weatherData['current']['rain'] }} mm</span>
                                    </div>
                                    <div class="flex items-center">
                                        <div class="w-4 h-4 bg-gray-500/20 rounded mr-2"></div>
                                        <span class="text-xs">Awan: {{ $weatherData['current']['cloud_cover'] }}%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right column: New card -->
                <div class="w-full lg:w-1/4 px-4 mt-4 lg:mt-0">
                    <div class="h-full bg-gradient-to-br from-blue-50 to-blue-100 dark:from-gray-800 dark:to-gray-900 rounded-lg shadow-lg">
                        <!-- Weather Summary Card -->
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h2 class="text-xl font-bold mb-4 text-gray-800 dark:text-white flex items-center">
                                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"></path>
                                </svg>
                                Cuaca Saat Ini
                            </h2>

                            <!-- Weather Animation -->
                            <div id="weatherAnimationContainer" wire:ignore class="nav-icon lottie-animation h-40 mb-4"></div>

                            <!-- Current Conditions -->
                            <div class="space-y-3 mb-6">
                                <div class="flex items-center justify-between p-3 bg-white/50 dark:bg-gray-700/50 rounded-lg">
                                    <span class="text-gray-600 dark:text-gray-300">Kondisi</span>
                                    <span class="font-semibold text-gray-800 dark:text-white">{{ $this->getWeatherIcon($weatherData['current']['weather_code']) }} {{ $weatherData['current']['weather_code'] }}</span>
                                </div>
                                <div class="flex items-center justify-between p-3 bg-white/50 dark:bg-gray-700/50 rounded-lg">
                                    <span class="text-gray-600 dark:text-gray-300">Tutupan Awan</span>
                                    <span class="font-semibold text-gray-800 dark:text-white">{{ $weatherData['current']['cloud_cover'] }}%</span>
                                </div>
                                <div class="flex items-center justify-between p-3 bg-white/50 dark:bg-gray-700/50 rounded-lg">
                                    <span class="text-gray-600 dark:text-gray-300">Hembusan Angin</span>
                                    <span class="font-semibold text-gray-800 dark:text-white">{{ $weatherData['current']['wind_gusts_10m'] }} km/h</span>
                                </div>
                            </div>
                        </div>

                        <!-- Weather Alerts Section -->
                        <div class="p-6">
                            <h2 class="text-xl font-bold mb-4 text-gray-800 dark:text-white flex items-center">
                                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                                Peringatan Cuaca
                            </h2>

                            @if (count($alerts ?? []) > 0)
                            @foreach ($alerts as $alert)
                            <div class="alert-item mb-3 p-4 bg-red-500/90 text-white rounded-lg transform transition-all hover:scale-102 hover:bg-red-600/90">
                                <div class="flex items-center">
                                    <span class="text-2xl mr-3">{{ $alert['icon'] }}</span>
                                    <div>
                                        <p class="font-bold">{{ $alert['title'] }}</p>
                                        <p class="text-sm opacity-90">{{ $alert['message'] }}</p>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                            @else
                            <div class="p-4 bg-green-500/20 dark:bg-green-900/20 rounded-lg">
                                <div class="flex items-center">
                                    <span class="text-2xl mr-3">✅</span>
                                    <p class="text-green-700 dark:text-green-300">Tidak ada peringatan cuaca saat ini.</p>
                                </div>
                            </div>
                            @endif

                            <!-- Quick Stats -->
                            <div class="mt-6">
                                <div class="stat-card p-3 bg-white/50 dark:bg-gray-700/50 rounded-lg text-center">
                                    <div class="text-sm text-gray-600 dark:text-gray-300">Tekanan Permukaan</div>
                                    <div class="font-bold text-gray-800 dark:text-white">{{ $weatherData['current']['surface_pressure'] }} hPa</div>
                                </div>

                            </div>
                        </div>
                        <!-- locatim info card -->
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700 bg-white/50 dark:bg-gray-700/50 rounded-lg shadow-sm hover:shadow-md transition-all duration-300">
                            <h2 class="text-2xl font-bold mb-6 text-gray-800 dark:text-white flex items-center">
                                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                Informasi Lokasi
                            </h2>
                            <div class="grid grid-cols-1 gap-4">
                                <div class="p-3 bg-white/30 dark:bg-gray-600/30 rounded-lg">
                                    <p class="text-sm text-gray-600 dark:text-gray-300">Zona Waktu</p>
                                    <p class="font-semibold text-gray-800 dark:text-white">{{ $weatherData['timezone'] }}</p>
                                </div>
                                <div class="p-3 bg-white/30 dark:bg-gray-600/30 rounded-lg">
                                    <p class="text-sm text-gray-600 dark:text-gray-300">Ketinggian</p>
                                    <p class="font-semibold text-gray-800 dark:text-white">{{ $weatherData['elevation'] }}m</p>
                                </div>
                                <div class="p-3 bg-white/30 dark:bg-gray-600/30 rounded-lg">
                                    <p class="text-sm text-gray-600 dark:text-gray-300">Koordinat</p>
                                    <p class="font-semibold text-gray-800 dark:text-white">{{ $weatherData['latitude'] }}°LU, {{ $weatherData['longitude'] }}°BT</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- UV Index Card -->
            <div class="weather-card rounded-lg shadow-lg p-6 col-span-full mt-4">
                <h2 class="text-2xl font-bold mb-4">Indeks UV</h2>
                <div wire:ignore id="uvIndexChart" class="h-64"></div>
            </div>
            <!-- 7-Day Forecast -->
            <div class="weather-card rounded-lg shadow-lg p-6 col-span-full mt-4">
                <h2 class="text-2xl font-bold mb-4">Prakiraan 7 Hari</h2>
                <div class="grid grid-cols-7 gap-4">
                    @foreach(range(0, 6) as $day)
                    <div class="text-center">
                        <p class="font-bold">{{ \Carbon\Carbon::parse($weatherData['daily']['time'][$day])->format('D') }}</p>
                        <p>{{ $this->getWeatherIcon($weatherData['daily']['weather_code'][$day]) }}</p>
                        <p class="text-sm">{{ round($weatherData['daily']['precipitation_sum'][$day]) }} mm</p>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Hourly Forecast -->
            <div class="weather-card rounded-lg shadow-lg p-6 col-span-full mt-4">
                <h2 class="text-2xl font-bold mb-4">Prakiraan Per Jam</h2>
                <div class="overflow-x-auto">
                    <div class="inline-flex space-x-4">
                        @foreach(range(0, 23) as $hour)
                        <div class="text-center">
                            <p class="font-bold">{{ \Carbon\Carbon::parse($weatherData['hourly']['time'][$hour])->format('H:i') }}</p>
                            <p>{{ round($weatherData['hourly']['temperature_2m'][$hour]) }}°C</p>
                            <p>{{ $weatherData['hourly']['relative_humidity_2m'][$hour] }}%</p>
                            <p>{{ $this->getWeatherIcon($weatherData['hourly']['weather_code'][$hour]) }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @else
            <p>Data cuaca tidak tersedia. Silakan coba lagi nanti.</p>
            @endif
        </div>
    </div>

    <script type="module">
        function calculateSunPosition() {
            const now = new Date();
            const sunrise = new Date("{{ isset($weatherData['daily']['sunrise'][0]) ? \Carbon\Carbon::parse($weatherData['daily']['sunrise'][0])->format('Y-m-d H:i:s') : 'N/A' }}");
            const sunset = new Date("{{ isset($weatherData['daily']['sunset'][0]) ? \Carbon\Carbon::parse($weatherData['daily']['sunset'][0])->format('Y-m-d H:i:s') : 'N/A' }}");

            if (now < sunrise || now > sunset) {
                return 0; // Sun is not visible
            }

            const totalDuration = sunset - sunrise;
            const elapsed = now - sunrise;
            const position = (elapsed / totalDuration) * 100; // Percentage of the sun's path
            return position;
        }

        $(document).ready(function() {
            const sunPosition = calculateSunPosition();
            $('.sun').css('left', sunPosition + '%');
        });
        document.addEventListener('livewire:initialized', function() {

            // Initialize the animation on first load
            initLottieAnimation(@json($weatheranimation));


            Livewire.on('weatherAnimationUpdated', (data) => {
                const animationName = data[0];

                initLottieAnimation(animationName);
            });


            const rainfallChartOptions = {
                chart: {
                    type: 'line',
                    backgroundColor: document.documentElement.classList.contains('dark') ? '#1f2937' : '#ffffff',
                },
                title: {
                    text: 'Indeks UV 7 Hari Kedepan',
                    style: {
                        color: document.documentElement.classList.contains('dark') ? '#ffffff' : '#000000',
                    }
                },
                xAxis: {
                    type: 'datetime',
                    labels: {
                        format: '{value:%Y-%m-%d}',
                        style: {
                            color: document.documentElement.classList.contains('dark') ? '#ffffff' : '#000000',
                        }
                    },
                    lineColor: document.documentElement.classList.contains('dark') ? '#ffffff' : '#000000',
                    tickColor: document.documentElement.classList.contains('dark') ? '#ffffff' : '#000000',
                },
                yAxis: {
                    title: {
                        text: 'Indeks UV',
                        style: {
                            color: document.documentElement.classList.contains('dark') ? '#ffffff' : '#000000',
                        }
                    },
                    min: 0,
                    labels: {
                        style: {
                            color: document.documentElement.classList.contains('dark') ? '#ffffff' : '#000000',
                        }
                    },
                    gridLineColor: document.documentElement.classList.contains('dark') ? '#4B5563' : '#E0E0E0',
                },
                tooltip: {
                    headerFormat: '<b>{point.x:%Y-%m-%d}</b><br/>',
                    pointFormat: 'UV Index: {point.y:.1f}',
                    backgroundColor: document.documentElement.classList.contains('dark') ? 'rgba(31, 41, 55, 0.8)' : 'rgba(255, 255, 255, 0.8)',
                    style: {
                        color: document.documentElement.classList.contains('dark') ? '#ffffff' : '#000000',
                    }
                },
                series: [{
                    name: 'UV Index',
                    data: @json($chartUvindex),
                    color: '#3B82F6',
                    marker: {
                        enabled: true,
                        fillColor: document.documentElement.classList.contains('dark') ? '#ffffff' : '#000000',
                    }
                }],
                legend: {
                    itemStyle: {
                        color: document.documentElement.classList.contains('dark') ? '#ffffff' : '#000000',
                    },
                    itemHoverStyle: {
                        color: document.documentElement.classList.contains('dark') ? '#F3F4F6' : '#333333',
                    }
                },
                plotOptions: {
                    line: {
                        dataLabels: {
                            enabled: true,
                            color: document.documentElement.classList.contains('dark') ? '#ffffff' : '#000000',
                        },
                        enableMouseTracking: true,
                    }
                },
                credits: {
                    style: {
                        color: document.documentElement.classList.contains('dark') ? '#ffffff' : '#000000',
                    }
                }
            };
            const uvchart = Highcharts.chart('uvIndexChart', rainfallChartOptions);
            Livewire.on('chartDataUpdated', (data) => {
                // console.log(data);

                if (data && Array.isArray(data)) {
                    uvchart.series[0].setData(data, true);
                } else {
                    console.warn('Invalid chart data received:', data);
                }
            });

            // Initialize Weather Map
            const map = L.map('weatherMap').setView([@json($lat), @json($lon)], 8);

            // Add OpenStreetMap tile layer
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            // Create a custom popup with weather information
            function createWeatherPopup(data) {
                return `
                    <div class="p-2">
                        <div class="space-y-1">
                            <p>💨 Angin: ${data.current.wind_speed_10m} m/s</p>
                            <p>💧 Kelembaban: ${data.current.relative_humidity_2m}%</p>
                        </div>
                    </div>
                `;
            }

            // Add marker for current location with weather info
            const marker = L.marker([@json($lat), @json($lon)])
                .addTo(map)
                .bindPopup(createWeatherPopup(@json($weatherData)))
                .openPopup();

            // Create weather overlay using the current weather data
            const weatherCircle = L.circle([@json($lat), @json($lon)], {
                color: 'blue',
                fillColor: '#3b82f6',
                fillOpacity: 0.2,
                radius: 30000 // 30km radius
            }).addTo(map);

            // Update map when location changes
            Livewire.on('locationUpdated', (data) => {
                // console.log(data);dad 

                const newLatLng = [data[0].lat, data[0].lon];
                map.setView(newLatLng, 8);
                marker.setLatLng(newLatLng)
                    .bindPopup(createWeatherPopup(@json($weatherData)))
                    .openPopup();
                weatherCircle.setLatLng(newLatLng);
            });

            // // Optional: Add a click handler to show weather at clicked location
            // map.on('click', function(e) {
            //     const lat = e.latlng.lat.toFixed(4);
            //     const lon = e.latlng.lng.toFixed(4);
            //     @this.updateLocation(lat, lon);
            // });

            // Initialize Swiper for weather circles
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