<div class="min-h-screen">
    <div class="container mx-auto px-4 py-6">

        <!-- Main Container with Background -->
        <div class="mx-auto">
            <!-- Search and Filter Container -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header Section -->
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Water Level Monitoring</h1>
                    <p class="text-gray-500">Last update: {{ now()->format('d F Y') }}</p>
                </div>

                <!-- Search and Filter Grid -->
                <div class="grid md:grid-cols-3 gap-6 mb-8">
                    <!-- Search Bar -->
                    <div class="md:col-span-2">
                        <div class="relative">
                            <input
                                wire:model.live="searchEstate"
                                type="text"
                                placeholder="Search estate by name or location..."
                                class="w-full bg-white rounded-lg py-4 pl-12 pr-4 focus:outline-none focus:ring-2 focus:ring-blue-500 border border-gray-200 shadow-sm hover:border-blue-300 transition-all duration-300">
                            <div class="absolute left-4 top-1/2 transform -translate-y-1/2">
                                <svg class="w-5 h-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="11" cy="11" r="8"></circle>
                                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Date Filters -->
                    <div class="relative group">
                        <button class="w-full bg-white rounded-lg py-4 px-6 text-left shadow-sm border border-gray-200 hover:border-blue-300 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-700">{{ $startDate ? 'Filtered Date' : 'Select Date Range' }}</span>
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </button>

                        <!-- Date Range Picker Dropdown -->
                        <div class="absolute right-0 mt-2 w-96 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50">
                            <x-utils.date-range
                                :start-date="$startDate"
                                :end-date="$endDate"
                                class="shadow-xl border-0" />
                        </div>
                    </div>
                </div>

                <!-- Active Filters Display -->
                @if($searchEstate || $startDate || $endDate)
                <div class="flex flex-wrap items-center gap-3 mb-6 p-4 bg-blue-50 rounded-lg">
                    <span class="text-sm font-medium text-blue-700">Active Filters:</span>
                    @if($searchEstate)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-blue-100 text-blue-800">
                        Search: {{ $searchEstate }}
                        <button wire:click="$set('searchEstate', '')" class="ml-2 hover:text-blue-600">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </span>
                    @endif
                    @if($startDate || $endDate)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-blue-100 text-blue-800">
                        Date: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }}
                        {{ $endDate ? ' - ' . \Carbon\Carbon::parse($endDate)->format('d M Y') : '' }}
                        <button wire:click="clearDateFilter" class="ml-2 hover:text-blue-600">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </span>
                    @endif
                </div>
                @endif

                <!-- Results Section -->

                @if($searchEstate && empty($filteredEstates))
                <!-- Results Header -->
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-lg font-semibold text-gray-900">Estate List</h2>
                    <span class="text-sm text-gray-500">{{ count($filteredEstates) }} results found</span>
                </div>

                <!-- No Results Message -->

                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No results found</h3>
                    <p class="mt-1 text-sm text-gray-500">No estates found matching "{{ $searchEstate }}"</p>
                </div>
                @else
                <!-- Estate Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-6">
                    @foreach($filteredEstates as $estate)
                    <button
                        wire:click="selectEstate({{ $estate['id'] }})"
                        class="group hover:shadow-lg transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 rounded-lg">
                        <x-utils.card :estate="$estate" />
                    </button>
                    @endforeach
                </div>
                @endif

            </div>
        </div>



        <!-- Chart Card Section -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8 mt-8">
            <!-- Temperature Chart -->
            <div class="mt-4 weather-card bg-white dark:bg-gray-800 rounded-2xl p-4 shadow-sm hover:shadow-md transition-shadow overflow-hidden relative">
                <!-- Header Section -->
                <div class="flex flex-col space-y-4 sm:space-y-0 sm:flex-row sm:items-center sm:justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-chart-line text-blue-500"></i>
                        <h2 class="font-semibold text-gray-800 dark:text-white">Data Riwayat {{ $selectedStation }}</h2>
                    </div>

                    <!-- Toggle Buttons Container -->
                    <div wire:ignore class="flex flex-col space-y-3 sm:flex-row sm:space-y-0 sm:gap-2 w-full sm:w-auto">
                        <!-- Data Period Toggles -->
                        <div class="flex flex-wrap gap-2 sm:mr-4">
                            <button id="todayButton"
                                class="flex-1 sm:flex-none px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 ease-in-out bg-blue-500 text-white">
                                <i class="fas fa-calendar-day mr-1"></i>Hari Ini
                            </button>
                            <button id="weekButton"
                                class="flex-1 sm:flex-none px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 ease-in-out bg-gray-200 text-gray-700">
                                <i class="fas fa-calendar-week mr-1"></i>Minggu Ini
                            </button>
                            <button id="monthButton"
                                class="flex-1 sm:flex-none px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 ease-in-out bg-gray-200 text-gray-700">
                                <i class="fas fa-calendar-alt mr-1"></i>Bulan Ini
                            </button>
                        </div>

                        <!-- Data Type Toggles -->
                        <div wire:ignore class="grid grid-cols-2 sm:flex gap-2">
                            <button id="blokButton"
                                class="px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 ease-in-out bg-blue-500 text-white">
                                <i class="fas fa-water mr-1"></i>Level Blok
                            </button>
                            <button id="paritButton"
                                class="px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 ease-in-out bg-gray-200 text-gray-700">
                                <i class="fas fa-stream mr-1"></i>Level Parit
                            </button>
                            <button id="sensorButton"
                                class="px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 ease-in-out bg-gray-200 text-gray-700">
                                <i class="fas fa-ruler-vertical mr-1"></i>Sensor Distance
                            </button>
                            <button id="rekapButton"
                                class="px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 ease-in-out bg-gray-200 text-gray-700">
                                <i class="fas fa-chart-bar mr-1"></i>Rekap
                            </button>

                            <!-- Tombol Download -->
                            <button id="downloadChartButton"
                                class="px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 ease-in-out bg-green-500 text-white hover:bg-green-600">
                                <i class="fas fa-download mr-1"></i>Download PDF
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Chart Container -->
                <div wire:ignore class="h-[300px] sm:h-[400px]">
                    <div id="combinedChart" class="w-full h-full"></div>
                </div>
            </div>

        </div>

        <!-- Bottom Section with Cards and Map -->
        <!-- Main Container -->
        <div class="bg-white p-6">
            <h2 class="text-lg font-medium mb-4">Lokasi Titik Water Level</h2>

            <!-- Main Grid: 2 Columns -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Left Column: Location, Map and Gallery -->
                <div class="space-y-4">
                    <!-- Location Info Card -->
                    <div class="bg-blue-50 rounded-lg p-4 shadow-sm">
                        <div class="flex items-center justify-between">
                            <!-- Location Info -->
                            <div class="space-y-2 flex-1">
                                <div class="flex items-center space-x-2">
                                    <svg class="h-5 w-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    </svg>
                                    <span class="text-sm text-gray-700 break-all" id="latlonmapsdiv" wire:ignore>-</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <svg class="h-5 w-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                                    </svg>
                                    <span class="text-sm text-gray-700 break-all" id="namaestatediv" wire:ignore>-</span>
                                </div>
                            </div>
                            @if (SuperAdmin())
                            <x-filament::modal :close-by-clicking-away="false" id="mapscordinates" width="5xl">
                                <x-slot name="trigger">
                                    @if (SuperAdmin())
                                    <x-filament::button
                                        icon="heroicon-o-map-pin"
                                        class="bg-indigo-500 text-white rounded-lg px-4 py-2 text-sm hover:bg-indigo-600">
                                        Update Maps
                                    </x-filament::button>
                                    @endif
                                </x-slot>
                                <x-slot name="heading">
                                    Insert/Update Maps Coordinates
                                </x-slot>
                                <x-slot name="description">
                                    Click on the map to select coordinates or search for a location
                                </x-slot>
                                <form wire:submit="updateStationCoordinates">
                                    <div class="space-y-4">
                                        @if (!$selectedStation)
                                        <div class="p-4 bg-yellow-50 text-yellow-700 rounded-lg">
                                            Please select Station before updating coordinates.
                                        </div>
                                        @endif

                                        <!-- Coordinates Display -->
                                        <div class="grid grid-cols-2 gap-4 mb-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Latitude</label>
                                                <input type="number"
                                                    step="any"
                                                    wire:model="selectedLat"
                                                    class="w-full rounded-lg border-gray-300"
                                                    placeholder="Enter latitude">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Longitude</label>
                                                <input type="number"
                                                    step="any"
                                                    wire:model="selectedLon"
                                                    class="w-full rounded-lg border-gray-300"
                                                    placeholder="Enter longitude">
                                            </div>
                                        </div>

                                        <!-- Add a note about input methods -->
                                        <div class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                            <p>You can update coordinates by:</p>
                                            <ul class="list-disc list-inside">
                                                <li>Clicking directly on the map</li>
                                                <li>Manually entering the coordinates above</li>
                                                <li>Using the search function on the map</li>
                                            </ul>
                                        </div>

                                        <!-- Submit Button -->
                                        <div
                                            <x-filament::button
                                            type="submit"
                                            :disabled="!$selectedStation">
                                            Save Coordinates
                                            </x-filament::button>
                                        </div>
                                    </div>
                                </form>
                            </x-filament::modal>
                            @endif
                        </div>
                    </div>

                    <!-- Map Section -->
                    <div class="relative">
                        <div wire:ignore id="map" class="w-full rounded-lg z-10 shadow-sm" style="min-height: 300px; @screen lg { min-height: 400px; }"></div>
                    </div>

                    <!-- Photo Gallery Section -->
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl shadow-sm p-6 space-y-6">
                        <!-- Header Section -->
                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                            <h2 class="text-xl font-semibold text-gray-800">Photo Gallery</h2>
                        </div>
                        @if (SuperAdmin())
                        <!-- Upload Button -->
                        <x-filament::modal :close-by-clicking-away="false" id="importGalery" width="5xl">
                            <x-slot name="trigger">
                                <x-filament::button
                                    icon="heroicon-o-arrow-up-tray"
                                    class="bg-indigo-600 text-white rounded-lg px-5 py-2.5 text-sm font-medium transition-colors hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                    Import Images
                                </x-filament::button>
                            </x-slot>
                            <x-slot name="heading">
                                Gallery TPH
                            </x-slot>
                            <x-slot name="description">
                                Insert new images for gallery waterlevel
                            </x-slot>
                            @if($selectedStation)
                            @livewire('add-galery-water-level', ['selectedStation' => $selectedStation])
                            @else
                            <div class="text-center py-4 text-gray-500">
                                <span>Please select water station first</span>
                            </div>
                            @endif
                        </x-filament::modal>
                        @endif
                        <div>
                            <!-- Gallery Grid -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                                @foreach($galleryImages as $image)
                                <div class="group relative aspect-square rounded-lg overflow-hidden bg-gray-100 hover:shadow-lg transition-all duration-300">
                                    <!-- Image -->
                                    <img
                                        src="{{ asset('storage/' . $image) }}"
                                        class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                                        alt="Gallery image">

                                    <!-- Hover Overlay -->
                                    <div class="absolute inset-0 bg-black bg-opacity-0 sm:group-hover:bg-opacity-40 bg-opacity-40 sm:bg-opacity-0 transition-all duration-300 flex items-center justify-center">
                                        <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                            <!-- View Button -->
                                            <button
                                                wire:click="$set('selectedImage', '{{ $image }}')"
                                                @click="$dispatch('open-modal', 'image-modal')"
                                                class="p-2 bg-white rounded-full hover:bg-gray-100 transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </button>

                                            <!-- Delete Button -->
                                            <button
                                                wire:click="$set('imageToDelete', '{{ $image }}')"
                                                @click="$dispatch('open-modal', 'delete-confirmation')"
                                                class="p-2 bg-white rounded-full hover:bg-gray-100 transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>

                            <!-- Empty State -->
                            @if(count($galleryImages) === 0)
                            <div class="text-center py-12">
                                <div class="bg-gray-50 rounded-lg p-6 inline-block">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900">No images</h3>
                                    <p class="mt-1 text-sm text-gray-500">Get started by uploading your first image.</p>
                                </div>
                            </div>
                            @endif

                            <!-- Image Modal -->
                            <div
                                x-data="{ 
                                    show: false,
                                    image: null
                                }"
                                x-show="show"
                                x-on:open-modal.window="if ($event.detail === 'image-modal') { show = true }"
                                x-on:close-modal.window="if ($event.detail === 'image-modal') { show = false }"
                                x-on:keydown.escape.window="show = false"
                                class="fixed inset-0 z-50 overflow-y-auto px-4"
                                style="display: none;">
                                <!-- Background overlay -->
                                <div class="fixed inset-0 bg-black bg-opacity-75 transition-opacity"></div>

                                <!-- Modal content -->
                                <div class="flex min-h-screen items-center justify-center">
                                    <div class="relative max-w-4xl w-full mx-auto">
                                        <!-- Close button -->
                                        <button
                                            @click="show = false"
                                            class="absolute -top-4 -right-4 bg-white rounded-full p-2 shadow-lg hover:bg-gray-100 z-10">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>

                                        <!-- Image container -->
                                        <div class="bg-white rounded-lg shadow-xl overflow-hidden">
                                            <img
                                                src="{{ $selectedImage ? asset('storage/' . $selectedImage) : '' }}"
                                                class="w-full h-auto max-h-[80vh] object-contain"
                                                alt="Selected image">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Delete Confirmation Modal -->
                            <div
                                x-data="{ show: false }"
                                x-show="show"
                                x-on:open-modal.window="if ($event.detail === 'delete-confirmation') { show = true }"
                                x-on:close-modal.window="if ($event.detail === 'delete-confirmation') { show = false }"
                                x-on:keydown.escape.window="show = false"
                                class="fixed inset-0 z-50 overflow-y-auto"
                                style="display: none;">
                                <!-- Background overlay -->
                                <div class="fixed inset-0 bg-black bg-opacity-75 transition-opacity"></div>

                                <!-- Modal content -->
                                <div class="flex min-h-screen items-center justify-center p-4">
                                    <div class="relative bg-white rounded-lg max-w-md w-full p-6">
                                        <h2 class="text-lg font-medium text-gray-900">
                                            Delete Image
                                        </h2>
                                        <p class="mt-2 text-sm text-gray-600">
                                            Are you sure you want to delete this image? This action cannot be undone.
                                        </p>
                                        <div class="mt-6 flex justify-end space-x-3">
                                            <button
                                                @click="show = false"
                                                class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                                                Cancel
                                            </button>
                                            <button
                                                wire:click="deleteImage"
                                                @click="show = false"
                                                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                                                Delete Image
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Table Card -->
                <div class="bg-white rounded-lg shadow mt-6 lg:mt-0">
                    <div class="p-6">
                        <div class="mb-4 flex flex-col sm:flex-row gap-4">
                            @if (SuperAdmin())
                            <x-filament::modal :close-by-clicking-away="false" id="waterlevel-modal">
                                <x-slot name="trigger">
                                    <x-filament::button
                                        icon="heroicon-o-arrow-up-tray"
                                        class="bg-indigo-500 text-white rounded-lg px-4 py-2 text-sm hover:bg-indigo-600 ml-auto">
                                        Insert Data
                                    </x-filament::button>
                                </x-slot>
                                <x-slot name="heading">
                                    Excel Water Level
                                </x-slot>
                                <x-slot name="description">
                                    Insert Data Excel Water Level here
                                </x-slot>
                                <form wire:submit="saveForm" wire:loading.attr="disabled">
                                    {{ $this->form }}

                                    <div class="flex justify-end gap-x-3 mt-6">
                                        <x-filament::button
                                            type="submit"
                                            wire:loading.attr="disabled"
                                            wire:loading.class="opacity-50 cursor-wait">
                                            <span wire:loading.remove>Upload</span>
                                            <span wire:loading>Processing...</span>
                                        </x-filament::button>
                                    </div>
                                </form>
                            </x-filament::modal>
                            @endif
                        </div>
                        <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4 flex items-center flex-wrap">
                            <i class="fas fa-table mr-2 text-blue-500"></i>Recent Measurements
                        </h2>
                        <div class="overflow-x-auto -mx-6 px-6">
                            {{ $this->table }}
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    @script
    <script type="module">
        let map = L.map('map', {
            preferCanvas: true,
        }).setView([-2.2745234, 111.61404248], 13);

        // Add tile layer
        L.tileLayer('http://{s}.google.com/vt?lyrs=s&x={x}&y={y}&z={z}', {
            maxZoom: 20,
            subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
        }).addTo(map);

        let layerGroup = L.layerGroup().addTo(map);


        // Add search control functionality
        const searchControl = L.Control.geocoder({
            defaultMarkGeocode: false
        }).addTo(map);
        let isUpdateMode = false;


        // Handle search results
        searchControl.on('markgeocode', function(e) {
            const bbox = e.geocode.bbox;
            const poly = L.polygon([
                bbox.getSouthEast(),
                bbox.getNorthEast(),
                bbox.getNorthWest(),
                bbox.getSouthWest()
            ]);
            map.fitBounds(poly.getBounds());

            const latlng = e.geocode.center;
            updateMarkerPosition(latlng);
        });

        // Handle map clicks
        map.on('click', function(e) {
            // Check if user is SuperAdmin and has selected both wilayah and station
            const hasRequiredSelections = @json(SuperAdmin() && !empty($selectedWilayah) && !empty($selectedStation));

            if (isUpdateMode && hasRequiredSelections) {
                updateMarkerPosition(e.latlng);
            }
        });

        let currentMarker = null;

        function updateMarkerPosition(latlng) {
            // Validate coordinates
            if (!isValidLatLng(latlng.lat, latlng.lng)) {
                console.warn('Invalid coordinates:', latlng);
                return;
            }

            // Clear existing marker
            if (currentMarker) {
                map.removeLayer(currentMarker);
            }

            // Add new marker
            currentMarker = L.marker(latlng, {
                draggable: true
            }).addTo(map);

            // Update coordinates in Livewire
            @this.dispatch('set-coordinates', {
                lat: latlng.lat,
                lng: latlng.lng
            });

            // Handle marker drag
            currentMarker.on('dragend', function(event) {
                const marker = event.target;
                const position = marker.getLatLng();
                @this.dispatch('set-coordinates', {
                    lat: position.lat,
                    lng: position.lng
                });
            });
        }

        // Add coordinate validation function
        function isValidLatLng(lat, lng) {
            return lat >= -90 && lat <= 90 && lng >= -180 && lng <= 180;
        }

        // Update the Livewire event listener to handle manual input



        $wire.on('set-coordinates', ({
            lat,
            lng
        }) => {
            if (lat && lng) {
                updateMarkerPosition(L.latLng(lat, lng));
                // If coordinates are valid, center the map on the new position
                map.setView([lat, lng], 15);
            }
        });

        // maps for station
        $wire.on('updateMapMarker', (eventData) => {
            const data = Array.isArray(eventData) ? eventData[0] : eventData;
            const coordinates = data.coordinates;
            const station = data.station;

            if (coordinates && coordinates.lat && coordinates.lon) {
                // Clear previous markers
                layerGroup.clearLayers();

                // Create marker with popup
                const marker = L.marker([coordinates.lat, coordinates.lon], {
                    title: station.location
                }).bindPopup(`
                <div class="text-center">
                <b>Tanggal: ${station.datetime ? station.datetime : 'No Data'}</b><br>
                <b>Water Station: ${station.location}</b><br>
                Level Blok: ${station.level_blok ? station.level_blok : 'No Data'}<br>
                Level Parit: ${station.level_parit ? station.level_parit : 'No Data'}<br>
                Sensor Distance: ${station.sensor_distance ? station.sensor_distance : 'No Data'}<br>
                </div>
            `);

                // Add marker to layer group
                layerGroup.addLayer(marker);

                // Fit bounds with padding
                map.setView([coordinates.lat, coordinates.lon], 15);
                marker.openPopup();
            }

            // Mendapatkan elemen dengan ID yang benar
            let latlon = document.getElementById('latlonmapsdiv');
            let nameestate = document.getElementById('namaestatediv');

            // Mengatur nilai yang sesuai
            if (latlon && nameestate) {
                latlon.innerText = `${coordinates.lat}, ${coordinates.lon}`;
                nameestate.innerText = station.location;
            }
        });



        // untuk futur search 
        // Search functionality
        // Tambahkan pengecekan elemen sebelum menambahkan event listener
        const estateSearchElement = document.getElementById('estateSearch');
        if (estateSearchElement) {
            estateSearchElement.addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase();
                document.querySelectorAll('.estate-item').forEach(item => {
                    const estateName = item.querySelector('p').textContent.toLowerCase();
                    if (estateName.includes(searchTerm)) {
                        item.style.display = '';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        }
        // Toggle active state
        function toggleEstate(estateId) {
            document.querySelectorAll('.estate-item').forEach(item => {
                const isTarget = item.dataset.estate === estateId;
                const cardDiv = item.querySelector('div');

                if (isTarget) {
                    item.classList.add('active');
                    cardDiv.classList.remove('bg-white', 'shadow');
                    cardDiv.classList.add('bg-blue-50', 'border-2', 'border-blue-500');
                    const text = cardDiv.querySelectorAll('p');
                    text[0].classList.remove('text-gray-500');
                    text[0].classList.add('text-blue-700');
                    text[1].classList.remove('text-gray-900');
                    text[1].classList.add('text-blue-900');
                } else {
                    item.classList.remove('active');
                    cardDiv.classList.remove('bg-blue-50', 'border-2', 'border-blue-500');
                    cardDiv.classList.add('bg-white', 'shadow');
                    const text = cardDiv.querySelectorAll('p');
                    text[0].classList.remove('text-blue-700');
                    text[0].classList.add('text-gray-500');
                    text[1].classList.remove('text-blue-900');
                    text[1].classList.add('text-gray-900');
                }
            });
        }

        // Initialize variables
        let chart;
        let currentView = 'blok';
        let currentPeriod = 'today';

        // Define style configurations for each view type
        const styleConfigs = {
            blok: {
                colors: ['#3B82F6'], // Blue
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
            parit: {
                colors: ['#10B981'], // Green
                gradient: {
                    shade: 'dark',
                    type: "vertical",
                    shadeIntensity: 0.5,
                    gradientToColors: ['#059669'],
                    inverseColors: true,
                    opacityFrom: 0.8,
                    opacityTo: 0.2,
                    stops: [0, 100]
                }
            },
            sensor: {
                colors: ['#F59E0B'], // Orange
                gradient: {
                    shade: 'dark',
                    type: "vertical",
                    shadeIntensity: 0.5,
                    gradientToColors: ['#D97706'],
                    inverseColors: true,
                    opacityFrom: 0.8,
                    opacityTo: 0.2,
                    stops: [0, 100]
                }
            },
            rekap: {
                colors: ['#3B82F6', '#10B981', '#F59E0B'],
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
        const chartOptions = {
            chart: {
                type: 'area',
                height: '100%',
                animations: {
                    enabled: true,
                    easing: 'easeinout',
                    dynamicAnimation: {
                        speed: 1000
                    }
                },
                toolbar: {
                    show: true
                },
                zoom: {
                    enabled: true
                }
            },
            stroke: {
                curve: 'smooth',
                width: 2
            },
            xaxis: {
                type: 'datetime',
                labels: {
                    datetimeUTC: false,
                    format: 'dd MMM HH:mm'
                },
                title: {
                    text: 'Waktu'
                }
            },
            yaxis: {
                title: {
                    text: 'Level (cm)'
                },
                labels: {
                    formatter: (value) => value.toFixed(2)
                }
            },
            tooltip: {
                x: {
                    format: 'dd MMM yyyy HH:mm'
                },
                y: {
                    formatter: (value) => `${value.toFixed(2)} cm`
                }
            },
            legend: {
                position: 'top',
                horizontalAlign: 'center'
            },
            grid: {
                borderColor: '#f1f1f1'
            },
            noData: {
                text: 'No Data...'
            },
            fill: {
                type: 'gradient',
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

        function updateChart(data, type = currentView, period = currentPeriod) {
            currentView = type;
            currentPeriod = period;
            console.log(data);

            // Update button states
            updateButtonStates();

            // Update chart options with new style config
            const newOptions = {
                ...chartOptions,
                colors: styleConfigs[type].colors,
                fill: {
                    ...chartOptions.fill,
                    gradient: styleConfigs[type].gradient
                },
                series: data.series
            };

            if (chart) {
                chart.destroy();
            }

            chart = new ApexCharts(document.querySelector("#combinedChart"), newOptions);
            chart.render();
        }

        function updateButtonStates() {
            // Update period buttons
            ['today', 'week', 'month'].forEach(period => {
                const button = document.getElementById(`${period}Button`);
                if (button) {
                    if (period === currentPeriod) {
                        button.classList.add('bg-blue-500', 'text-white');
                        button.classList.remove('bg-gray-200', 'text-gray-700');
                    } else {
                        button.classList.remove('bg-blue-500', 'text-white');
                        button.classList.add('bg-gray-200', 'text-gray-700');
                    }
                }
            });

            // Update type buttons
            ['blok', 'parit', 'sensor', 'rekap'].forEach(type => {
                const button = document.getElementById(`${type}Button`);
                if (button) {
                    if (type === currentView) {
                        button.classList.add('bg-blue-500', 'text-white');
                        button.classList.remove('bg-gray-200', 'text-gray-700');
                    } else {
                        button.classList.remove('bg-blue-500', 'text-white');
                        button.classList.add('bg-gray-200', 'text-gray-700');
                    }
                }
            });
        }

        // Button click handlers
        document.getElementById('todayButton').addEventListener('click', () => {
            currentPeriod = 'today';
            updateButtonStates();
            $wire.updateChart('today', currentView);
        });

        document.getElementById('weekButton').addEventListener('click', () => {
            currentPeriod = 'week';
            updateButtonStates();
            $wire.updateChart('week', currentView);
        });

        document.getElementById('monthButton').addEventListener('click', () => {
            currentPeriod = 'month';
            updateButtonStates();
            $wire.updateChart('month', currentView);
        });

        document.getElementById('blokButton').addEventListener('click', () => {
            currentView = 'blok';
            updateButtonStates();
            $wire.updateChart(currentPeriod, 'blok');
        });

        document.getElementById('paritButton').addEventListener('click', () => {
            currentView = 'parit';
            updateButtonStates();
            $wire.updateChart(currentPeriod, 'parit');
        });

        document.getElementById('sensorButton').addEventListener('click', () => {
            currentView = 'sensor';
            updateButtonStates();
            $wire.updateChart(currentPeriod, 'sensor');
        });

        document.getElementById('rekapButton').addEventListener('click', () => {
            currentView = 'rekap';
            updateButtonStates();
            $wire.updateChart(currentPeriod, 'rekap');
        });

        // Listen for chart updates from Livewire
        $wire.on('updateChart', (response) => {
            if (Array.isArray(response)) {
                response = response[0];
            }
            updateChart(response.data, response.type, response.period);
        });

        // Listen for initial state
        $wire.on('initChartState', (state) => {
            if (Array.isArray(state)) {
                state = state[0];
            }
            currentView = state.type;
            currentPeriod = state.period;
            updateButtonStates();
        });




        Livewire.on('showLoadingScreen', () => {
            showLoadingScreen();
        });

        Livewire.on('hideLoadingScreen', () => {
            hideLoadingScreen();
        });


        // Tambahkan di bagian JavaScript

        // Fungsi untuk download chart
        async function downloadChart() {
            if (!chart) {
                Livewire.dispatch('notify', {
                    type: 'error',
                    message: 'Chart not ready. Please wait and try again.'
                });
                return;
            }

            try {
                // Show loading indicator
                showLoadingScreen();

                // Get chart image
                const {
                    imgURI
                } = await chart.dataURI();
                if (!imgURI) {
                    throw new Error('Failed to generate chart image');
                }

                // Call the Livewire method to generate and download PDF
                await $wire.GeneratePDF(imgURI);

            } catch (error) {
                console.error('Error downloading chart:', error);
                Livewire.dispatch('notify', {
                    type: 'error',
                    message: error.message || 'Failed to generate PDF report'
                });
            } finally {
                hideLoadingScreen();
            }
        }

        // Add event listener with debounce
        const downloadButton = document.getElementById('downloadChartButton');
        if (downloadButton) {
            let isProcessing = false;
            downloadButton.addEventListener('click', async () => {
                if (isProcessing) return;

                isProcessing = true;
                downloadButton.disabled = true;

                try {
                    await downloadChart();
                } finally {
                    isProcessing = false;
                    downloadButton.disabled = false;
                }
            });
        }
    </script>
    @endscript
</div>