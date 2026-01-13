<x-app-layout>
    <!-- Page Title -->
    <div class="mb-8 flex items-center gap-4">
        <a href="{{ route('transfers.index') }}" class="text-slate-400 hover:text-slate-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-slate-900">New Transfer Request</h1>
            <p class="text-slate-500">Create a new fund transfer to Kenya</p>
        </div>
    </div>

    <div class="max-w-2xl">
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="p-6">
                <form method="POST" action="{{ route('transfers.store') }}">
                    @csrf

                    @if($errors->any())
                        <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg">
                            <ul class="list-disc list-inside">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="mb-6">
                        <label for="request_date" class="block text-sm font-medium text-slate-700 mb-1">Request Date</label>
                        <input type="date" name="request_date" id="request_date" value="{{ old('request_date', date('Y-m-d')) }}"
                            class="w-full rounded-lg border-slate-300 focus:border-teal-500 focus:ring-teal-500" required>
                        <p class="mt-2 text-sm text-slate-500">The transfer number will be auto-generated</p>
                    </div>

                    <div class="flex justify-end gap-4 pt-4 border-t border-slate-200">
                        <a href="{{ route('transfers.index') }}" class="btn btn-secondary">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Create Transfer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
