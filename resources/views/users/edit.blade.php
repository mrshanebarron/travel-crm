<x-app-layout>
    <div class="max-w-2xl mx-auto">
        <!-- Page Title -->
        <div class="mb-8">
            <a href="{{ route('users.index') }}" class="text-orange-600 hover:text-orange-800 text-sm flex items-center gap-1 mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Back to Team
            </a>
            <h1 class="text-2xl font-bold text-slate-900">Edit Team Member</h1>
            <p class="text-slate-500">Update {{ $user->name }}'s account details</p>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-xl border border-slate-200 p-6">
            <form method="POST" action="{{ route('users.update', $user) }}">
                @csrf
                @method('PUT')

                <div class="space-y-6">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-slate-700 mb-2">Full Name</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                            class="form-input w-full rounded-lg border-slate-200 focus:border-orange-500 focus:ring-orange-500">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-700 mb-2">Email Address</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                            class="form-input w-full rounded-lg border-slate-200 focus:border-orange-500 focus:ring-orange-500">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phone Number -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-slate-700 mb-2">WhatsApp Phone Number</label>
                        <input type="tel" id="phone" name="phone" value="{{ old('phone', $user->phone) }}"
                            class="form-input w-full rounded-lg border-slate-200 focus:border-orange-500 focus:ring-orange-500"
                            placeholder="+1234567890">
                        <p class="mt-1 text-xs text-slate-500">
                            International format for WhatsApp notifications. Leave blank to disable notifications.
                        </p>
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Role -->
                    <div>
                        <label for="role" class="block text-sm font-medium text-slate-700 mb-2">Role</label>
                        <select id="role" name="role" required
                            class="form-select w-full rounded-lg border-slate-200 focus:border-orange-500 focus:ring-orange-500"
                            {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                            @foreach($roles as $value => $label)
                                <option value="{{ $value }}" {{ old('role', $user->role) === $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @if($user->id === auth()->id())
                            <input type="hidden" name="role" value="{{ $user->role }}">
                            <p class="mt-1 text-xs text-amber-600">You cannot change your own role</p>
                        @else
                            <p class="mt-1 text-xs text-slate-500">
                                <strong>Admin:</strong> Full access including user management |
                                <strong>Manager:</strong> Create/edit all bookings & transfers |
                                <strong>Staff:</strong> View only, edit own bookings
                            </p>
                        @endif
                        @error('role')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <hr class="border-slate-200">

                    <div class="bg-slate-50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-slate-700 mb-3">Change Password (Optional)</h3>
                        <p class="text-xs text-slate-500 mb-4">Leave blank to keep the current password</p>

                        <!-- Password -->
                        <div class="mb-4">
                            <label for="password" class="block text-sm font-medium text-slate-700 mb-2">New Password</label>
                            <input type="password" id="password" name="password"
                                class="form-input w-full rounded-lg border-slate-200 focus:border-orange-500 focus:ring-orange-500"
                                placeholder="Minimum 8 characters">
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-2">Confirm New Password</label>
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                class="form-input w-full rounded-lg border-slate-200 focus:border-orange-500 focus:ring-orange-500"
                                placeholder="Re-enter password">
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-slate-200">
                    <x-action-button type="cancel" :href="route('users.index')" />
                    <x-action-button type="save" label="Save Changes" :submit="true" />
                </div>
            </form>
        </div>

        @if($user->id !== auth()->id())
            <!-- Danger Zone -->
            <div class="mt-8 bg-red-50 rounded-xl border border-red-200 p-6">
                <h3 class="text-lg font-semibold text-red-800 mb-2">Danger Zone</h3>
                <p class="text-sm text-red-600 mb-4">Permanently delete this team member's account. This action cannot be undone.</p>
                <form method="POST" action="{{ route('users.destroy', $user) }}" onsubmit="return confirm('Are you sure you want to delete this team member? This action cannot be undone.')">
                    @csrf
                    @method('DELETE')
                    <x-action-button type="delete" label="Delete Team Member" :submit="true" />
                </form>
            </div>
        @endif
    </div>
</x-app-layout>
