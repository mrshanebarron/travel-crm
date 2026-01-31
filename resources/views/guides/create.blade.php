@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <div class="mb-8">
            <div class="flex items-center">
                <a href="{{ route('guides.index') }}" class="text-slate-500 hover:text-slate-700 mr-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-slate-900">Add Guide Assignment</h1>
                    <p class="mt-2 text-sm text-slate-600">Assign guides to countries with specific date ranges</p>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <form action="{{ route('guides.store') }}" method="POST" class="space-y-6 p-6">
                @csrf

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <!-- Country -->
                    <div>
                        <label for="country" class="block text-sm font-medium text-slate-700 mb-2">Country</label>
                        <select name="country" id="country" required
                                class="w-full px-3 py-2 border border-slate-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500">
                            <option value="">Select Country</option>
                            @foreach($countries as $country)
                                <option value="{{ $country }}" {{ old('country') === $country ? 'selected' : '' }}>
                                    {{ $country }}
                                </option>
                            @endforeach
                        </select>
                        @error('country')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Guide Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-slate-700 mb-2">Guide</label>
                        <div class="relative">
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                   list="kenya-guides"
                                   class="w-full px-3 py-2 border border-slate-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500"
                                   placeholder="Enter guide name or select from list">
                            
                            <!-- Kenya Guides Dropdown -->
                            <datalist id="kenya-guides">
                                @foreach($kenyaGuides as $guide)
                                    <option value="{{ $guide }}">{{ $guide }}</option>
                                @endforeach
                            </datalist>
                        </div>
                        <p class="mt-1 text-xs text-slate-500">
                            For Kenya: Select from dropdown or type custom name
                        </p>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <!-- From Date -->
                    <div>
                        <label for="date_from" class="block text-sm font-medium text-slate-700 mb-2">From Date</label>
                        <input type="date" name="date_from" id="date_from" value="{{ old('date_from') }}" required
                               class="w-full px-3 py-2 border border-slate-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500">
                        @error('date_from')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- To Date -->
                    <div>
                        <label for="date_to" class="block text-sm font-medium text-slate-700 mb-2">To Date</label>
                        <input type="date" name="date_to" id="date_to" value="{{ old('date_to') }}" required
                               class="w-full px-3 py-2 border border-slate-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500">
                        @error('date_to')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Notes -->
                <div>
                    <label for="notes" class="block text-sm font-medium text-slate-700 mb-2">Notes (Optional)</label>
                    <textarea name="notes" id="notes" rows="3"
                              class="w-full px-3 py-2 border border-slate-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500"
                              placeholder="Additional notes about this assignment...">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Kenya Guides Reference -->
                <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                    <h4 class="text-sm font-medium text-blue-900 mb-2">Kenya Guides Reference:</h4>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-sm text-blue-700">
                        @foreach($kenyaGuides as $guide)
                            <div>• {{ $guide }}</div>
                        @endforeach
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end space-x-3 pt-6 border-t border-slate-200">
                    <a href="{{ route('guides.index') }}"
                       class="inline-flex items-center px-4 py-2 border border-slate-300 text-sm font-medium rounded-md text-slate-700 bg-white hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Cancel
                    </a>
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Add Guide Assignment
                    </button>
                </div>
            </form>
        </div>

        <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-md p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800">
                        Next: Add Another Guide Assignment
                    </h3>
                    <div class="mt-2 text-sm text-yellow-700">
                        <p>After saving this assignment, you can add another guide by clicking "Add Guide Assignment" again from the guides list.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-populate Kenya guides when Kenya is selected
document.getElementById('country').addEventListener('change', function() {
    const nameField = document.getElementById('name');
    if (this.value === 'Kenya') {
        nameField.setAttribute('list', 'kenya-guides');
        nameField.placeholder = 'Select from Kenya guides or enter custom name';
    } else {
        nameField.removeAttribute('list');
        nameField.placeholder = 'Enter guide name';
    }
});
</script>
@endsection
