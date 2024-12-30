<div
    x-load-js="[@js(\Filament\Support\Facades\FilamentAsset::getScriptSrc('canvas-draw-tool'))]"
    class="flex border-2 border-black dark:border-white mb-4 select-none"
>
    <div class="w-5/6 border-r-2 border-black dark:border-white relative cursor-none">
        <img
            src="{{ $image->getUrl() }}"
            alt="{{ $image->name }}"
            class="w-full"
            id="image-{{ $image->id }}"
            data-mask-url="{{ $mask->getUrl() }}?u={{ md5($mask->updated_at) }}"
        >

        <canvas
            id="canvas-{{ $image->id }}"
            class="absolute top-0 left-0 w-full"
        ></canvas>
        <div
            id="custom-cursor-{{ $image->id }}"
            class="absolute pointer-events-none z-[1000] rounded-full"
            style="display: none;"
        ></div>
    </div>

    <div class="w-1/6 p-2">
        <div class="sticky top-[4.5rem] p-4 bg-slate-100 dark:bg-slate-800 rounded-md">
            <div class="rounded-md overflow-hidden inline-block">
                <label>
                    <input
                        type="radio"
                        name="tool-mode-{{ $image->id }}"
                        value="brush"
                        checked
                        class="sr-only peer"
                    />
                    <span
                        class="px-4 py-2 bg-gray-200 text-gray-700 cursor-pointer peer-checked:bg-green-500 peer-checked:text-white hover:bg-gray-300 transition"
                    >
                        Кисть
                    </span>
                </label>
                <label>
                    <input
                        type="radio"
                        name="tool-mode-{{ $image->id }}"
                        value="eraser"
                        class="sr-only peer"
                    />
                    <span
                        class="px-4 py-2 bg-gray-200 text-gray-700 cursor-pointer peer-checked:bg-green-500 peer-checked:text-white hover:bg-gray-300 transition"
                    >
                        Ластик
                    </span>
                </label>
            </div>
            <div>
                <label>
                    <span class="font-semibold">Размер</span>
                    <input
                        type="range"
                        id="brush-size-{{ $image->id }}"
                        min="10"
                        max="100"
                        value="30"
                        class="w-full cursor-pointer accent-green-500"
                    />
                </label>
            </div>

            <hr class="my-2">

            <div>
                <label>
                    <span class="font-semibold">Прозрачность</span>
                    <input
                        type="range"
                        id="opacity-{{ $image->id }}"
                        min="1"
                        max="100"
                        value="50"
                        class="w-full cursor-pointer accent-green-500"
                    />
                </label>
            </div>

            <hr class="my-2">

            <div class="flex items-center gap-2">
                <x-filament::button
                    id="save-btn-{{ $image->id }}"
                    data-mid="{{ $image->id }}"
                    style="display: none;"
                >
                    Сохранить
                </x-filament::button>
                <div class="font-semibold">
                    # {{ $image->name }}
                </div>
            </div>
        </div>
    </div>
</div>

@script
    <script>
        new CanvasDrawTool({{ $image->id }}, $wire);
    </script>
@endscript
