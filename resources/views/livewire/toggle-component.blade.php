<div>
    <div class="flex justify-end mt-6 mb-2">
        <x-filament::button
            wire:click="toggleComponent"
            color="success"
        >
            Toggle Mask Mode
        </x-filament::button>
    </div>

    @if ($component === 'mask')
        @livewire(\App\Livewire\MaskVerification::class, $item, key("mask-$key"))
    @else
        @livewire(\App\Livewire\ClearVerification::class, $item, key("clear-$key"))
    @endif
</div>
