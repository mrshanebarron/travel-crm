<div>
    <!-- Header -->
    <div class="px-6 py-4 border-b border-slate-200 flex justify-between items-center">
        <h2 class="text-lg font-semibold text-slate-900">Communication History</h2>
        <x-action-button type="add" size="sm" label="Add Note" wire:click="openAddModal" />
    </div>

    <!-- Notes List -->
    <div class="p-6">
        @if($notes->count() > 0)
            <div class="space-y-4">
                @foreach($notes as $note)
                    <div class="flex gap-4" wire:key="note-{{ $note->id }}">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 {{ $note->getTypeColor() }} rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $note->getTypeIcon() }}" />
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1 bg-slate-50 rounded-xl p-4">
                            <div class="flex justify-between items-start">
                                <div>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $note->getTypeColor() }}">
                                        {{ $note->getTypeLabel() }}
                                    </span>
                                    <span class="text-xs text-slate-500 ml-2">{{ $note->contacted_at->format('M j, Y g:i A') }}</span>
                                </div>
                                <div class="flex items-center gap-1">
                                    <x-action-button type="edit" size="xs" :icon="false" wire:click="openEditModal({{ $note->id }})" />
                                    <x-action-button type="delete" size="xs" :icon="false" wire:click="deleteNote({{ $note->id }})" wire:confirm="Delete this note?" />
                                </div>
                            </div>
                            <div class="mt-2 text-slate-700 whitespace-pre-line">{{ $note->content }}</div>
                            <div class="mt-2 text-xs text-slate-400">
                                Added by {{ $note->creator->name ?? 'Unknown' }} on {{ $note->created_at->format('M j, Y') }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8 text-slate-500">
                <svg class="w-12 h-12 mx-auto mb-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                </svg>
                <p>No communication history</p>
                <p class="text-sm">Click "Add Note" to start tracking interactions</p>
            </div>
        @endif
    </div>

    <!-- Add Note Modal -->
    @if($showAddModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" wire:click.self="closeAddModal">
            <div class="bg-white rounded-xl p-6 w-full max-w-lg">
                <h3 class="text-lg font-semibold text-slate-900 mb-4">Add Communication Note</h3>
                <form wire:submit="addNote">
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Type *</label>
                                <select wire:model="noteType" required class="w-full rounded-lg border-slate-300 focus:border-orange-500 focus:ring-orange-500">
                                    @foreach($noteTypes as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Contact Date/Time</label>
                                <input type="datetime-local" wire:model="contactedAt"
                                    class="w-full rounded-lg border-slate-300 focus:border-orange-500 focus:ring-orange-500">
                            </div>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Notes *</label>
                            <textarea wire:model="noteContent" rows="4" required
                                class="w-full rounded-lg border-slate-300 focus:border-orange-500 focus:ring-orange-500"
                                placeholder="Describe the communication or interaction..."></textarea>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 mt-6">
                        <x-action-button type="cancel" wire:click="closeAddModal" />
                        <x-action-button type="add" label="Add Note" :submit="true" />
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Edit Note Modal -->
    @if($showEditModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" wire:click.self="closeEditModal">
            <div class="bg-white rounded-xl p-6 w-full max-w-lg">
                <h3 class="text-lg font-semibold text-slate-900 mb-4">Edit Note</h3>
                <form wire:submit="updateNote">
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Type *</label>
                                <select wire:model="editNoteType" required class="w-full rounded-lg border-slate-300 focus:border-orange-500 focus:ring-orange-500">
                                    @foreach($noteTypes as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Contact Date/Time</label>
                                <input type="datetime-local" wire:model="editContactedAt"
                                    class="w-full rounded-lg border-slate-300 focus:border-orange-500 focus:ring-orange-500">
                            </div>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Notes *</label>
                            <textarea wire:model="editNoteContent" rows="4" required
                                class="w-full rounded-lg border-slate-300 focus:border-orange-500 focus:ring-orange-500"
                                placeholder="Describe the communication or interaction..."></textarea>
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
</div>
