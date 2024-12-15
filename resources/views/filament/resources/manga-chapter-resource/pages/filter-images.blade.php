<x-filament-panels::page>
    <form wire:submit.prevent="submit">
        <div class="mb-4 flex items-center justify-between">
            <div class="flex gap-2">
                {{ $this->submitAction }}
                {{ $this->markDoneAction }}
            </div>
            <x-filament::badge
                color="info"
                class="!text-lg"
            >
                Total images: {{ count($this->images) }}
            </x-filament::badge>
        </div>

        @foreach ($this->images as $image)
            @php($will_delete = $this->willDeleteImage($image->id))

            <div
                class="flex border-2 border-black dark:border-white mb-4 {{ $will_delete ? 'bg-red-500 !border-red-500' : '' }}">
                <img
                    src="{{ $image->getUrl() }}"
                    alt="{{ $image->name }}"
                    class="border-r-2 border-black dark:border-white w-1/2 {{ $will_delete ? '!border-red-500' : '' }}"
                >
                <div class="w-1/2 p-2">
                    <div
                        class="sticky top-[4.5rem] p-4 flex justify-between items-center bg-slate-100 dark:bg-slate-800 rounded-md">
                        <div class="flex items-center space-x-2">
                            @if (!$will_delete)
                                @foreach (['bubble' => 'Bubble/Sfx', 'crop' => 'Crop'] as $key => $label)
                                    <label class="cursor-pointer">
                                        <x-filament::input.checkbox
                                            wire:model.change="data.{{ $image->id }}.{{ $key }}"
                                            class="sr-only peer"
                                        />
                                        <div
                                            class="px-4 py-2 rounded-lg font-medium text-center
                                            peer-checked:bg-blue-500 peer-checked:text-white
                                            bg-gray-200 text-gray-700 transition ">
                                            {{ $label }}
                                        </div>
                                    </label>
                                @endforeach
                            @else
                                <span>Будет удалено.</span>
                            @endif

                            <div class="font-semibold">
                                # {{ $image->name }}
                            </div>
                        </div>

                        <label class="cursor-pointer">
                            <x-filament::input.checkbox
                                wire:model.change="data.{{ $image->id }}.delete"
                                class="sr-only peer"
                            />
                            <div
                                class="px-4 py-2 rounded-lg font-medium text-center
                                peer-checked:bg-red-500 peer-checked:text-white
                                bg-gray-200 text-gray-700 transition capitalize">
                                Удалить
                            </div>
                        </label>
                    </div>
                </div>
            </div>
        @endforeach
    </form>
</x-filament-panels::page>
