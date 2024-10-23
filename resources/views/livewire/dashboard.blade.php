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
            <!-- Current Weather -->
            <div class="weather-card rounded-lg shadow-lg p-4 transform hover:scale-105 transition-transform duration-300 relative overflow-hidden">
                <div class="weather-animation absolute inset-0 opacity-20"></div>
                <h2 class="text-lg font-semibold mb-1">Cuaca saat ini</h2>
                <p class="text-xs opacity-75">{{ \Carbon\Carbon::parse($weatherData['current']['time'])->format('H:i') }}</p>
                <div class="flex items-center justify-between mt-2">
                    <div class="flex items-center">
                        <div class="text-4xl font-bold mr-2">{{ round($weatherData['current']['temperature_2m']) }}°C</div>
                        <div class="text-3xl">{{ $this->getWeatherIcon($weatherData['current']['weather_code']) }}</div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm">{{ $this->getWeatherDescription($weatherData['current']['weather_code']) }}</p>
                        <p class="text-xs">Terasa seperti: {{ round($weatherData['current']['apparent_temperature']) }}°</p>
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-2 mt-2 text-xs">
                    <div>
                        <p class="opacity-75">Angin</p>
                        <p class="font-semibold">{{ round($weatherData['current']['wind_speed_10m']) }} km/j</p>
                    </div>
                    <div>
                        <p class="opacity-75">Kelembaban</p>
                        <p class="font-semibold">{{ $weatherData['current']['relative_humidity_2m'] }}%</p>
                    </div>
                    <div>
                        <p class="opacity-75">Curah hujan</p>
                        <p class="font-semibold">{{ $weatherData['current']['precipitation'] }} mm</p>
                    </div>
                    <div>
                        <p class="opacity-75">Tutupan awan</p>
                        <p class="font-semibold">{{ $weatherData['current']['cloud_cover'] }}%</p>
                    </div>
                    <div>
                        <p class="opacity-75">Tekanan</p>
                        <p class="font-semibold">{{ round($weatherData['current']['pressure_msl']) }} mb</p>
                    </div>
                    <div>
                        <p class="opacity-75">Arah angin</p>
                        <p class="font-semibold">{{ $weatherData['current']['wind_direction_10m'] }}°</p>
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