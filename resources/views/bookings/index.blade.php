<x-app-layout>
    <!-- Import from Safari Office Modal -->
    <div x-data="{ open: false, submitting: false }"
         x-show="open"
         x-cloak
         x-on:open-import-modal.window="open = true"
         x-on:keydown.escape.window="open = false"
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div @click.away="!submitting && (open = false)" class="bg-white rounded-xl shadow-xl w-full max-w-lg mx-4 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-slate-900">Import from Safari Office</h3>
                <button @click="open = false" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="p-6">
                <p class="text-slate-600 mb-4">Upload a Safari Office PDF to automatically create a booking with travelers, itinerary, and rates.</p>
                <form action="{{ route('bookings.create-from-pdf') }}" method="POST" enctype="multipart/form-data" 
                      @submit="submitting = true" x-data="{ uploading: false }">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-slate-700 mb-2">Safari Office PDF</label>
                        <input type="file" name="pdf" accept=".pdf" required
                            class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100">
                        <p class="mt-1 text-xs text-slate-500">Upload the PDF proposal/quote exported from Safari Office</p>
                    </div>
                    <div class="flex justify-end gap-3">
                        <x-action-button type="cancel" @click="open = false" x-show="!submitting" />
                        <button type="submit" :disabled="submitting"
                                class="inline-flex items-center font-medium rounded border transition-colors text-sm py-1.5 px-3 gap-1.5 bg-orange-600 border-orange-600 text-white hover:bg-orange-700 disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg x-show="submitting" class="animate-spin w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4V2m0 20v-2m8-8h2M2 12h2m15.364-6.364L19.778 4.222M4.222 19.778l1.414-1.414M19.778 19.778l-1.414-1.414M4.222 4.222l1.414 1.414" />
                            </svg>
                            <svg x-show="!submitting" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            <span x-text="submitting ? 'Creating...' : 'Create Booking'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <livewire:bookings-list />
</x-app-layout>
