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
                        @php
                        $heatIndex = $this->calculateHeatIndex($weather_data['temperature']['current'], $weather_data['temperature']['humidity']);
                        @endphp
                        <div class="flex flex-wrap justify-center w-full mb-4 sm:mb-6">
                            <div class="flex flex-col items-center w-1/3 px-1 sm:px-2">
                                <div class="relative w-20 h-20 sm:w-24 sm:h-24 md:w-32 md:h-32 rounded-full border-4 sm:border-8 border-yellow-400 flex items-center justify-center">
                                    <div class="text-center">
                                        <div class="text-[10px] sm:text-xs text-yellow-300">Terasa Seperti</div>
                                        <div class="text-lg sm:text-2xl md:text-3xl font-bold text-yellow-400">{{ number_format($heatIndex, 1) }}°</div>
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
                                        <div class="text-[10px] sm:text-xs text-purple-300 dark:text-purple-200">Kelembapan<br><span>Dalam Ruangan</span></div>
                                        <div class="text-lg sm:text-2xl md:text-3xl font-bold text-purple-400 dark:text-purple-300">{{ $weather_data['temperature']['indoor'] }}%</div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex flex-col items-center w-1/3 px-1 sm:px-2">
                                <div class="relative w-20 h-20 sm:w-24 sm:h-24 md:w-32 md:h-32 rounded-full border-4 sm:border-8 border-orange-400 flex items-center justify-center">
                                    <div class="text-center">
                                        <div class="text-[10px] sm:text-xs text-orange-300 dark:text-orange-200">Hujan saat ini</div>
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
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <i class="fas fa-chart-line text-blue-500"></i>
                    <h2 class="font-semibold text-gray-800 dark:text-white">Data Riwayat {{ $selectedDate }}</h2>
                </div>

                <!-- Toggle Buttons -->
                <div class="flex gap-2">
                    <button id="tempButton"
                        class="px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 ease-in-out active-chart-btn"
                        onclick="toggleChartData('temperature')">
                        <i class="fas fa-temperature-high mr-1"></i>Suhu
                    </button>
                    <button id="rainButton"
                        class="px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 ease-in-out"
                        onclick="toggleChartData('rainfall')">
                        <i class="fas fa-cloud-rain mr-1"></i>Curah Hujan
                    </button>
                </div>
            </div>
            <div wire:ignore class="h-[400px]">
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
</div>
<script type="module">
    document.addEventListener('livewire:initialized', function() {
        // Initialize D3 chart
        const margin = {
            top: 20,
            right: 30,
            bottom: 30,
            left: 60
        };
        const width = document.getElementById('combinedChart').offsetWidth - margin.left - margin.right;
        const height = 400 - margin.top - margin.bottom;

        // Create SVG container
        const svg = d3.select('#combinedChart')
            .append('svg')
            .attr('width', width + margin.left + margin.right)
            .attr('height', height + margin.top + margin.bottom)
            .append('g')
            .attr('transform', `translate(${margin.left},${margin.top})`);

        // Parse the data with error handling
        const tempData = (@js($tempChartData) || []).map(d => ({
            date: new Date(d[0]),
            value: d[1]
        }));

        const rainData = (@js($rainChartData) || []).map(d => ({
            date: new Date(d[0]),
            value: d[1]
        }));

        // Scales
        const x = d3.scaleTime()
            .domain(d3.extent(tempData, d => d.date))
            .range([0, width]);

        const yTemp = d3.scaleLinear()
            .domain([0, d3.max(tempData, d => d.value) * 1.2])
            .range([height, 0]);

        const yRain = d3.scaleLinear()
            .domain([0, d3.max(rainData, d => d.value) * 1.2])
            .range([height, 0]);

        // Add X axis
        svg.append('g')
            .attr('class', 'x-axis')
            .attr('transform', `translate(0,${height})`)
            .call(d3.axisBottom(x).tickFormat(d3.timeFormat('%H:%M')));

        // Add Y axis
        const yAxisTemp = svg.append('g')
            .attr('class', 'y-axis-temp')
            .call(d3.axisLeft(yTemp));

        const yAxisRain = svg.append('g')
            .attr('class', 'y-axis-rain')
            .style('opacity', 0)
            .call(d3.axisLeft(yRain));

        // Add gradient definition
        const gradient = svg.append("defs")
            .append("linearGradient")
            .attr("id", "temperature-gradient")
            .attr("gradientUnits", "userSpaceOnUse")
            .attr("x1", 0)
            .attr("y1", 0)
            .attr("x2", 0)
            .attr("y2", height);

        gradient.append("stop")
            .attr("offset", "0%")
            .attr("stop-color", "rgba(0, 255, 255, 0.5)");

        gradient.append("stop")
            .attr("offset", "100%")
            .attr("stop-color", "rgba(0, 100, 255, 0.1)");

        // Create line generators
        const tempArea = d3.area()
            .x(d => x(d.date))
            .y0(height)
            .y1(d => yTemp(d.value))
            .curve(d3.curveMonotoneX);

        const rainLine = d3.line()
            .x(d => x(d.date))
            .y(d => yRain(d.value))
            .curve(d3.curveMonotoneX);

        // Add the paths
        const tempPath = svg.append('path')
            .datum(tempData)
            .attr('class', 'temp-area')
            .attr('fill', 'url(#temperature-gradient)')
            .attr('stroke', 'rgba(0, 150, 255, 0.8)')
            .attr('stroke-width', 2)
            .attr('d', tempArea);

        const rainPath = svg.append('path')
            .datum(rainData)
            .attr('class', 'rain-line')
            .attr('fill', 'none')
            .attr('stroke', '#3B82F6')
            .attr('stroke-width', 2)
            .attr('opacity', 0)
            .attr('d', rainLine);

        // Create tooltip
        const tooltip = d3.select('#combinedChart')
            .append('div')
            .attr('class', 'tooltip')
            .style('opacity', 0)
            .style('position', 'absolute')
            .style('background-color', 'rgba(255, 255, 255, 0.9)')
            .style('padding', '8px')
            .style('border-radius', '4px')
            .style('box-shadow', '0 2px 4px rgba(0,0,0,0.1)')
            .style('pointer-events', 'none')
            .style('font-size', '12px');

        // Add dots for temperature
        const tempDots = svg.selectAll('.temp-dot')
            .data(tempData)
            .enter()
            .append('circle')
            .attr('class', 'temp-dot')
            .attr('cx', d => x(d.date))
            .attr('cy', d => yTemp(d.value))
            .attr('r', 4)
            .attr('fill', '#EF4444')
            .on('mouseover', function(event, d) {
                const dot = d3.select(this);
                dot.attr('r', 6);

                tooltip.transition()
                    .duration(200)
                    .style('opacity', 1);

                tooltip.html(`
                    <div class="font-semibold">Temperature</div>
                    <div>Time: ${d3.timeFormat('%H:%M')(d.date)}</div>
                    <div>Value: ${d.value}°C</div>
                `)
                    .style('left', (event.pageX + 10) + 'px')
                    .style('top', (event.pageY - 10) + 'px');
            })
            .on('mouseout', function() {
                const dot = d3.select(this);
                dot.attr('r', 4);

                tooltip.transition()
                    .duration(500)
                    .style('opacity', 0);
            });

        // Add dots for rainfall
        const rainDots = svg.selectAll('.rain-dot')
            .data(rainData)
            .enter()
            .append('circle')
            .attr('class', 'rain-dot')
            .attr('cx', d => x(d.date))
            .attr('cy', d => yRain(d.value))
            .attr('r', 4)
            .attr('fill', '#3B82F6')
            .style('opacity', 0)
            .on('mouseover', function(event, d) {
                const dot = d3.select(this);
                dot.attr('r', 6);

                tooltip.transition()
                    .duration(200)
                    .style('opacity', 1);

                tooltip.html(`
                    <div class="font-semibold">Rainfall</div>
                    <div>Time: ${d3.timeFormat('%H:%M')(d.date)}</div>
                    <div>Value: ${d.value} mm</div>
                `)
                    .style('left', (event.pageX + 10) + 'px')
                    .style('top', (event.pageY - 10) + 'px');
            })
            .on('mouseout', function() {
                const dot = d3.select(this);
                dot.attr('r', 4);

                tooltip.transition()
                    .duration(500)
                    .style('opacity', 0);
            });

        // Toggle function
        window.toggleChartData = function(type) {
            const duration = 750;

            if (type === 'temperature') {
                d3.select('#tempButton').attr('class', 'px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 ease-in-out active-chart-btn');
                d3.select('#rainButton').attr('class', 'px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 ease-in-out inactive-chart-btn');

                tempPath.transition().duration(duration).style('opacity', 1);
                rainPath.transition().duration(duration).style('opacity', 0);
                tempDots.transition().duration(duration).style('opacity', 1);
                rainDots.transition().duration(duration).style('opacity', 0);
                yAxisTemp.transition().duration(duration).style('opacity', 1);
                yAxisRain.transition().duration(duration).style('opacity', 0);
            } else {
                d3.select('#rainButton').attr('class', 'px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 ease-in-out active-chart-btn');
                d3.select('#tempButton').attr('class', 'px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 ease-in-out inactive-chart-btn');

                tempPath.transition().duration(duration).style('opacity', 0);
                rainPath.transition().duration(duration).style('opacity', 1);
                tempDots.transition().duration(duration).style('opacity', 0);
                rainDots.transition().duration(duration).style('opacity', 1);
                yAxisTemp.transition().duration(duration).style('opacity', 0);
                yAxisRain.transition().duration(duration).style('opacity', 1);
            }
        }

        // Initialize with temperature view
        toggleChartData('temperature');

        // Update chart when Livewire updates
        Livewire.on('chartDataUpdated', (data) => {
            const chartData = data[0];

            if (chartData && chartData.tempData && chartData.rainData) {
                // Update the data
                const newTempData = chartData.tempData.map(d => ({
                    date: new Date(d[0]),
                    value: d[1]
                }));

                const newRainData = chartData.rainData.map(d => ({
                    date: new Date(d[0]),
                    value: d[1]
                }));

                // Update scales
                x.domain(d3.extent(newTempData, d => d.date));
                yTemp.domain([0, d3.max(newTempData, d => d.value) * 1.2]);
                yRain.domain([0, d3.max(newRainData, d => d.value) * 1.2]);

                // Update axes
                svg.select('.x-axis')
                    .transition()
                    .duration(750)
                    .call(d3.axisBottom(x).tickFormat(d3.timeFormat('%H:%M')));

                svg.select('.y-axis-temp')
                    .transition()
                    .duration(750)
                    .call(d3.axisLeft(yTemp));

                svg.select('.y-axis-rain')
                    .transition()
                    .duration(750)
                    .call(d3.axisLeft(yRain));

                // Update lines
                tempPath.datum(newTempData)
                    .transition()
                    .duration(750)
                    .attr('d', tempLine);

                rainPath.datum(newRainData)
                    .transition()
                    .duration(750)
                    .attr('d', rainLine);

                // Update dots
                const tempDotsUpdate = svg.selectAll('.temp-dot')
                    .data(newTempData);

                tempDotsUpdate.exit().remove();

                tempDotsUpdate.enter()
                    .append('circle')
                    .attr('class', 'temp-dot')
                    .attr('r', 4)
                    .attr('fill', '#EF4444')
                    .merge(tempDotsUpdate)
                    .transition()
                    .duration(750)
                    .attr('cx', d => x(d.date))
                    .attr('cy', d => yTemp(d.value));

                const rainDotsUpdate = svg.selectAll('.rain-dot')
                    .data(newRainData);

                rainDotsUpdate.exit().remove();

                rainDotsUpdate.enter()
                    .append('circle')
                    .attr('class', 'rain-dot')
                    .attr('r', 4)
                    .attr('fill', '#3B82F6')
                    .merge(rainDotsUpdate)
                    .transition()
                    .duration(750)
                    .attr('cx', d => x(d.date))
                    .attr('cy', d => yRain(d.value));
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
                // console.log(data);

                return `
                <div class="p-2">
                    <div class="space-y-1">
                        <p>💨 Angin: ${data.wind.speed} m/s</p>
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

        // Add some CSS for smoother transitions
        const style = document.createElement('style');
        style.textContent = `
            .temp-area {
                transition: opacity 0.75s ease-in-out;
            }
            .rain-line {
                transition: opacity 0.75s ease-in-out;
            }
        `;
        document.head.appendChild(style);
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