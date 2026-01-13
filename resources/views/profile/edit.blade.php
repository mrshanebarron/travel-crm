<x-app-layout>
    <!-- Page Title -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-900">Profile Settings</h1>
        <p class="text-slate-500">Manage your account settings and preferences</p>
    </div>

    <div class="max-w-3xl space-y-6">
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200">
                <h2 class="text-lg font-semibold text-slate-900">Profile Information</h2>
                <p class="text-sm text-slate-500">Update your account's profile information and email address.</p>
            </div>
            <div class="p-6">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200">
                <h2 class="text-lg font-semibold text-slate-900">Update Password</h2>
                <p class="text-sm text-slate-500">Ensure your account is using a long, random password to stay secure.</p>
            </div>
            <div class="p-6">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200">
                <h2 class="text-lg font-semibold text-slate-900">Delete Account</h2>
                <p class="text-sm text-slate-500">Permanently delete your account and all associated data.</p>
            </div>
            <div class="p-6">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-app-layout>
