<div>
    <form wire:submit="create">
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

    <x-filament-actions::modals />
</div>