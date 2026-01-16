<x-app-layout>
    <!-- Page Title -->
    <div class="mb-6 sm:mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900">Transfer Requests</h1>
            <p class="text-slate-500 text-sm sm:text-base">Manage fund transfer requests to Kenya</p>
        </div>
        <x-action-button type="create" label="New Transfer" :href="route('transfers.create')" class="w-full sm:w-auto justify-center" />
    </div>

    <livewire:transfers-list />
</x-app-layout>
