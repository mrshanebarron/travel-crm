<x-app-layout>
    <!-- Page Title -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">User Management</h1>
            <p class="text-slate-500">Manage users and their roles</p>
        </div>
        <x-action-button type="adduser" label="Add User" onclick="Livewire.dispatch('openAddModal')" />
    </div>

    <livewire:users-list />
</x-app-layout>
