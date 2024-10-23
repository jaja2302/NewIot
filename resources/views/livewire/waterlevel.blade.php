<div>
    <div class="flex space-x-4 mb-4">
        <div class="flex-1">
            <label for="date" class="block text-sm font-medium text-gray-700">Date</label>
            <input type="date" id="date" wire:model="selectedDate" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
        </div>
        <div class="flex-1">
            <label for="wilayah" class="block text-sm font-medium text-gray-700">Wilayah</label>
            <select id="wilayah" wire:model="selectedWilayah" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" wire:change="updateSelectedStation($event.target.value)">
                <option value="">Select Wilayah</option>
                @foreach($wilayah as $wil)
                <option value="{{ $wil->id }}">{{ $wil->nama }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex-1">
            <label for="station" class="block text-sm font-medium text-gray-700">Station</label>
            <select id="station" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                <option value="">Select Station</option>
                @foreach($stations as $station)
                <option value="{{ $station->id }}">{{ $station->location }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>