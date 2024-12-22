<div class="flex border-2 border-black dark:border-white mb-4 select-none">
    <div class="w-1/2 border-r-2 border-black dark:border-white relative cursor-none">
        <img
            src="{{ $image->getUrl() }}?u={{ md5($image->updated_at) }}"
            alt="{{ $image->name }}"
            class="w-full"
            id="image-{{ $image->id }}"
        >

        <canvas
            id="canvas-{{ $image->id }}"
            class="absolute top-0 left-0 w-full"
        ></canvas>
    </div>

    <div class="w-1/2 p-2">
        <div class="sticky top-[4.5rem] p-4 bg-slate-100 dark:bg-slate-800 rounded-md">
            <div class="flex justify-between">
                <div class="flex items-center space-x-2">
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
                </div>
                {{ ($this->deleteImage)(['image_id' => $image->id]) }}
            </div>

            <hr class="my-2">

            <div>
                <label>
                    <input
                        type="checkbox"
                        id="split-toggle-{{ $image->id }}"
                    /> Split Mode
                </label>
            </div>
            <div
                id="angle-container-{{ $image->id }}"
                style="display:none"
            >
                <label>
                    Angle: <span id="angle-value-{{ $image->id }}">0</span>
                    <input
                        type="range"
                        id="angle-input-{{ $image->id }}"
                        min="0"
                        max="190"
                        value="0"
                        class="w-full"
                    />
                </label>
            </div>

            <hr class="my-2">

            <div class="flex items-center gap-2">
                <div class="font-semibold">
                    # {{ $image->name }}
                </div>
            </div>
        </div>
    </div>
</div>
