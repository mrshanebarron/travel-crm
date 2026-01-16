<x-app-layout>
    <div class="max-w-3xl mx-auto">
        <!-- Page Title -->
        <div class="mb-6 sm:mb-8">
            <a href="{{ route('vendors.show', $vendor) }}" class="text-orange-600 hover:text-orange-800 text-sm flex items-center gap-1 mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Back to Vendor
            </a>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900">Edit {{ $vendor->name }}</h1>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('vendors.update', $vendor) }}">
            @csrf
            @method('PUT')

            <div class="bg-white rounded-xl border border-slate-200 p-4 sm:p-6 mb-4 sm:mb-6">
                <h2 class="text-lg font-semibold text-slate-900 mb-4">Basic Information</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Vendor Name *</label>
                        <input type="text" name="name" value="{{ old('name', $vendor->name) }}" required
                            class="w-full rounded-lg border-slate-200 focus:border-orange-500 focus:ring-orange-500">
                        @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Category *</label>
                        <select name="category" required class="w-full rounded-lg border-slate-200 focus:border-orange-500 focus:ring-orange-500">
                            @foreach($categories as $value => $label)
                                <option value="{{ $value }}" {{ old('category', $vendor->category) === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Country</label>
                        <input type="text" name="country" value="{{ old('country', $vendor->country) }}"
                            class="w-full rounded-lg border-slate-200 focus:border-orange-500 focus:ring-orange-500">
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 p-4 sm:p-6 mb-4 sm:mb-6">
                <h2 class="text-lg font-semibold text-slate-900 mb-4">Contact Information</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Contact Person</label>
                        <input type="text" name="contact_name" value="{{ old('contact_name', $vendor->contact_name) }}"
                            class="w-full rounded-lg border-slate-200 focus:border-orange-500 focus:ring-orange-500">
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Email</label>
                        <input type="email" name="email" value="{{ old('email', $vendor->email) }}"
                            class="w-full rounded-lg border-slate-200 focus:border-orange-500 focus:ring-orange-500">
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Phone</label>
                        <input type="text" name="phone" value="{{ old('phone', $vendor->phone) }}"
                            class="w-full rounded-lg border-slate-200 focus:border-orange-500 focus:ring-orange-500">
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">WhatsApp</label>
                        <input type="text" name="whatsapp" value="{{ old('whatsapp', $vendor->whatsapp) }}"
                            class="w-full rounded-lg border-slate-200 focus:border-orange-500 focus:ring-orange-500">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Address</label>
                        <textarea name="address" rows="2"
                            class="w-full rounded-lg border-slate-200 focus:border-orange-500 focus:ring-orange-500">{{ old('address', $vendor->address) }}</textarea>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 p-4 sm:p-6 mb-4 sm:mb-6">
                <h2 class="text-lg font-semibold text-slate-900 mb-4">Banking Information</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Bank Name</label>
                        <input type="text" name="bank_name" value="{{ old('bank_name', $vendor->bank_name) }}"
                            class="w-full rounded-lg border-slate-200 focus:border-orange-500 focus:ring-orange-500">
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Account Number</label>
                        <input type="text" name="bank_account" value="{{ old('bank_account', $vendor->bank_account) }}"
                            class="w-full rounded-lg border-slate-200 focus:border-orange-500 focus:ring-orange-500">
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">SWIFT Code</label>
                        <input type="text" name="swift_code" value="{{ old('swift_code', $vendor->swift_code) }}"
                            class="w-full rounded-lg border-slate-200 focus:border-orange-500 focus:ring-orange-500">
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Payment Terms</label>
                        <input type="text" name="payment_terms" value="{{ old('payment_terms', $vendor->payment_terms) }}"
                            class="w-full rounded-lg border-slate-200 focus:border-orange-500 focus:ring-orange-500">
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 p-4 sm:p-6 mb-4 sm:mb-6">
                <h2 class="text-lg font-semibold text-slate-900 mb-4">Additional Information</h2>
                <div>
                    <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Notes</label>
                    <textarea name="notes" rows="3"
                        class="w-full rounded-lg border-slate-200 focus:border-orange-500 focus:ring-orange-500">{{ old('notes', $vendor->notes) }}</textarea>
                </div>
                <div class="mt-4">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $vendor->is_active) ? 'checked' : '' }}
                            class="rounded border-slate-300 text-orange-600 focus:ring-orange-500">
                        <span class="text-sm text-slate-700">Vendor is active</span>
                    </label>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <form method="POST" action="{{ route('vendors.destroy', $vendor) }}" onsubmit="return confirm('Are you sure you want to delete this vendor?')" class="order-2 sm:order-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-600 hover:text-red-800 font-medium text-sm">Delete Vendor</button>
                </form>
                <div class="flex flex-col-reverse sm:flex-row sm:items-center gap-3 order-1 sm:order-2">
                    <x-action-button type="cancel" :href="route('vendors.show', $vendor)" class="w-full sm:w-auto justify-center" />
                    <x-action-button type="save" label="Save Changes" :submit="true" class="w-full sm:w-auto justify-center" />
                </div>
            </div>
        </form>
    </div>
</x-app-layout>
