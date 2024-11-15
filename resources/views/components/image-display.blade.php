<!-- resources/views/components/image-display.blade.php -->
<div class="flex flex-wrap justify-center gap-8">
    {{-- 'mask' => 'Mask', --}}
    @foreach (['original' => 'Original', 'result' => 'Result'] as $key => $label)
        <div class="w-64 p-4 bg-slate-300 rounded-lg shadow-lg">
            <img
                src="{{ $files[$key] ?? '' }}"
                alt="{{ $label }}"
                class="w-full h-64 object-cover rounded-md"
            >
            <div class="text-center text-gray-700 mt-2 font-semibold">{{ $label }}</div>
        </div>
    @endforeach
</div>
