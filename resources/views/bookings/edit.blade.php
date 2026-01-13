<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit {{ $booking->booking_number }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('bookings.update', $booking) }}">
                        @csrf
                        @method('PUT')

                        @if($errors->any())
                            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                                <ul class="list-disc list-inside">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Trip Details -->
                        <div class="mb-8">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Trip Details</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="country" class="block text-sm font-medium text-gray-700">Country</label>
                                    <select name="country" id="country" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
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
                                    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                                    <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                        <option value="upcoming" {{ $booking->status === 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                                        <option value="active" {{ $booking->status === 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="completed" {{ $booking->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                                    <input type="date" name="start_date" id="start_date" value="{{ $booking->start_date->format('Y-m-d') }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                </div>

                                <div>
                                    <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                                    <input type="date" name="end_date" id="end_date" value="{{ $booking->end_date->format('Y-m-d') }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-between">
                            <form method="POST" action="{{ route('bookings.destroy', $booking) }}" onsubmit="return confirm('Are you sure you want to delete this booking?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center px-4 py-2 border border-red-300 rounded-md font-semibold text-xs text-red-700 uppercase tracking-widest bg-white hover:bg-red-50">
                                    Delete Booking
                                </button>
                            </form>

                            <div class="flex gap-4">
                                <a href="{{ route('bookings.show', $booking) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest bg-white hover:bg-gray-50">
                                    Cancel
                                </a>
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                    Save Changes
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
