<div class="bg-gray-100 p-6">
    <!-- Location Request Button -->
    <button id="requestLocation" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mb-4">
        Get My Location
    </button>

    <!-- Location Error Message -->
    @if($locationError)
    <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4" role="alert">
        <p>{{ $locationError }}</p>
        <p>Using default location: Latitude {{ $lat }}, Longitude {{ $lon }}</p>
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
            <div class="bg-white rounded-lg shadow-lg p-6">
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
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-2xl font-bold mb-4 text-blue-600">Location Info</h2>
                <p><strong>Timezone:</strong> {{ $weatherData['timezone'] }}</p>
                <p><strong>Elevation:</strong> {{ $weatherData['elevation'] }}m</p>
                <p><strong>Coordinates:</strong> {{ $weatherData['latitude'] }}°N, {{ $weatherData['longitude'] }}°E</p>
            </div>

            <!-- Today's Highlights -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-2xl font-bold mb-4 text-blue-600">Today's Highlights</h2>
                <p><strong>UV Index:</strong> {{ $weatherData['daily']['uv_index_max'][0] }}</p>
                <p><strong>Precipitation:</strong> {{ $weatherData['daily']['precipitation_sum'][0] }} mm</p>
                <p><strong>Sunrise:</strong> {{ \Carbon\Carbon::parse($weatherData['daily']['sunrise'][0])->format('H:i') }}</p>
                <p><strong>Sunset:</strong> {{ \Carbon\Carbon::parse($weatherData['daily']['sunset'][0])->format('H:i') }}</p>
            </div>

            <!-- 7-Day Forecast -->
            <div class="bg-white rounded-lg shadow-lg p-6 col-span-full">
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
            <div class="bg-white rounded-lg shadow-lg p-6 col-span-full">
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

    <script>
        function getLocation() {
            if ("geolocation" in navigator) {
                console.log("Geolocation is available");
                navigator.permissions.query({
                    name: 'geolocation'
                }).then(function(result) {
                    if (result.state == 'granted') {
                        console.log("Permission is granted");
                        navigator.geolocation.getCurrentPosition(showPosition, showError);
                    } else if (result.state == 'prompt') {
                        console.log("Permission has not been requested yet");
                        navigator.geolocation.getCurrentPosition(showPosition, showError);
                    } else if (result.state == 'denied') {
                        console.log("Permission was denied");
                        @this.setLocationError("Location access was denied. Using default location.");
                    }
                });
            } else {
                console.log("Geolocation is not supported by this browser");
                @this.setLocationError("Geolocation is not supported by this browser. Using default location.");
            }
        }

        function showPosition(position) {
            let lat = position.coords.latitude;
            let lon = position.coords.longitude;
            @this.updateLocation(lat, lon);
        }

        function showError(error) {
            let errorMessage;
            switch (error.code) {
                case error.PERMISSION_DENIED:
                    errorMessage = "Location access was denied. Using default location.";
                    break;
                case error.POSITION_UNAVAILABLE:
                    errorMessage = "Location information is unavailable. Using default location.";
                    break;
                case error.TIMEOUT:
                    errorMessage = "The request to get user location timed out. Using default location.";
                    break;
                case error.UNKNOWN_ERROR:
                    errorMessage = "An unknown error occurred. Using default location.";
                    break;
            }
            @this.setLocationError(errorMessage);
        }

        document.getElementById('requestLocation').addEventListener('click', getLocation);
    </script>
</div>