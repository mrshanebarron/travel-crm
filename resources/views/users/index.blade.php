<x-app-layout>
    <!-- Page Title -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">User Management</h1>
            <p class="text-slate-500">Manage users and their roles</p>
        </div>
        <button type="button" onclick="document.getElementById('add-user-modal').classList.remove('hidden')" class="btn btn-primary">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Add User
        </button>
    </div>

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
                    <tr>
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
                                <button type="button" onclick="openEditUserModal({{ $user->id }}, '{{ addslashes($user->name) }}', '{{ $user->email }}', '{{ $user->roles->first()?->name ?? 'user' }}')" class="btn btn-secondary text-sm py-2 px-3">
                                    Edit
                                </button>
                                @if($user->id !== auth()->id())
                                    <form method="POST" action="{{ route('users.destroy', $user) }}" onsubmit="return confirm('Are you sure you want to delete this user?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium">Delete</button>
                                    </form>
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
    <div id="add-user-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl p-6 w-full max-w-md mx-4">
            <h3 class="text-lg font-semibold text-slate-900 mb-4">Add User</h3>
            <form method="POST" action="{{ route('users.store') }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Name</label>
                        <input type="text" name="name" class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500" required>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Email</label>
                        <input type="email" name="email" class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500" required>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Password</label>
                        <input type="password" name="password" class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500" required>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500" required>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Role</label>
                        <select name="role" class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500" required>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}">{{ ucwords(str_replace('_', ' ', $role->name)) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="document.getElementById('add-user-modal').classList.add('hidden')" class="btn btn-secondary">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add User</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div id="edit-user-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl p-6 w-full max-w-md mx-4">
            <h3 class="text-lg font-semibold text-slate-900 mb-4">Edit User</h3>
            <form id="edit-user-form" method="POST">
                @csrf
                @method('PATCH')
                <div class="space-y-4">
                    <div>
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Name</label>
                        <input type="text" name="name" id="edit-user-name" class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500" required>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Email</label>
                        <input type="email" name="email" id="edit-user-email" class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500" required>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">New Password (leave blank to keep current)</label>
                        <input type="password" name="password" class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500">
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Confirm New Password</label>
                        <input type="password" name="password_confirmation" class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500">
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Role</label>
                        <select name="role" id="edit-user-role" class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500" required>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}">{{ ucwords(str_replace('_', ' ', $role->name)) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="document.getElementById('edit-user-modal').classList.add('hidden')" class="btn btn-secondary">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEditUserModal(userId, name, email, role) {
            document.getElementById('edit-user-form').action = `/users/${userId}`;
            document.getElementById('edit-user-name').value = name;
            document.getElementById('edit-user-email').value = email;
            document.getElementById('edit-user-role').value = role;
            document.getElementById('edit-user-modal').classList.remove('hidden');
        }

        // Close modals when clicking outside
        document.querySelectorAll('[id$="-modal"]').forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    this.classList.add('hidden');
                }
            });
        });
    </script>
</x-app-layout>
