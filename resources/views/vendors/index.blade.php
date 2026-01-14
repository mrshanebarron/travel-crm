<x-app-layout>
    <!-- Page Title -->
    <div class="mb-6 sm:mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900">Vendors</h1>
            <p class="text-slate-500 text-sm sm:text-base">Manage lodges, guides, and service providers</p>
        </div>
        <a href="{{ route('vendors.create') }}" class="btn btn-primary w-full sm:w-auto justify-center">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Add Vendor
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl border border-slate-200 p-3 sm:p-4 mb-4 sm:mb-6">
        <form method="GET" action="{{ route('vendors.index') }}" class="flex flex-col sm:flex-row gap-3 sm:gap-4">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search vendors..."
                    class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500">
            </div>
            <div class="flex gap-2 sm:gap-4">
                <select name="category" class="flex-1 sm:flex-none rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500">
                    <option value="">All Categories</option>
                    @foreach($categories as $value => $label)
                        <option value="{{ $value }}" {{ request('category') === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-secondary">Filter</button>
                @if(request()->hasAny(['search', 'category']))
                    <a href="{{ route('vendors.index') }}" class="btn btn-secondary hidden sm:inline-flex">Clear</a>
                @endif
            </div>
        </form>
        @if(request()->hasAny(['search', 'category']))
            <div class="mt-3 sm:hidden">
                <a href="{{ route('vendors.index') }}" class="text-slate-500 hover:text-slate-700 text-sm">Clear filters</a>
            </div>
        @endif
    </div>

    <!-- Vendors -->
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <!-- Mobile Card View -->
        <div class="md:hidden divide-y divide-slate-100">
            @forelse($vendors as $vendor)
                <a href="{{ route('vendors.show', $vendor) }}" class="block p-4 hover:bg-orange-50 transition-colors">
                    <div class="flex items-start justify-between gap-3 mb-2">
                        <div class="min-w-0">
                            <p class="font-semibold text-slate-900">{{ $vendor->name }}</p>
                            @if($vendor->contact_name)
                                <p class="text-sm text-slate-500">{{ $vendor->contact_name }}</p>
                            @endif
                        </div>
                        <div class="flex flex-col items-end gap-1 flex-shrink-0">
                            <span class="badge badge-info text-xs">{{ $categories[$vendor->category] ?? $vendor->category }}</span>
                            @if($vendor->is_active)
                                <span class="badge badge-success text-xs">Active</span>
                            @else
                                <span class="badge text-xs" style="background: #f1f5f9; color: #475569;">Inactive</span>
                            @endif
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-slate-500">
                        @if($vendor->email)
                            <span class="flex items-center gap-1 truncate">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                <span class="truncate">{{ $vendor->email }}</span>
                            </span>
                        @endif
                        @if($vendor->country)
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                {{ $vendor->country }}
                            </span>
                        @endif
                    </div>
                    <div class="mt-3 flex items-center gap-2">
                        <span class="btn btn-secondary text-sm py-1.5 px-3 flex-1 justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            View
                        </span>
                        <a href="{{ route('vendors.edit', $vendor) }}" class="btn btn-secondary text-sm py-1.5 px-3 flex-1 justify-center" onclick="event.stopPropagation()">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Edit
                        </a>
                    </div>
                </a>
            @empty
                <div class="p-8 text-center">
                    <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <p class="text-slate-500 mb-4">No vendors found</p>
                    <a href="{{ route('vendors.create') }}" class="text-orange-600 hover:text-orange-800 font-medium">Add your first vendor</a>
                </div>
            @endforelse
        </div>

        <!-- Desktop Table View -->
        <div class="hidden md:block table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Vendor</th>
                        <th>Category</th>
                        <th>Contact</th>
                        <th>Country</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vendors as $vendor)
                        <tr class="cursor-pointer hover:bg-slate-50" onclick="window.location='{{ route('vendors.show', $vendor) }}'">
                            <td>
                                <div class="font-medium text-slate-900">{{ $vendor->name }}</div>
                                @if($vendor->contact_name)
                                    <div class="text-sm text-slate-500">{{ $vendor->contact_name }}</div>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-info">{{ $categories[$vendor->category] ?? $vendor->category }}</span>
                            </td>
                            <td>
                                <div class="text-sm">
                                    @if($vendor->email)
                                        <div class="text-slate-600">{{ $vendor->email }}</div>
                                    @endif
                                    @if($vendor->phone)
                                        <div class="text-slate-500">{{ $vendor->phone }}</div>
                                    @endif
                                </div>
                            </td>
                            <td class="text-slate-600">{{ $vendor->country ?? '-' }}</td>
                            <td>
                                @if($vendor->is_active)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge" style="background: #f1f5f9; color: #475569;">Inactive</span>
                                @endif
                            </td>
                            <td onclick="event.stopPropagation()">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('vendors.edit', $vendor) }}" class="btn btn-secondary text-sm py-2 px-3">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-500">
                                No vendors found. <a href="{{ route('vendors.create') }}" class="text-orange-600 hover:text-orange-800">Add your first vendor</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($vendors->hasPages())
            <div class="px-4 sm:px-6 py-4 border-t border-slate-200">
                {{ $vendors->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
