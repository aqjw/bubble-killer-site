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
                Total images: {{ count($this->images) }}
            </x-filament::badge>
        </div>

        @foreach ($this->images as $image)
            <div class="flex border-2 border-black dark:border-white mb-4 select-none">
                <div class="w-4/6 border-r-2 border-black dark:border-white relative cursor-none">
                    <img
                        src="{{ $image->getUrl() }}"
                        alt="{{ $image->name }}"
                        class="w-full"
                        id="image-{{ $loop->index }}"
                        data-mask-url="{{ $this->getMaskUrl($image->name) }}"
                    >

                    <canvas
                        id="canvas-{{ $loop->index }}"
                        class="absolute top-0 left-0 w-full"
                    ></canvas>
                    <div
                        id="custom-cursor-{{ $loop->index }}"
                        class="absolute pointer-events-none z-[1000] rounded-full"
                        style="display: none;"
                    ></div>
                </div>

                <div class="w-2/6 p-2">
                    <div class="sticky top-[4.5rem] p-4 bg-slate-100 dark:bg-slate-800 rounded-md">
                        <div class="rounded-md overflow-hidden inline-block">
                            <label>
                                <input
                                    type="radio"
                                    name="tool-mode-{{ $loop->index }}"
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
                                    name="tool-mode-{{ $loop->index }}"
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
                                    id="brush-size-{{ $loop->index }}"
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
                                    id="opacity-{{ $loop->index }}"
                                    min="1"
                                    max="100"
                                    value="70"
                                    class="w-full cursor-pointer accent-green-500"
                                />
                            </label>
                        </div>

                        <hr class="my-2">

                        <div class="flex items-center gap-2">
                            <x-filament::button
                                id="save-btn-{{ $loop->index }}"
                                data-mid="{{ $image->id }}"
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
        @endforeach
    </form>
</x-filament-panels::page>

