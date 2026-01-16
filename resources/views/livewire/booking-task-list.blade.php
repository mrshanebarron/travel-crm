<div>
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-lg font-semibold text-slate-900">Master Checklist</h2>
        <x-action-button type="add" label="Add Task" wire:click="openAddModal" />
    </div>

    <!-- All Tasks Table -->
    <div class="border border-slate-200 rounded-xl overflow-hidden">
        <div class="bg-slate-50 px-6 py-3 border-b border-slate-200">
            <div class="flex justify-between items-center">
                <h3 class="font-medium text-slate-900">All Tasks ({{ $tasks->count() }})</h3>
                <div class="text-sm text-slate-500">
                    <span class="text-green-600 font-medium">{{ $completedCount }}</span> completed,
                    <span class="text-amber-600 font-medium">{{ $pendingCount }}</span> pending
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-2 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider w-8"></th>
                        <th class="px-2 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider" style="min-width: 320px;">Task</th>
                        <th class="px-2 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider whitespace-nowrap">Assigned To</th>
                        <th class="px-2 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider whitespace-nowrap">Assigned</th>
                        <th class="px-2 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider whitespace-nowrap">Completed</th>
                        <th class="px-2 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider w-full">Notes</th>
                        <th class="px-2 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider w-16"></th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    @forelse($tasks as $task)
                        <tr class="{{ $task->status === 'completed' ? 'bg-green-50/50' : '' }} hover:bg-slate-50" wire:key="task-{{ $task->id }}">
                            <td class="px-2 py-2">
                                <button wire:click="toggleTask({{ $task->id }})" class="{{ $task->status === 'completed' ? 'w-5 h-5 bg-green-500 rounded flex items-center justify-center hover:bg-green-600' : 'w-5 h-5 border-2 border-slate-300 rounded hover:border-orange-500 hover:bg-orange-50' }} transition-colors" title="{{ $task->status === 'completed' ? 'Mark incomplete' : 'Mark complete' }}">
                                    @if($task->status === 'completed')
                                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                        </svg>
                                    @endif
                                </button>
                            </td>
                            <td class="px-2 py-2" style="min-width: 320px;">
                                <span class="{{ $task->status === 'completed' ? 'text-slate-500 line-through' : 'text-slate-900 font-medium' }} text-sm">
                                    {{ $task->name }}
                                </span>
                            </td>
                            <td class="px-2 py-2 text-xs whitespace-nowrap">
                                <button wire:click="openAssignModal({{ $task->id }})" class="text-xs {{ $task->assignedTo ? 'bg-slate-100 text-slate-700 hover:bg-slate-200' : 'bg-orange-100 text-orange-700 hover:bg-orange-200' }} px-2 py-1 rounded font-medium">
                                    {{ $task->assignedTo ? $task->assignedTo->name : 'Assign' }}
                                </button>
                            </td>
                            <td class="px-2 py-2 text-xs text-slate-500 whitespace-nowrap">
                                {{ $task->assigned_at ? $task->assigned_at->format('n/j') : '-' }}
                            </td>
                            <td class="px-2 py-2 text-xs text-slate-500 whitespace-nowrap">
                                @if($task->status === 'completed' && $task->completed_at)
                                    {{ $task->completed_at->format('n/j') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-2 py-2 text-sm text-slate-600">
                                {{ $task->description ?: '' }}
                            </td>
                            <td class="px-2 py-2 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <x-action-button type="edit" size="xs" wire:click="openEditModal({{ $task->id }})" />
                                    <x-action-button type="delete" size="xs" wire:click="deleteTask({{ $task->id }})" wire:confirm="Delete this task?" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-6 text-center text-slate-500">
                                No tasks yet. Click "Add Task" to create one.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Task Modal -->
    @if($showAddModal)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" wire:click.self="closeAddModal">
        <div class="bg-white rounded-xl p-6 w-full max-w-md mx-4">
            <h3 class="text-lg font-semibold text-slate-900 mb-4">Add Task</h3>
            <form wire:submit="addTask">
                <div class="space-y-4">
                    <div>
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Task Name</label>
                        <input type="text" wire:model="taskName" class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500" required>
                        @error('taskName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Description (Optional)</label>
                        <textarea wire:model="taskDescription" rows="2" class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500"></textarea>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Due Date (Optional)</label>
                        <input type="date" wire:model="taskDueDate" class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500">
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Assign To (Optional)</label>
                        <select wire:model="taskAssignedTo" class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500">
                            <option value="">Unassigned</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <x-action-button type="cancel" wire:click="closeAddModal" />
                    <x-action-button type="add" label="Add Task" :submit="true" />
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Edit Task Modal -->
    @if($showEditModal)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" wire:click.self="closeEditModal">
        <div class="bg-white rounded-xl p-6 w-full max-w-md mx-4">
            <h3 class="text-lg font-semibold text-slate-900 mb-4">Edit Task</h3>
            <form wire:submit="updateTask">
                <div class="space-y-4">
                    <div>
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Task Name</label>
                        <input type="text" wire:model="editTaskName" class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500" required>
                        @error('editTaskName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Due Date (Optional)</label>
                        <input type="date" wire:model="editTaskDueDate" class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500">
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Assign To</label>
                        <select wire:model="editTaskAssignedTo" class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500">
                            <option value="">Unassigned</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <x-action-button type="cancel" wire:click="closeEditModal" />
                    <x-action-button type="save" label="Save Changes" :submit="true" />
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Assign Task Modal -->
    @if($showAssignModal)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" wire:click.self="closeAssignModal">
        <div class="bg-white rounded-xl p-6 w-full max-w-md mx-4">
            <h3 class="text-lg font-semibold text-slate-900 mb-4">Assign Task</h3>
            <form wire:submit="assignTask">
                <div class="space-y-4">
                    <div>
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Assign To</label>
                        <select wire:model="assignTaskUserId" class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500">
                            <option value="">Unassigned</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <x-action-button type="cancel" wire:click="closeAssignModal" />
                    <x-action-button type="save" :submit="true" />
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
