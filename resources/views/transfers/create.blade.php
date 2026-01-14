<x-app-layout>
    <!-- Page Title -->
    <div class="mb-6 sm:mb-8">
        <a href="{{ route('transfers.index') }}" class="text-orange-600 hover:text-orange-800 text-sm flex items-center gap-1 mb-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Back to Transfers
        </a>
        <h1 class="text-xl sm:text-2xl font-bold text-slate-900">New Transfer Request</h1>
        <p class="text-slate-500 text-sm sm:text-base">Create a new fund transfer to Tapestry of Africa (Kenya)</p>
    </div>

    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="p-4 sm:p-6">
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
                        <label for="request_date" class="text-xs font-medium text-slate-500 uppercase tracking-wide">Request Date *</label>
                        <input type="date" name="request_date" id="request_date" value="{{ old('request_date', date('Y-m-d')) }}"
                            class="w-full rounded-lg border-slate-300 focus:border-orange-500 focus:ring-orange-500" required>
                        <p class="mt-2 text-sm text-slate-500">The transfer number will be auto-generated</p>
                    </div>

                    <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 pt-4 border-t border-slate-200">
                        <a href="{{ route('transfers.index') }}" class="btn btn-secondary w-full sm:w-auto justify-center">Cancel</a>
                        <button type="submit" class="btn btn-primary w-full sm:w-auto justify-center">
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
