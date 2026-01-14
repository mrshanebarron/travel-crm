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
            <h1 class="text-2xl font-bold text-slate-900">Add Team Member</h1>
            <p class="text-slate-500">Create a new user account for a team member</p>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-xl border border-slate-200 p-6">
            <form method="POST" action="{{ route('users.store') }}">
                @csrf

                <div class="space-y-6">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-slate-700 mb-2">Full Name</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required
                            class="form-input w-full rounded-lg border-slate-200 focus:border-orange-500 focus:ring-orange-500"
                            placeholder="John Smith">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-700 mb-2">Email Address</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required
                            class="form-input w-full rounded-lg border-slate-200 focus:border-orange-500 focus:ring-orange-500"
                            placeholder="john@example.com">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Role -->
                    <div>
                        <label for="role" class="block text-sm font-medium text-slate-700 mb-2">Role</label>
                        <select id="role" name="role" required
                            class="form-select w-full rounded-lg border-slate-200 focus:border-orange-500 focus:ring-orange-500">
                            @foreach($roles as $value => $label)
                                <option value="{{ $value }}" {{ old('role', 'staff') === $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-slate-500">
                            <strong>Admin:</strong> Full access including user management |
                            <strong>Manager:</strong> Create/edit all bookings & transfers |
                            <strong>Staff:</strong> View only, edit own bookings
                        </p>
                        @error('role')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-slate-700 mb-2">Password</label>
                        <input type="password" id="password" name="password" required
                            class="form-input w-full rounded-lg border-slate-200 focus:border-orange-500 focus:ring-orange-500"
                            placeholder="Minimum 8 characters">
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-2">Confirm Password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required
                            class="form-input w-full rounded-lg border-slate-200 focus:border-orange-500 focus:ring-orange-500"
                            placeholder="Re-enter password">
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-slate-200">
                    <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Create Team Member</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
