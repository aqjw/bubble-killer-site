<x-guest-layout additionalClasses="sm:!max-w-[80%] !bg-transparent !shadow-none">
    <div class="container mx-auto  px-8">
        @if ($task->multiple)
            <div class="flex justify-between items-center mb-8 gap-6 p-4 bg-white rounded-lg shadow-lg">
                <h1 class="text-3xl font-bold text-gray-900">{{ $task->original_filename }}</h1>
                <div>
                    <span class="text-xs font-semibold text-gray-500 italic bg-gray-100 px-2 py-1 rounded-lg">ID:
                        {{ $task->id }}</span>
                    <div class="text-xs text-gray-500 italic mt-2">
                        Submitted on: {{ $task->created_at->format('F j, Y, g:i a') }}
                    </div>
                </div>
            </div>

            <div class="text-lg font-bold text-gray-700 mb-4">Subtasks</div>
            @if ($task->subtasks->isEmpty())
                <div class="text-sm font-bold text-gray-700 mb-4">processing..</div>
            @endif

            @foreach ($task->subtasks as $subtask)
                <x-cards.task-card :task="$subtask" />
            @endforeach
        @else
            @if ($task->parentTask)
                <div class="inline-block">
                    <a
                        href="{{ route('task', $task->parentTask) }}"
                        class="flex gap-1 items-center p-2 pr-4 mb-2 hover:bg-black/5 rounded-lg"
                    >
                        <svg
                            class="w-6 h-6"
                            viewBox="0 0 24 24"
                            fill="none"
                        >
                            <path
                                d="M15 20L7 12L15 4"
                                stroke="#000000"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                        </svg>
                        Back
                    </a>
                </div>
            @endif
            <x-cards.task-card :task="$task" />
        @endif
    </div>
</x-guest-layout>
