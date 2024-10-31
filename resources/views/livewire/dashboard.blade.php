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

            <div class="weather-card mb-6 rounded-lg shadow-lg p-4 sm:p-6 bg-black text-white">
                <!-- Circle cards -->
                <div class="flex flex-wrap justify-center">
                    <div class="flex flex-col items-center justify-center w-full sm:w-1/2 mb-4 sm:mb-0">
                        <div class="flex flex-wrap items-center justify-center w-full">
                            <div class="flex flex-col items-center w-1/2 mb-4 sm:mb-0">
                                <div class="relative w-24 h-24 sm:w-32 sm:h-32 rounded-full border-8 border-yellow-400 flex items-center justify-center">
                                    <div class="text-center">
                                        <div class="text-xs text-yellow-300">Feels Like</div>
                                        <div class="text-2xl sm:text-3xl font-bold text-yellow-400">{{ $weatherData['current']['apparent_temperature'] }}°</div>
                                        <div class="text-xs text-yellow-300">Actual: {{ $weatherData['current']['temperature_2m'] }}°</div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex flex-col items-center w-1/2">
                                <div class="relative w-24 h-24 sm:w-32 sm:h-32 rounded-full border-8 border-blue-400 flex items-center justify-center">
                                    <div class="text-center">
                                        <div class="text-xs text-blue-300">Wind</div>
                                        <div class="text-2xl sm:text-3xl font-bold text-blue-400">{{ $weatherData['current']['wind_speed_10m'] }} m/s</div>
                                        <div class="text-xs text-blue-300">Dir: {{ $weatherData['current']['wind_direction_10m'] }}°</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 grid grid-cols-2 gap-2 text-center text-xs w-full">
                            <div class="flex flex-col">
                                <span>10' Wind</span>
                                <span class="text-base sm:text-lg">{{ $weatherData['current']['wind_speed_10m'] ?? 'N/A' }} km/h</span>
                            </div>
                            <div class="flex flex-col">
                                <span>Rain/Day</span>
                                <span class="text-base sm:text-lg">{{ $weatherData['daily']['precipitation_sum'][0] ?? 'N/A' }} mm</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col items-center justify-center w-full sm:w-1/2">
                        <div class="flex flex-wrap items-center justify-center w-full">
                            <div class="flex flex-col items-center w-1/2 mb-4 sm:mb-0">
                                <div class="relative w-24 h-24 sm:w-32 sm:h-32 rounded-full border-8 border-green-400 flex items-center justify-center">
                                    <div class="text-center">
                                        <div class="text-xs text-green-300">Temperature</div>
                                        <div class="text-2xl sm:text-3xl font-bold text-green-400">{{ $weatherData['current']['temperature_2m'] }}°</div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex flex-col items-center w-1/2">
                                <div class="relative w-24 h-24 sm:w-32 sm:h-32 rounded-full border-8 border-purple-400 flex items-center justify-center overflow-hidden">
                                    <div class="absolute bottom-0 left-0 right-0 bg-purple-400/40 transition-all duration-500" style="height: {{ $weatherData['current']['relative_humidity_2m'] }}%; transform-origin: bottom;">
                                        <div class="absolute top-0 left-0 right-0 h-2 bg-purple-200/30 animate-wave"></div>
                                        <div class="absolute top-1 left-1 right-1 h-1 bg-purple-100/20 animate-wave-delayed"></div>
                                    </div>
                                    <div class="text-center relative z-10">
                                        <div class="text-xs text-purple-300">Humidity</div>
                                        <div class="text-2xl sm:text-3xl font-bold text-purple-400">{{ $weatherData['current']['relative_humidity_2m'] }}%</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 grid grid-cols-2 gap-2 text-center text-xs w-full">
                            <div>Barometer<br><span class="text-base sm:text-lg">ABS {{ $weatherData['current']['pressure_msl'] ?? 'N/A' }} hPa</span></div>
                            <div class="text-right">Pressure<br><span class="text-base sm:text-lg">{{ ($weatherData['current']['pressure_msl'] ?? 0) - 1013.25 }} hPa</span></div>
                        </div>
                    </div>
                </div>

                <!-- Sunrise Info -->
                <div class="mt-6 relative">
                    <div class="sun-path">
                        @php
                        $sunPosition = $this->calculateSunPosition();
                        @endphp
                        @if ($sunPosition >= 0)
                        <div class="sun" style="left: {{ $sunPosition }}%;">
                            <span class="sun-icon">🌞</span>
                        </div>
                        @else
                        <div class="moon" style="left: 50%;"> <!-- Centered for nighttime -->
                            <span class="moon-icon">🌙</span>
                        </div>
                        @endif
                    </div>
                    <div class="time-labels dark:text-white text-xs sm:text-sm flex flex-wrap justify-between">
                        <div>🌅 {{ isset($weatherData['daily']['sunrise'][0]) ? \Carbon\Carbon::parse($weatherData['daily']['sunrise'][0])->format('H:i') : 'N/A' }}</div>
                        <div>UV Index: {{ $weatherData['daily']['uv_index_max'][0] ?? 'N/A' }}</div>
                        <div class="w-full text-center my-2">{{ isset($weatherData['current']['time']) ? \Carbon\Carbon::parse($weatherData['current']['time'])->format('D, d M Y') : 'N/A' }}</div>
                        <div class="w-full text-center mb-2">{{ isset($weatherData['current']['time']) ? \Carbon\Carbon::parse($weatherData['current']['time'])->format('H:i:s') : 'N/A' }}</div>
                        <div>🌇 {{ isset($weatherData['daily']['sunset'][0]) ? \Carbon\Carbon::parse($weatherData['daily']['sunset'][0])->format('H:i') : 'N/A' }}</div>
                    </div>
                </div>

            </div>


            <div id="weather-content" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                <!-- Location Info -->
                <div class="weather-card rounded-lg shadow-lg p-6">
                    <h2 class="text-2xl font-bold mb-4">Informasi Lokasi</h2>
                    <p><strong>Zona Waktu:</strong> {{ $weatherData['timezone'] }}</p>
                    <p><strong>Ketinggian:</strong> {{ $weatherData['elevation'] }}m</p>
                    <p><strong>Koordinat:</strong> {{ $weatherData['latitude'] }}°LU, {{ $weatherData['longitude'] }}°BT</p>
                </div>

                <!-- Today's Highlights -->
                <div class="weather-card rounded-lg shadow-lg p-6">
                    <h2 class="text-2xl font-bold mb-4">Sorotan Hari Ini</h2>
                    <p><strong>Indeks UV:</strong> {{ $weatherData['daily']['uv_index_max'][0] }}</p>
                    <p><strong>Curah Hujan:</strong> {{ $weatherData['daily']['precipitation_sum'][0] }} mm</p>
                    <p><strong>Matahari Terbit:</strong> {{ \Carbon\Carbon::parse($weatherData['daily']['sunrise'][0])->format('H:i') }}</p>
                    <p><strong>Matahari Terbenam:</strong> {{ \Carbon\Carbon::parse($weatherData['daily']['sunset'][0])->format('H:i') }}</p>
                </div>

                <!-- 7-Day Forecast -->
                <div class="weather-card rounded-lg shadow-lg p-6 col-span-full">
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
                <div class="weather-card rounded-lg shadow-lg p-6 col-span-full">
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
    </script>

    <!-- <script type="module">
        initializeScrollNavigation(
            "{{ route('dashboardaws') }}", // Up route
            "{{ route('waterlevel') }}" // Down route
        );
    </script> -->
</div>