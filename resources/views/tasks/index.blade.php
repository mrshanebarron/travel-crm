<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tasks') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Task</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Booking</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned To</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($tasks as $task)
                                <tr class="{{ $task->due_date && $task->due_date->isPast() ? 'bg-red-50' : '' }}">
                                    <td class="px-4 py-4">
                                        <div class="font-medium text-gray-900">{{ $task->name }}</div>
                                        @if($task->description)
                                            <div class="text-sm text-gray-500">{{ Str::limit($task->description, 50) }}</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <a href="{{ route('bookings.show', $task->booking) }}" class="text-indigo-600 hover:text-indigo-900">
                                            {{ $task->booking->booking_number }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm">
                                        @if($task->due_date)
                                            <span class="{{ $task->due_date->isPast() ? 'text-red-600 font-medium' : 'text-gray-900' }}">
                                                {{ $task->due_date->format('M j, Y') }}
                                            </span>
                                            @if($task->due_date->isPast())
                                                <span class="text-xs text-red-600">(overdue)</span>
                                            @endif
                                        @else
                                            <span class="text-gray-400">No due date</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            {{ $task->status === 'pending' ? 'bg-gray-100 text-gray-800' : '' }}
                                            {{ $task->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : '' }}
                                            {{ $task->status === 'completed' ? 'bg-green-100 text-green-800' : '' }}">
                                            {{ str_replace('_', ' ', ucfirst($task->status)) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $task->assignedTo?->name ?? '-' }}
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <form method="POST" action="{{ route('tasks.update', $task) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="name" value="{{ $task->name }}">
                                            <input type="hidden" name="status" value="completed">
                                            <button type="submit" class="text-green-600 hover:text-green-900 mr-3">Complete</button>
                                        </form>
                                        <form method="POST" action="{{ route('tasks.destroy', $task) }}" class="inline" onsubmit="return confirm('Delete this task?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                        No pending tasks
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $tasks->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
