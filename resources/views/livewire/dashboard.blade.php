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
        <div id="weather-content" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Misol-like Weather Widget -->
            <div class="weather-card rounded-lg shadow-lg p-4 transform hover:scale-105 transition-transform duration-300 relative overflow-hidden bg-black text-white">
                <div class="grid grid-cols-3 gap-4">
                    <div class="col-span-2">
                        <div class="text-xl font-bold mb-2">{{ \Carbon\Carbon::now()->format('D, d M Y') }}</div>
                        <div class="text-4xl font-bold">{{ \Carbon\Carbon::now()->format('H:i:s') }}</div>
                    </div>
                    <div class="text-right">
                        <div class="text-5xl font-bold">{{ round($weatherData['current']['temperature_2m']) }}°C</div>
                        <div class="text-lg">{{ $this->getWeatherIcon($weatherData['current']['weather_code']) }}</div>
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-4 mt-4">
                    <div class="text-center">
                        <div class="text-yellow-400 text-2xl font-bold">{{ round($weatherData['current']['wind_speed_10m']) }}</div>
                        <div class="text-sm">Wind km/h</div>
                    </div>
                    <div class="text-center">
                        <div class="text-blue-400 text-2xl font-bold">{{ $weatherData['current']['relative_humidity_2m'] }}%</div>
                        <div class="text-sm">Humidity</div>
                    </div>
                    <div class="text-center">
                        <div class="text-green-400 text-2xl font-bold">{{ $weatherData['current']['precipitation'] }}</div>
                        <div class="text-sm">Rain mm</div>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="flex justify-between items-center">
                        <span>Sunrise</span>
                        <span>{{ \Carbon\Carbon::parse($weatherData['daily']['sunrise'][0])->format('H:i') }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span>Sunset</span>
                        <span>{{ \Carbon\Carbon::parse($weatherData['daily']['sunset'][0])->format('H:i') }}</span>
                    </div>
                </div>
            </div>

            <!-- Location Info -->
            <div class="weather-card rounded-lg shadow-lg p-6 transform hover:scale-105 transition-transform duration-300">
                <h2 class="text-2xl font-bold mb-4">Informasi Lokasi</h2>
                <p><strong>Zona Waktu:</strong> {{ $weatherData['timezone'] }}</p>
                <p><strong>Ketinggian:</strong> {{ $weatherData['elevation'] }}m</p>
                <p><strong>Koordinat:</strong> {{ $weatherData['latitude'] }}°LU, {{ $weatherData['longitude'] }}°BT</p>
            </div>

            <!-- Today's Highlights -->
            <div class="weather-card rounded-lg shadow-lg p-6 transform hover:scale-105 transition-transform duration-300">
                <h2 class="text-2xl font-bold mb-4">Sorotan Hari Ini</h2>
                <p><strong>Indeks UV:</strong> {{ $weatherData['daily']['uv_index_max'][0] }}</p>
                <p><strong>Curah Hujan:</strong> {{ $weatherData['daily']['precipitation_sum'][0] }} mm</p>
                <p><strong>Matahari Terbit:</strong> {{ \Carbon\Carbon::parse($weatherData['daily']['sunrise'][0])->format('H:i') }}</p>
                <p><strong>Matahari Terbenam:</strong> {{ \Carbon\Carbon::parse($weatherData['daily']['sunset'][0])->format('H:i') }}</p>
            </div>

            <!-- 7-Day Forecast -->
            <div class="weather-card rounded-lg shadow-lg p-6 col-span-full transform hover:scale-105 transition-transform duration-300">
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
            <div class="weather-card rounded-lg shadow-lg p-6 col-span-full transform hover:scale-105 transition-transform duration-300">
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