<x-app-layout>
    <div class="max-w-3xl mx-auto">
        <!-- Page Title -->
        <div class="mb-6 sm:mb-8">
            <a href="{{ route('vendors.index') }}" class="text-orange-600 hover:text-orange-800 text-sm flex items-center gap-1 mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Back to Vendors
            </a>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900">Add New Vendor</h1>
            <p class="text-slate-500 text-sm sm:text-base">Create a new vendor record for lodges, guides, or service providers</p>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('vendors.store') }}">
            @csrf

            <div class="bg-white rounded-xl border border-slate-200 p-4 sm:p-6 mb-4 sm:mb-6">
                <h2 class="text-lg font-semibold text-slate-900 mb-4">Basic Information</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Vendor Name *</label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                            class="w-full rounded-lg border-slate-200 focus:border-orange-500 focus:ring-orange-500"
                            placeholder="e.g., Mara Serena Safari Lodge">
                        @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Category *</label>
                        <select name="category" required class="w-full rounded-lg border-slate-200 focus:border-orange-500 focus:ring-orange-500">
                            <option value="">Select category...</option>
                            @foreach($categories as $value => $label)
                                <option value="{{ $value }}" {{ old('category') === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('category')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Country</label>
                        <input type="text" name="country" value="{{ old('country') }}"
                            class="w-full rounded-lg border-slate-200 focus:border-orange-500 focus:ring-orange-500"
                            placeholder="e.g., Kenya">
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 p-4 sm:p-6 mb-4 sm:mb-6">
                <h2 class="text-lg font-semibold text-slate-900 mb-4">Contact Information</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Contact Person</label>
                        <input type="text" name="contact_name" value="{{ old('contact_name') }}"
                            class="w-full rounded-lg border-slate-200 focus:border-orange-500 focus:ring-orange-500"
                            placeholder="Primary contact name">
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}"
                            class="w-full rounded-lg border-slate-200 focus:border-orange-500 focus:ring-orange-500"
                            placeholder="email@example.com">
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Phone</label>
                        <input type="text" name="phone" value="{{ old('phone') }}"
                            class="w-full rounded-lg border-slate-200 focus:border-orange-500 focus:ring-orange-500"
                            placeholder="+254 xxx xxx xxx">
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">WhatsApp</label>
                        <input type="text" name="whatsapp" value="{{ old('whatsapp') }}"
                            class="w-full rounded-lg border-slate-200 focus:border-orange-500 focus:ring-orange-500"
                            placeholder="+254 xxx xxx xxx">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Address</label>
                        <textarea name="address" rows="2"
                            class="w-full rounded-lg border-slate-200 focus:border-orange-500 focus:ring-orange-500"
                            placeholder="Physical address or location">{{ old('address') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 p-4 sm:p-6 mb-4 sm:mb-6">
                <h2 class="text-lg font-semibold text-slate-900 mb-4">Banking Information</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Bank Name</label>
                        <input type="text" name="bank_name" value="{{ old('bank_name') }}"
                            class="w-full rounded-lg border-slate-200 focus:border-orange-500 focus:ring-orange-500">
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Account Number</label>
                        <input type="text" name="bank_account" value="{{ old('bank_account') }}"
                            class="w-full rounded-lg border-slate-200 focus:border-orange-500 focus:ring-orange-500">
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">SWIFT Code</label>
                        <input type="text" name="swift_code" value="{{ old('swift_code') }}"
                            class="w-full rounded-lg border-slate-200 focus:border-orange-500 focus:ring-orange-500">
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Payment Terms</label>
                        <input type="text" name="payment_terms" value="{{ old('payment_terms') }}"
                            class="w-full rounded-lg border-slate-200 focus:border-orange-500 focus:ring-orange-500"
                            placeholder="e.g., Net 30, 50% deposit">
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 p-4 sm:p-6 mb-4 sm:mb-6">
                <h2 class="text-lg font-semibold text-slate-900 mb-4">Additional Information</h2>
                <div>
                    <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Notes</label>
                    <textarea name="notes" rows="3"
                        class="w-full rounded-lg border-slate-200 focus:border-orange-500 focus:ring-orange-500"
                        placeholder="Any additional notes about this vendor...">{{ old('notes') }}</textarea>
                </div>
                <div class="mt-4">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" value="1" checked
                            class="rounded border-slate-300 text-orange-600 focus:ring-orange-500">
                        <span class="text-sm text-slate-700">Vendor is active</span>
                    </label>
                </div>
            </div>

            <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-3">
                <a href="{{ route('vendors.index') }}" class="btn btn-secondary w-full sm:w-auto justify-center">Cancel</a>
                <button type="submit" class="btn btn-primary w-full sm:w-auto justify-center">Create Vendor</button>
            </div>
        </form>
    </div>
</x-app-layout>
