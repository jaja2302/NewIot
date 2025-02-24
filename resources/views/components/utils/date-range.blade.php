@props(['startDate' => null, 'endDate' => null])

<div class="bg-white p-6 rounded-lg shadow-lg border border-gray-200">
    <div class="space-y-4">
        <!-- Section Title -->
        <div class="mb-4">
            <h3 class="text-lg font-medium text-gray-900">Date Range Filter</h3>
            <p class="text-sm text-gray-500">Select a date range to filter results</p>
        </div>

        <!-- Quick Selection Buttons -->
        <div class="grid grid-cols-2 gap-2 mb-4">
            <div class="space-y-2">
                <h4 class="text-sm font-medium text-gray-700">Previous</h4>
                <div class="grid grid-cols-2 gap-2">
                    <button
                        wire:click="setLastWeek"
                        class="px-4 py-2 text-sm font-medium rounded-md border border-gray-200 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Last Week
                    </button>
                    <button
                        wire:click="setLastMonth"
                        class="px-4 py-2 text-sm font-medium rounded-md border border-gray-200 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Last Month
                    </button>
                </div>
            </div>
            <div class="space-y-2">
                <h4 class="text-sm font-medium text-gray-700">Current</h4>
                <div class="grid grid-cols-2 gap-2">
                    <button
                        wire:click="setCurrentWeek"
                        class="px-4 py-2 text-sm font-medium rounded-md border border-gray-200 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        This Week
                    </button>
                    <button
                        wire:click="setCurrentMonth"
                        class="px-4 py-2 text-sm font-medium rounded-md border border-gray-200 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        This Month
                    </button>
                </div>
            </div>
        </div>

        <!-- Date Inputs -->
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                <input
                    type="date"
                    wire:model="startDate"
                    value="{{ $startDate }}"
                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                <input
                    type="date"
                    wire:model="endDate"
                    value="{{ $endDate }}"
                    min="{{ $startDate }}"
                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex space-x-3">
            <button
                wire:click="applyDateFilter"
                class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200">
                Apply Filter
            </button>
            <button
                wire:click="clearDateFilter"
                class="flex-1 bg-white text-gray-700 px-4 py-2 rounded-md border border-gray-300 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors duration-200">
                Clear
            </button>
        </div>
    </div>
</div>