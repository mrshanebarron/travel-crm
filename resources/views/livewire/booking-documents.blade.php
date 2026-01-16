<div>
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-lg font-semibold text-slate-900">Documents</h2>
    </div>

    <!-- Upload Form -->
    <form wire:submit="uploadDocument" class="mb-6 p-4 bg-slate-50 rounded-xl">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div>
                <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Document Name</label>
                <input type="text" wire:model="name" placeholder="Document name" class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500" required>
                @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Category</label>
                <select wire:model="category" class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500" required>
                    <option value="lodge">Lodges/Camps</option>
                    <option value="arrival_departure_flight">Arrival/Departure Flight</option>
                    <option value="internal_flight">Internal Flights</option>
                    <option value="passport">Passport</option>
                    <option value="safari_guide_invoice">Safari Guide Invoices</option>
                    <option value="misc">Miscellaneous</option>
                </select>
            </div>
            <div>
                <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">File</label>
                <input type="file" wire:model="file" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100" required>
                @error('file') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                <div wire:loading wire:target="file" class="text-xs text-orange-600 mt-1">Uploading...</div>
            </div>
            <div>
                <x-action-button type="upload" label="Upload Document" :submit="true" class="w-full justify-center" wire:loading.attr="disabled" />
            </div>
        </div>
    </form>

    <!-- Documents Grid -->
    @if($documents->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($documents as $doc)
                <div class="border border-slate-200 rounded-xl p-4" wire:key="doc-{{ $doc->id }}">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div>
                                <a href="{{ route('documents.download', $doc) }}" class="font-medium text-orange-600 hover:text-orange-800">
                                    {{ $doc->name }}
                                </a>
                                <div class="text-xs text-slate-500">{{ $categoryLabels[$doc->category] ?? ucfirst($doc->category) }}</div>
                            </div>
                        </div>
                        <x-action-button type="delete" size="xs" :icon="false" wire:click="deleteDocument({{ $doc->id }})" wire:confirm="Delete this document?" />
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-12 text-slate-500">
            <svg class="mx-auto mb-4 text-slate-300" width="48" height="48" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <p>No documents uploaded yet</p>
        </div>
    @endif
</div>
