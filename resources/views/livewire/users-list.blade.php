<div>
    <!-- Role Legend -->
    <div class="bg-white rounded-xl border border-slate-200 p-4 mb-6">
        <div class="flex flex-wrap items-center gap-6">
            <div class="flex items-center gap-2">
                <span class="badge badge-danger">Super Admin</span>
                <span class="text-sm text-slate-500">Full access - manage users, edit locked rates</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="badge badge-warning">Admin</span>
                <span class="text-sm text-slate-500">Manage bookings, transfers, clients</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="badge badge-info">User</span>
                <span class="text-sm text-slate-500">View only access</span>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Joined</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr wire:key="user-{{ $user->id }}">
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center">
                                    <span class="text-orange-600 font-semibold">{{ substr($user->name, 0, 1) }}</span>
                                </div>
                                <div>
                                    <div class="font-medium text-slate-900">{{ $user->name }}</div>
                                    @if($user->id === auth()->id())
                                        <span class="text-xs text-orange-600">(You)</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @if($user->hasRole('super_admin'))
                                <span class="badge badge-danger">Super Admin</span>
                            @elseif($user->hasRole('admin'))
                                <span class="badge badge-warning">Admin</span>
                            @else
                                <span class="badge badge-info">User</span>
                            @endif
                        </td>
                        <td>{{ $user->created_at->format('M d, Y') }}</td>
                        <td>
                            <div class="flex items-center gap-2">
                                <x-action-button type="edit" size="xs" wire:click="openEditModal({{ $user->id }})" />
                                @if($user->id !== auth()->id())
                                    <x-action-button type="delete" size="xs" wire:click="deleteUser({{ $user->id }})" wire:confirm="Are you sure you want to delete this user?" />
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-12 text-center text-slate-500">
                            No users found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Add User Modal -->
    @if($showAddModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" wire:click.self="closeAddModal">
            <div class="bg-white rounded-xl p-6 w-full max-w-md mx-4">
                <h3 class="text-lg font-semibold text-slate-900 mb-4">Add User</h3>
                <form wire:submit="createUser">
                    <div class="space-y-4">
                        <div>
                            <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Name</label>
                            <input type="text" wire:model="addName" class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500" required>
                            @error('addName') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Email</label>
                            <input type="email" wire:model="addEmail" class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500" required>
                            @error('addEmail') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Password</label>
                            <input type="password" wire:model="addPassword" class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500" required>
                            @error('addPassword') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Confirm Password</label>
                            <input type="password" wire:model="addPasswordConfirmation" class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500" required>
                            @error('addPasswordConfirmation') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Role</label>
                            <select wire:model="addRole" class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500" required>
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}">{{ ucwords(str_replace('_', ' ', $role->name)) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 mt-6">
                        <x-action-button type="cancel" wire:click="closeAddModal" />
                        <x-action-button type="adduser" :submit="true" />
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Edit User Modal -->
    @if($showEditModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" wire:click.self="closeEditModal">
            <div class="bg-white rounded-xl p-6 w-full max-w-md mx-4">
                <h3 class="text-lg font-semibold text-slate-900 mb-4">Edit User</h3>
                <form wire:submit="updateUser">
                    <div class="space-y-4">
                        <div>
                            <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Name</label>
                            <input type="text" wire:model="editName" class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500" required>
                            @error('editName') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Email</label>
                            <input type="email" wire:model="editEmail" class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500" required>
                            @error('editEmail') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">New Password (leave blank to keep current)</label>
                            <input type="password" wire:model="editPassword" class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500">
                            @error('editPassword') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Confirm New Password</label>
                            <input type="password" wire:model="editPasswordConfirmation" class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500">
                            @error('editPasswordConfirmation') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Role</label>
                            <select wire:model="editRole" class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500" required>
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}">{{ ucwords(str_replace('_', ' ', $role->name)) }}</option>
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
</div>
