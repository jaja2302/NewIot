<div class="flex h-screen">
    <!-- Leaflet Map Background -->
    <div id="map" class="flex-grow z-0 h-3/4"></div>

    <!-- Right Sidebar -->
    <div class="w-80 bg-white shadow-lg z-10 flex flex-col h-3/4 overflow-y-auto">
        <!-- Filter Controls -->
        <div class="p-4">
            <h3 class="text-lg font-semibold mb-3">Filters</h3>
            <div class="space-y-3">
                <div>
                    <label for="date" class="block text-sm font-medium text-gray-700">Date</label>
                    <input type="date" id="date" wire:model="selectedDate" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>
                <div>
                    <label for="wilayah" class="block text-sm font-medium text-gray-700">Wilayah</label>
                    <select id="wilayah" wire:model="selectedWilayah" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" wire:change="updateSelectedStation($event.target.value)">
                        <option value="">Select Wilayah</option>
                        @foreach($wilayah as $wil)
                        <option value="{{ $wil->id }}">{{ $wil->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="station" class="block text-sm font-medium text-gray-700">Station</label>
                    <select id="station" wire:model="selectedStation" wire:change="onChangeStation($event.target.value)" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <option value="">Select Station</option>
                        @foreach($stations as $station)
                        <option value="{{ $station->id }}">{{ $station->location }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Divider -->
        <hr class="border-gray-200">

        <!-- Water Level IoT Details -->
        <div class="p-4">
            <h2 class="text-2xl font-bold mb-2">Water Level IoT</h2>
            <p class="text-sm mb-4">Portal website ini digunakan untuk memonitoring data dari proses pemantuan ketinggian air di</p>
            <p class="text-sm mb-4">Update data device terakhir pada</p>

            <!-- Latest Levels -->
            <div class="mb-4">
                <h3 class="font-semibold mb-2">Level In Terakhir</h3>
                <h3 class="font-semibold mb-2">Level Out Terakhir</h3>
                <h3 class="font-semibold mb-2">Level Actual Terakhir</h3>
            </div>

            <!-- Average Levels -->
            <div class="mb-4">
                <h3 class="font-semibold mb-2 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1H3a1 1 0 01-1-1V4zM8 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1H9a1 1 0 01-1-1V4zM15 3a1 1 0 00-1 1v12a1 1 0 001 1h2a1 1 0 001-1V4a1 1 0 00-1-1h-2z" />
                    </svg>
                    Rata rata Level In
                </h3>
                <h3 class="font-semibold mb-2 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1H3a1 1 0 01-1-1V4zM8 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1H9a1 1 0 01-1-1V4zM15 3a1 1 0 00-1 1v12a1 1 0 001 1h2a1 1 0 001-1V4a1 1 0 00-1-1h-2z" />
                    </svg>
                    Rata rata Level Out
                </h3>
                <h3 class="font-semibold mb-2 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1H3a1 1 0 01-1-1V4zM8 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1H9a1 1 0 01-1-1V4zM15 3a1 1 0 00-1 1v12a1 1 0 001 1h2a1 1 0 001-1V4a1 1 0 00-1-1h-2z" />
                    </svg>
                    Rata rata Level Actual
                </h3>
            </div>

            <!-- Water Limits -->
            <div>
                <h3 class="font-semibold mb-2 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M14.763 3.398a.75.75 0 01.79.067l4.264 3.262a.75.75 0 010 1.179l-4.264 3.262a.75.75 0 01-1.18-.617V8.25h-1.5A3.75 3.75 0 009.123 12v1.5h2.25a.75.75 0 010 1.5h-3a.75.75 0 01-.75-.75V12a5.25 5.25 0 015.25-5.25h1.5V4.015a.75.75 0 01.39-.617z" clip-rule="evenodd" />
                    </svg>
                    Batas Atas Air
                </h3>
                <h3 class="font-semibold mb-2 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.237 3.398a.75.75 0 01.79.067l4.264 3.262a.75.75 0 010 1.179l-4.264 3.262a.75.75 0 01-1.18-.617V8.25H3.347A3.75 3.75 0 00.597 12v1.5h2.25a.75.75 0 010 1.5h-3a.75.75 0 01-.75-.75V12a5.25 5.25 0 015.25-5.25h1.5V4.015a.75.75 0 01.39-.617z" clip-rule="evenodd" />
                    </svg>
                    Batas Bawah Air
                </h3>
            </div>
        </div>
    </div>

    <script type="module">
        let map;
        let marker;

        document.addEventListener('livewire:load', function() {
            initMap();

            Livewire.on('updateMap', (data) => {
                console.log("updateMap event received", data);
                const latlon = JSON.parse(data);
                updateMarker(latlon);
            });
        });

        function initMap() {
            map = L.map('map', {
                preferCanvas: true,
            }).setView([-2.2745234, 111.61404248], 13);

            L.tileLayer('http://{s}.google.com/vt?lyrs=s&x={x}&y={y}&z={z}', {
                maxZoom: 20,
                subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
            }).addTo(map);

            console.log("Map initialized");
        }

        function updateMarker(latlon) {
            const newLatLng = [parseFloat(latlon.lat), parseFloat(latlon.lon)];

            if (marker) {
                map.removeLayer(marker);
            }

            marker = L.marker(newLatLng).addTo(map);
            map.setView(newLatLng, 13);
        }
    </script>

</div>