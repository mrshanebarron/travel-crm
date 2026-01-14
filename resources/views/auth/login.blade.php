<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <!-- Demo Notice -->
    <div class="mb-4 p-3 bg-orange-50 border border-orange-200 rounded-lg text-sm text-orange-700">
        <strong>Demo Mode:</strong> Login credentials are pre-filled. Click "Log in" to continue.
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-sm font-medium text-slate-700">Email</label>
            <input id="email" type="email" name="email" value="demo@travelcrm.com" required autofocus autocomplete="username"
                class="mt-1 block w-full px-4 py-3 rounded-lg border border-slate-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
            @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div class="mt-4">
            <label for="password" class="block text-sm font-medium text-slate-700">Password</label>
            <input id="password" type="text" name="password" value="Tr@vel2024!Demo" required autocomplete="current-password"
                class="mt-1 block w-full px-4 py-3 rounded-lg border border-slate-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
            @error('password')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-orange-600 shadow-sm focus:ring-orange-500" name="remember" checked>
                <span class="ms-2 text-sm text-slate-600">Remember me</span>
            </label>
        </div>

        <div class="mt-6">
            <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                Log in
            </button>
        </div>
    </form>
</x-guest-layout>
