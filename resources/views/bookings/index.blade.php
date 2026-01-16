<x-app-layout>
    <!-- Page Title -->
    <div class="mb-6 sm:mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900">Bookings</h1>
            <p class="text-slate-500 text-sm sm:text-base">Manage all safari bookings</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-2">
            <x-action-button type="import" label="Import from Safari Office" @click="$dispatch('open-import-modal')" class="w-full sm:w-auto justify-center" />
            <x-action-button type="create" label="New Booking" :href="route('bookings.create')" class="w-full sm:w-auto justify-center" />
        </div>
    </div>

    <!-- Import from Safari Office Modal -->
    <div x-data="{ open: false }"
         x-show="open"
         x-cloak
         x-on:open-import-modal.window="open = true"
         x-on:keydown.escape.window="open = false"
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div @click.away="open = false" class="bg-white rounded-xl shadow-xl w-full max-w-lg mx-4 overflow-hidden">
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
                <form action="{{ route('bookings.create-from-pdf') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-slate-700 mb-2">Safari Office PDF</label>
                        <input type="file" name="pdf" accept=".pdf" required
                            class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100">
                        <p class="mt-1 text-xs text-slate-500">Upload the PDF proposal/quote exported from Safari Office</p>
                    </div>
                    <div class="flex justify-end gap-3">
                        <x-action-button type="cancel" @click="open = false" />
                        <x-action-button type="create" label="Create Booking" :submit="true" />
                    </div>
                </form>
            </div>
        </div>
    </div>

    <livewire:bookings-list />
</x-app-layout>
