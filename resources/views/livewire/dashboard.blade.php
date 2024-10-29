<div>
    <!-- City Search Input -->
    <div class="mb-4 relative">
        <input type="text"
            wire:model.defer="searchQuery"
            wire:keydown.enter="searchCity"
            placeholder="Cari kota di Indonesia"
            class="w-full px-4 py-2 border rounded-lg"
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
        <!-- Misol-like Weather Widget -->
        <div class="weather-card mb-6 rounded-lg shadow-lg p-4 sm:p-6 relative overflow-hidden bg-black text-white">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <!-- Temperature circle -->
                <div class="relative w-full h-24 sm:w-24 sm:h-24">
                    <div class="absolute inset-0 rounded-full border-yellow-400 circle-border spin-slow"></div>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <div class="text-xs">11.2°</div>
                        <div class="text-2xl sm:text-3xl font-bold text-yellow-400">10.5°</div>
                        <div class="text-xs">9.8°</div>
                    </div>
                </div>

                <!-- Wind circle -->
                <div class="relative w-full h-24 sm:w-24 sm:h-24">
                    <div class="absolute inset-0 rounded-full border-blue-400 circle-border spin-slow"></div>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <div class="text-xs">NNW 336°</div>
                        <div class="text-2xl sm:text-3xl font-bold text-blue-400">5.2</div>
                        <div class="text-xs">Gust 7.8</div>
                    </div>
                </div>

                <!-- Temperature circle -->
                <div class="relative w-full h-24 sm:w-24 sm:h-24">
                    <div class="absolute inset-0 rounded-full border-green-400 circle-border spin-slow"></div>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <div class="text-xs">Temperature</div>
                        <div class="text-2xl sm:text-3xl font-bold text-green-400">10.5°</div>
                    </div>
                </div>

                <!-- Indoor Humidity circle -->
                <div class="relative w-full h-24 sm:w-24 sm:h-24">
                    <div class="absolute inset-0 rounded-full border-purple-400 circle-border spin-slow"></div>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <div class="text-xs">Humidity</div>
                        <div class="text-2xl sm:text-3xl font-bold text-purple-400">65%</div>
                    </div>
                </div>
            </div>

            <!-- Additional data -->
            <div class="mt-4 grid grid-cols-3 sm:grid-cols-6 gap-2 text-xs">
                <div>FeelsLike<br>9.8°</div>
                <div>DewPoint<br>4.5°</div>
                <div>Humidity<br>65%</div>
                <div>10'Wind<br>NNW 5.2</div>
                <div>Rain/Day<br>0.2</div>
                <div>0.0<br>Hourly</div>
            </div>

            <!-- Rain and barometer -->
            <div class="mt-4 flex flex-wrap justify-between items-center">
                <div class="text-blue-400 text-2xl">💧</div>
                <div class="text-sm">0.2<br>mm</div>
                <div class="text-sm text-center">Barometer Reading<br>ABS 1015.2 hPa</div>
                <div class="text-sm text-right">0.3<br>hpa</div>
            </div>

            <!-- Sunrise and Sunset -->
            <div class="mt-4 flex flex-wrap justify-between items-center text-sm">
                <div>🌅 06:45</div>
                <div class="text-center">--------- UV Index 3 ---------</div>
                <div>🌇 18:30</div>
            </div>

            <!-- Date and Time -->
            <div class="mt-4 text-right text-sm">
                <div>Wed, 26 Apr 2023</div>
                <div>14:30:00</div>
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