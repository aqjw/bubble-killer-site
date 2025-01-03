<x-filament-panels::page>
    <form wire:submit.prevent="submit">
        <div class="mb-4 flex items-center justify-between">
            <div class="flex gap-2">
                {{ $this->markDoneAction }}
            </div>
            <x-filament::badge
                color="info"
                class="!text-lg"
            >
                Total images: {{ count($this->items) }}
            </x-filament::badge>
        </div>

        @foreach ($this->items as $key => $item)
            @livewire(\App\Livewire\MaskVerification::class, $item, key($key))
        @endforeach
    </form>
</x-filament-panels::page>
