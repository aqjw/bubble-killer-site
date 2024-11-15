<x-guest-layout additionalClasses="sm:!max-w-[80%] !bg-transparent !shadow-none">
    <div class="container mx-auto py-12 px-8">
        <!-- Заголовок и идентификатор задачи -->
        <div class="flex justify-between items-center mb-8 gap-6 p-4 bg-white rounded-lg shadow-lg">
            <h1 class="text-3xl font-bold text-gray-900">Task Status: {{ ucfirst($task->status) }}</h1>
            <div>

                <span class="text-xs font-semibold text-gray-500 italic bg-gray-100 px-2 py-1 rounded-lg">ID:
                    {{ $task->id }}</span>
                <div class="text-xs text-gray-500 italic mt-2">
                    Submitted on: {{ $task->created_at->format('F j, Y, g:i a') }}
                </div>
            </div>
        </div>


        {{-- <!-- Время обработки и прогресс -->
        @if ($task->processingTime)
            <div class="text-md text-gray-600 mb-4">AI Processing Time: {{ $task->processingTime }} seconds</div>
        @endif --}}

        <!-- Показать подзадачи или основную задачу -->
        @if ($task->multiple)
            <div class="text-lg font-bold text-gray-700 mb-4">Subtasks</div>
            @foreach ($task->subtasks as $subtask)
                <div class="mb-10 flex gap-6 items-start bg-white p-4 rounded-lg shadow-md">
                    <!-- Левый блок с информацией о подзадаче -->
                    <div class="w-1/3">
                        <div class="text-xs font-semibold text-gray-500 italic bg-gray-100 px-2 py-1 rounded-lg mb-2">
                            Subtask ID: {{ $subtask->id }}
                        </div>
                        <div class="text-xs text-gray-500 italic mb-2">
                            Submitted on: {{ $subtask->created_at->format('F j, Y, g:i a') }}
                        </div>
                        <div class="text-sm text-gray-600">Status: {{ ucfirst($subtask->status) }}</div>
                    </div>

                    <!-- Правый блок с изображением -->
                    <div class="w-2/3 flex justify-end">
                        <x-image-display :files="$subtask->files" />
                    </div>
                </div>
            @endforeach
        @else
            <div class="bg-gray-50 p-6 rounded-lg shadow-md mb-10 flex gap-6 items-start">
                <!-- Левый блок с информацией о задаче -->
                <div class="w-1/3">
                    <div class="text-xs font-semibold text-gray-500 italic bg-gray-100 px-2 py-1 rounded-lg mb-2">
                        Task ID: {{ $task->id }}
                    </div>
                    <div class="text-xs text-gray-500 italic mb-2">
                        Submitted on: {{ $task->created_at->format('F j, Y, g:i a') }}
                    </div>
                    <div class="text-sm text-gray-600">Status: {{ ucfirst($task->status) }}</div>
                </div>

                <!-- Правый блок с изображением -->
                <div class="w-2/3 flex justify-end">
                    <x-image-display :files="$task->files" />
                </div>
            </div>
        @endif
    </div>
</x-guest-layout>
