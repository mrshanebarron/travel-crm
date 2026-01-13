<x-app-layout>
    <!-- Page Title -->
    <div class="mb-8 flex items-center gap-4">
        <a href="{{ route('bookings.show', $booking) }}" class="text-slate-400 hover:text-slate-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Edit {{ $booking->booking_number }}</h1>
            <p class="text-slate-500">Update booking details</p>
        </div>
    </div>

    <div class="max-w-4xl">
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="p-6">
                <form method="POST" action="{{ route('bookings.update', $booking) }}">
                    @csrf
                    @method('PUT')

                    @if($errors->any())
                        <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg">
                            <ul class="list-disc list-inside">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Trip Details -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-slate-900 mb-4">Trip Details</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="country" class="block text-sm font-medium text-slate-700 mb-1">Country</label>
                                <select name="country" id="country" class="w-full rounded-lg border-slate-300 focus:border-teal-500 focus:ring-teal-500" required>
                                    <option value="Tanzania" {{ $booking->country === 'Tanzania' ? 'selected' : '' }}>Tanzania</option>
                                    <option value="Kenya" {{ $booking->country === 'Kenya' ? 'selected' : '' }}>Kenya</option>
                                    <option value="Botswana" {{ $booking->country === 'Botswana' ? 'selected' : '' }}>Botswana</option>
                                    <option value="South Africa" {{ $booking->country === 'South Africa' ? 'selected' : '' }}>South Africa</option>
                                    <option value="Namibia" {{ $booking->country === 'Namibia' ? 'selected' : '' }}>Namibia</option>
                                    <option value="Rwanda" {{ $booking->country === 'Rwanda' ? 'selected' : '' }}>Rwanda</option>
                                    <option value="Uganda" {{ $booking->country === 'Uganda' ? 'selected' : '' }}>Uganda</option>
                                    <option value="Zimbabwe" {{ $booking->country === 'Zimbabwe' ? 'selected' : '' }}>Zimbabwe</option>
                                    <option value="Zambia" {{ $booking->country === 'Zambia' ? 'selected' : '' }}>Zambia</option>
                                </select>
                            </div>

                            <div>
                                <label for="status" class="block text-sm font-medium text-slate-700 mb-1">Status</label>
                                <select name="status" id="status" class="w-full rounded-lg border-slate-300 focus:border-teal-500 focus:ring-teal-500" required>
                                    <option value="upcoming" {{ $booking->status === 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                                    <option value="active" {{ $booking->status === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="completed" {{ $booking->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                </select>
                            </div>

                            <div>
                                <label for="start_date" class="block text-sm font-medium text-slate-700 mb-1">Start Date</label>
                                <input type="date" name="start_date" id="start_date" value="{{ $booking->start_date->format('Y-m-d') }}"
                                    class="w-full rounded-lg border-slate-300 focus:border-teal-500 focus:ring-teal-500" required>
                            </div>

                            <div>
                                <label for="end_date" class="block text-sm font-medium text-slate-700 mb-1">End Date</label>
                                <input type="date" name="end_date" id="end_date" value="{{ $booking->end_date->format('Y-m-d') }}"
                                    class="w-full rounded-lg border-slate-300 focus:border-teal-500 focus:ring-teal-500" required>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-between pt-4 border-t border-slate-200">
                        <button type="button" onclick="document.getElementById('delete-form').submit()" class="btn btn-secondary text-red-600 hover:bg-red-50">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Delete Booking
                        </button>

                        <div class="flex gap-4">
                            <a href="{{ route('bookings.show', $booking) }}" class="btn btn-secondary">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Save Changes
                            </button>
                        </div>
                    </div>
                </form>

                <form id="delete-form" method="POST" action="{{ route('bookings.destroy', $booking) }}" class="hidden" onsubmit="return confirm('Are you sure you want to delete this booking? This action cannot be undone.')">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
