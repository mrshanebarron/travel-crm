<x-app-layout>
    <!-- Page Title -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Vendors</h1>
            <p class="text-slate-500">Manage lodges, guides, and service providers</p>
        </div>
        <a href="{{ route('vendors.create') }}" class="btn btn-primary">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Add Vendor
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl border border-slate-200 p-4 mb-6">
        <form method="GET" action="{{ route('vendors.index') }}" class="flex items-center gap-4">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search vendors..."
                    class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500">
            </div>
            <div>
                <select name="category" class="rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500">
                    <option value="">All Categories</option>
                    @foreach($categories as $value => $label)
                        <option value="{{ $value }}" {{ request('category') === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-secondary">Filter</button>
            @if(request()->hasAny(['search', 'category']))
                <a href="{{ route('vendors.index') }}" class="text-slate-500 hover:text-slate-700 text-sm">Clear</a>
            @endif
        </form>
    </div>

    <!-- Vendors Table -->
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
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

        @if($vendors->hasPages())
            <div class="px-6 py-4 border-t border-slate-200">
                {{ $vendors->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