@script
    <script>
        const canvases = document.querySelectorAll('canvas');
        canvases.forEach((canvas, index) => {
            const toolModes = document.querySelectorAll(`[name="tool-mode-${index}"]`);
            const brushSizeRange = document.getElementById(`brush-size-${index}`);
            const maskOpacityRange = document.getElementById(`opacity-${index}`);
            const saveMaskBtn = document.getElementById(`save-btn-${index}`);

            // Инициализировать начальные значения
            canvas.isEraser = false;
            canvas.brushSize = parseInt(brushSizeRange.value, 10);
            canvas.customCursor = document.getElementById(`custom-cursor-${index}`);
            canvas.style.opacity = (+maskOpacityRange.value) / 100

            // Добавить обработчики событий для изменения режима и размера кисти
            toolModes.forEach((tool) => {
                tool.addEventListener('change', (e) => {
                    canvas.isEraser = e.target.value === 'eraser';
                    init_canvas_cursor(canvas);
                });
            });

            brushSizeRange.addEventListener('input', (e) => {
                canvas.brushSize = parseInt(e.target.value, 10);
                init_canvas_cursor(canvas)
            });

            maskOpacityRange.addEventListener('input', (e) => {
                canvas.style.opacity = (+e.target.value) / 100
            });

            saveMaskBtn.addEventListener('click', (e) => {
                const base64 = canvas.toDataURL()
                $wire.saveMask(+saveMaskBtn.dataset.mid, base64)
            });

            init_canvas_cursor(canvas)
            init_canvas(canvas, index);
        });

        const get_coords = (e, canvas) => {
            const rect = canvas.getBoundingClientRect();
            const scaleX = canvas.width / rect.width;
            const scaleY = canvas.height / rect.height;

            return {
                x: (e.clientX - rect.left) * scaleX,
                y: (e.clientY - rect.top) * scaleY,
            };
        }

        function init_canvas_cursor(canvas) {
            canvas.customCursor.style.width = `${canvas.brushSize}px`;
            canvas.customCursor.style.height = `${canvas.brushSize}px`;
            canvas.customCursor.style.background = canvas.isEraser ? 'black' : 'white';
        }

        function init_canvas(canvas, index) {
            const image = document.getElementById(`image-${index}`);
            const maskUrl = image.getAttribute('data-mask-url');

            const ctx = canvas.getContext('2d');
            const img = new Image();
            const mask = new Image();

            // Загрузка изображения
            img.src = image.src;
            img.onload = () => {
                // Оригинальные размеры изображения
                const originalWidth = img.width;
                const originalHeight = img.height;

                // Установка реальных размеров canvas
                canvas.width = originalWidth;
                canvas.height = originalHeight;

                // Визуальные размеры (зависит от CSS)
                const set_canvas_size = () => {
                    const scaledWidth = image.clientWidth; // Ширина в пикселях (в CSS)
                    const scaledHeight = (originalHeight / originalWidth) * scaledWidth;
                    canvas.style.width = `${scaledWidth}px`;
                    canvas.style.height = `${scaledHeight}px`;
                }
                set_canvas_size()

                // Отображение изображения
                ctx.drawImage(img, 0, 0);

                // Полупрозрачная маска
                mask.src = maskUrl;
                mask.onload = () => {
                    ctx.drawImage(mask, 0, 0, originalWidth, originalHeight);
                };

                window.addEventListener('resize', set_canvas_size);
            };

            let drawing = false;
            let lastX = 0;
            let lastY = 0;

            canvas.addEventListener('mouseenter', () => {
                canvas.customCursor.style.display = 'block';
            });

            canvas.addEventListener('mouseleave', () => {
                canvas.customCursor.style.display = 'none';
            });

            canvas.addEventListener('mousedown', (e) => {
                drawing = true;

                const {
                    x,
                    y
                } = get_coords(e, canvas);

                // Рисуем точку для клика
                ctx.fillStyle = canvas.isEraser ? 'black' : 'white';
                ctx.beginPath();
                ctx.arc(x, y, (canvas.brushSize / 2), 0, Math.PI * 2);
                ctx.fill();

                // Сохраняем начальную позицию для линий
                lastX = x;
                lastY = y;
            });

            document.addEventListener('mouseup', () => {
                drawing = false;
                ctx.beginPath(); // Сброс пути.
            });

            const moveCursor = (clientX, clientY, canvas) => {
                canvas.customCursor.style.display = 'block';

                const rect = canvas.getBoundingClientRect();
                const scaleX = canvas.width / rect.width; // Масштаб по ширине
                // const scaleY = canvas.height / rect.height; // Масштаб по высоте
                const brushSize = canvas.brushSize / scaleX; // Корректируем размер кисти

                // Устанавливаем позицию курсора с учётом масштаба
                const offsetX = clientX - rect.left - brushSize / 2;
                const offsetY = clientY - rect.top - brushSize / 2;

                canvas.customCursor.style.width = `${brushSize}px`;
                canvas.customCursor.style.height = `${brushSize}px`;
                canvas.customCursor.style.left = `${offsetX}px`;
                canvas.customCursor.style.top = `${offsetY}px`;
            };

            canvas.addEventListener('mousemove', (e) => {
                moveCursor(e.clientX, e.clientY, canvas);

                if (!drawing) return;

                const {
                    x,
                    y
                } = get_coords(e, canvas);

                ctx.lineWidth = canvas.brushSize;
                ctx.lineCap = 'round';
                ctx.strokeStyle = canvas.isEraser ? 'black' : 'white';

                ctx.beginPath(); // Начинаем новый путь.
                ctx.moveTo(lastX, lastY); // Начинаем с последней позиции.
                ctx.lineTo(x, y); // Соединяем с текущей позицией.
                ctx.stroke();

                // Обновляем последнюю позицию
                lastX = x;
                lastY = y;
            });
        }
    </script>
@endscript
