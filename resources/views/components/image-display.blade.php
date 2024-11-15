<!-- resources/views/components/image-display.blade.php -->
<div
    class="flex justify-center divide-x-2 divide-gray-400 border-2 border-gray-400 rounded-lg shadow-lg overflow-hidden">
    @foreach (['original' => 'Оригинал', 'mask' => 'Маска', 'result' => 'Результат'] as $key => $label)
        @if (!empty($files[$key]))
            <img
                src="{{ $files[$key] }}"
                alt="{{ $label }}"
                class="w-64 h-auto object-cover "
            >
        @endif
    @endforeach
</div>
