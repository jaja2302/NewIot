<x-layouts.app>
    @section('title', 'Dashboard')

    <div class="text-gray-800">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-4xl font-bold text-blue-600">07:32 AM</h1>
                <p class="text-xl">Wednesday, 14 April, 2021</p>
                <p class="text-2xl mt-2 text-blue-600">Good morning, Asif!</p>
            </div>
            <div class="flex items-center space-x-4">
                <input type="text" placeholder="Search..." class="bg-white border border-gray-300 rounded-full px-4 py-2 text-gray-700 placeholder-gray-500">
                <div class="w-10 h-10 bg-blue-500 rounded-full"></div>
            </div>
        </div>

        <!-- Weather Forecast -->
        <div class="grid grid-cols-6 gap-4 mb-8">
            @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri'] as $day)
            <div class="bg-white rounded-lg p-4 text-center shadow">
                <p class="font-bold">{{ $day }}</p>
                <i class="fas fa-sun text-2xl my-2 text-yellow-500"></i>
                <p>28°</p>
            </div>
            @endforeach
        </div>

        <!-- Main Content -->
        <div class="grid grid-cols-3 gap-6">
            <!-- Left Column -->
            <div class="col-span-2 space-y-6">
                <!-- Air Quality Index -->
                <div class="bg-white rounded-lg p-6 shadow">
                    <h2 class="text-2xl font-bold mb-4 text-blue-600">Air Quality Index</h2>
                    <!-- Add air quality index content here -->
                </div>

                <!-- Monthly Rainfall -->
                <div class="bg-white rounded-lg p-6 shadow">
                    <h2 class="text-2xl font-bold mb-4 text-blue-600">Monthly Rainfall</h2>
                    <!-- Add monthly rainfall chart here -->
                </div>
            </div>

            <!-- Right Column -->
            <div class="space-y-6">
                <!-- Today's Weather -->
                <div class="bg-blue-500 rounded-lg p-6 text-white shadow">
                    <h2 class="text-2xl font-bold mb-2">Dhaka</h2>
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-6xl font-bold">29°</p>
                            <p class="text-xl">Sunny</p>
                        </div>
                        <i class="fas fa-cloud-sun text-5xl"></i>
                    </div>
                    <div class="mt-4">
                        <p>Wind: 19 km/h</p>
                        <p>Hum: 22%</p>
                    </div>
                </div>

                <!-- Other Cities -->
                <div class="bg-pink-500 rounded-lg p-6 text-white shadow">
                    <h2 class="text-2xl font-bold mb-2">Tokyo</h2>
                    <p class="text-4xl font-bold">26°</p>
                    <div class="mt-2">
                        <p>Wind: 15 km/h</p>
                        <p>Hum: 28%</p>
                    </div>
                </div>

                <div class="bg-orange-500 rounded-lg p-6 text-white shadow">
                    <h2 class="text-2xl font-bold mb-2">NY</h2>
                    <p class="text-4xl font-bold">31°</p>
                    <div class="mt-2">
                        <p>Wind: 17 km/h</p>
                        <p>Hum: 25%</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>