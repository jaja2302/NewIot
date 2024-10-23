<div>
    <!-- City Search Input -->
    <div class="mb-4 relative">
        <input type="text"
            wire:model.defer="searchQuery"
            wire:keydown.enter="searchCity"
            placeholder="Search for an Indonesian city"
            class="w-full px-4 py-2 border rounded-lg"
            wire:loading.attr="disabled"
            wire:target="searchCity">
    </div>

    <!-- Loading Indicator for City Search -->
    <div wire:loading wire:target="searchCity">
        <div class="mb-4">
            <p class="text-center">Loading...</p>
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
        <p>Using location: {{ $searchQuery ?: 'Default' }}</p>
    </div>
    @endif

    <!-- Loading Indicator -->
    <div wire:loading wire:target="fetchWeatherData" class="text-center py-8">
        <div class="inline-block animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-blue-500"></div>
        <p class="mt-2 text-gray-600">Loading weather data...</p>
    </div>

    <!-- Weather Content -->
    <div wire:loading.remove wire:target="fetchWeatherData">
        @if(!empty($weatherData))
        <div id="weather-content" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Current Weather -->
            <div class="weather-card bg-white rounded-lg shadow-lg p-6 transform hover:scale-105 transition-transform duration-300 relative overflow-hidden">
                <div class="weather-animation absolute inset-0 opacity-20"></div>
                <h2 class="text-2xl font-bold mb-4 text-blue-600">Current Weather</h2>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-5xl font-bold">{{ round($weatherData['current']['temperature_2m']) }}°C</p>
                        <p class="text-xl">{{ $weatherData['current']['is_day'] ? 'Day' : 'Night' }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-gray-600">Feels like: {{ round($weatherData['current']['apparent_temperature']) }}°C</p>
                        <p class="text-gray-600">Humidity: {{ $weatherData['hourly']['relative_humidity_2m'][0] }}%</p>
                    </div>
                </div>
                <p class="mt-4 text-sm text-gray-500">Last updated: {{ \Carbon\Carbon::parse($weatherData['current']['time'])->format('M d, Y H:i') }}</p>
            </div>

            <!-- Location Info -->
            <div class="weather-card bg-white rounded-lg shadow-lg p-6 transform hover:scale-105 transition-transform duration-300">
                <h2 class="text-2xl font-bold mb-4 text-blue-600">Location Info</h2>
                <p><strong>Timezone:</strong> {{ $weatherData['timezone'] }}</p>
                <p><strong>Elevation:</strong> {{ $weatherData['elevation'] }}m</p>
                <p><strong>Coordinates:</strong> {{ $weatherData['latitude'] }}°N, {{ $weatherData['longitude'] }}°E</p>
            </div>

            <!-- Today's Highlights -->
            <div class="weather-card bg-white rounded-lg shadow-lg p-6 transform hover:scale-105 transition-transform duration-300">
                <h2 class="text-2xl font-bold mb-4 text-blue-600">Today's Highlights</h2>
                <p><strong>UV Index:</strong> {{ $weatherData['daily']['uv_index_max'][0] }}</p>
                <p><strong>Precipitation:</strong> {{ $weatherData['daily']['precipitation_sum'][0] }} mm</p>
                <p><strong>Sunrise:</strong> {{ \Carbon\Carbon::parse($weatherData['daily']['sunrise'][0])->format('H:i') }}</p>
                <p><strong>Sunset:</strong> {{ \Carbon\Carbon::parse($weatherData['daily']['sunset'][0])->format('H:i') }}</p>
            </div>

            <!-- 7-Day Forecast -->
            <div class="weather-card bg-white rounded-lg shadow-lg p-6 col-span-full transform hover:scale-105 transition-transform duration-300">
                <h2 class="text-2xl font-bold mb-4 text-blue-600">7-Day Forecast</h2>
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
            <div class="weather-card bg-white rounded-lg shadow-lg p-6 col-span-full transform hover:scale-105 transition-transform duration-300">
                <h2 class="text-2xl font-bold mb-4 text-blue-600">Hourly Forecast</h2>
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
        <p>No weather data available. Please try again later.</p>
        @endif
    </div>
</div>