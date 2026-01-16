<x-app-layout>
    <div class="max-w-4xl mx-auto">
        <!-- Page Header -->
        <div class="mb-6 sm:mb-8">
            <a href="{{ route('vendors.index') }}" class="text-orange-600 hover:text-orange-800 text-sm flex items-center gap-1 mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Back to Vendors
            </a>
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-slate-900">{{ $vendor->name }}</h1>
                    <div class="flex flex-wrap items-center gap-2 mt-1">
                        <span class="badge badge-info">{{ App\Models\Vendor::CATEGORIES[$vendor->category] ?? $vendor->category }}</span>
                        @if($vendor->is_active)
                            <span class="badge badge-success">Active</span>
                        @else
                            <span class="badge" style="background: #f1f5f9; color: #475569;">Inactive</span>
                        @endif
                    </div>
                </div>
                <x-action-button type="edit" size="sm" label="Edit Vendor" :href="route('vendors.edit', $vendor)" class="w-full sm:w-auto justify-center" />
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
            <!-- Contact Information -->
            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
                <div class="px-4 sm:px-6 py-4 border-b border-slate-200">
                    <h2 class="text-lg font-semibold text-slate-900">Contact Information</h2>
                </div>
                <div class="p-4 sm:p-6 space-y-4">
                    @if($vendor->contact_name)
                        <div>
                            <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Contact Person</label>
                            <p class="text-slate-900">{{ $vendor->contact_name }}</p>
                        </div>
                    @endif
                    @if($vendor->email)
                        <div>
                            <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Email</label>
                            <p><a href="mailto:{{ $vendor->email }}" class="text-orange-600 hover:text-orange-800">{{ $vendor->email }}</a></p>
                        </div>
                    @endif
                    @if($vendor->phone)
                        <div>
                            <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Phone</label>
                            <p class="text-slate-900">{{ $vendor->phone }}</p>
                        </div>
                    @endif
                    @if($vendor->whatsapp)
                        <div>
                            <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">WhatsApp</label>
                            <p class="text-slate-900">{{ $vendor->whatsapp }}</p>
                        </div>
                    @endif
                    @if($vendor->country)
                        <div>
                            <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Country</label>
                            <p class="text-slate-900">{{ $vendor->country }}</p>
                        </div>
                    @endif
                    @if($vendor->address)
                        <div>
                            <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Address</label>
                            <p class="text-slate-900 whitespace-pre-line">{{ $vendor->address }}</p>
                        </div>
                    @endif
                    @if(!$vendor->contact_name && !$vendor->email && !$vendor->phone)
                        <p class="text-slate-500 text-center py-4">No contact information on file</p>
                    @endif
                </div>
            </div>

            <!-- Banking Information -->
            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
                <div class="px-4 sm:px-6 py-4 border-b border-slate-200">
                    <h2 class="text-lg font-semibold text-slate-900">Banking Information</h2>
                </div>
                <div class="p-4 sm:p-6 space-y-4">
                    @if($vendor->bank_name)
                        <div>
                            <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Bank Name</label>
                            <p class="text-slate-900">{{ $vendor->bank_name }}</p>
                        </div>
                    @endif
                    @if($vendor->bank_account)
                        <div>
                            <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Account Number</label>
                            <p class="text-slate-900 font-mono">{{ $vendor->bank_account }}</p>
                        </div>
                    @endif
                    @if($vendor->swift_code)
                        <div>
                            <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">SWIFT Code</label>
                            <p class="text-slate-900 font-mono">{{ $vendor->swift_code }}</p>
                        </div>
                    @endif
                    @if($vendor->payment_terms)
                        <div>
                            <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Payment Terms</label>
                            <p class="text-slate-900">{{ $vendor->payment_terms }}</p>
                        </div>
                    @endif
                    @if(!$vendor->bank_name && !$vendor->bank_account)
                        <p class="text-slate-500 text-center py-4">No banking information on file</p>
                    @endif
                </div>
            </div>
        </div>

        @if($vendor->notes)
            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden mt-4 sm:mt-6">
                <div class="px-4 sm:px-6 py-4 border-b border-slate-200">
                    <h2 class="text-lg font-semibold text-slate-900">Notes</h2>
                </div>
                <div class="p-4 sm:p-6">
                    <p class="text-slate-700 whitespace-pre-line">{{ $vendor->notes }}</p>
                </div>
            </div>
        @endif

        <!-- Timestamps -->
        <div class="mt-4 sm:mt-6 text-sm text-slate-500">
            <p>Created: {{ $vendor->created_at->format('M j, Y g:i A') }}</p>
            @if($vendor->updated_at->ne($vendor->created_at))
                <p>Updated: {{ $vendor->updated_at->format('M j, Y g:i A') }}</p>
            @endif
        </div>
    </div>
</x-app-layout>
