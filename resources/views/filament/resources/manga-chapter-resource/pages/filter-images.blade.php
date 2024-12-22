<x-filament-panels::page>
    <div x-load-js="[@js(\Filament\Support\Facades\FilamentAsset::getScriptSrc('canvas-split-tool'))]">
        <div class="mb-4 flex items-center justify-between">
            <div class="flex gap-2">
                {{ $this->markDoneAction }}
            </div>
            <x-filament::badge
                color="info"
                class="!text-lg"
            >
                Total images: {{ count($this->images) }}
            </x-filament::badge>
        </div>

        @foreach ($this->images as $key => $image)
            <div
                x-init="new CanvasSplitTool({{ $image->id }}, $wire)"
                wire:key="{{ $key }}"
            >
                @include('components.filter-image')
            </div>
        @endforeach
    </div>
</x-filament-panels::page>
