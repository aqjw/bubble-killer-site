@props(['task'])

<div class="bg-white p-5 rounded-lg shadow-sm mb-8 flex gap-4 items-start">
    <div class="w-1/3">
        <div class="text-xs font-medium text-gray-400 bg-gray-100 px-3 py-1 rounded mb-3">
            ID: {{ $task->id }}
        </div>

        <table>
            <tr>
                <td class="text-sm text-gray-500 pr-4">файл</td>
                <td class="text-gray-700 truncate max-w-12">{{ $task->original_filename }}</td>
            </tr>
            <tr>
                <td class="text-sm text-gray-500 pr-4">Загружено в</td>
                <td class="text-gray-700">{{ $task->created_at->translatedFormat('F j, Y (H:i)') }}</td>
            </tr>
            <tr>
                <td class="text-sm text-gray-500 pr-4">Статус</td>
                <td class="text-gray-700">{{ $task->status->getName() }}</td>
            </tr>
            <tr>
                <td class="text-sm text-gray-500 pr-4">Mask Time</td>
                <td class="text-gray-700">
                    @if ($task->maskTimeSeconds)
                        {{ $task->maskTimeSeconds }} seconds
                    @else
                        –
                    @endif
                </td>
            </tr>
            <tr>
                <td class="text-sm text-gray-500 pr-4">Clean Time</td>
                <td class="text-gray-700">
                    @if ($task->cleanTimeSeconds)
                        {{ $task->cleanTimeSeconds }} seconds
                    @else
                        –
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <div class="w-2/3 flex justify-end">
        <x-image-display :files="$task->files" />
    </div>
</div>
