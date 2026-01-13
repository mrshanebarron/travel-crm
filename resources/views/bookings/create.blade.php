<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('New Booking') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('bookings.store') }}" x-data="bookingForm()">
                        @csrf

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
                                        <option value="">Select a country</option>
                                        <option value="Tanzania" {{ old('country') === 'Tanzania' ? 'selected' : '' }}>Tanzania</option>
                                        <option value="Kenya" {{ old('country') === 'Kenya' ? 'selected' : '' }}>Kenya</option>
                                        <option value="Botswana" {{ old('country') === 'Botswana' ? 'selected' : '' }}>Botswana</option>
                                        <option value="South Africa" {{ old('country') === 'South Africa' ? 'selected' : '' }}>South Africa</option>
                                        <option value="Namibia" {{ old('country') === 'Namibia' ? 'selected' : '' }}>Namibia</option>
                                        <option value="Rwanda" {{ old('country') === 'Rwanda' ? 'selected' : '' }}>Rwanda</option>
                                        <option value="Uganda" {{ old('country') === 'Uganda' ? 'selected' : '' }}>Uganda</option>
                                        <option value="Zimbabwe" {{ old('country') === 'Zimbabwe' ? 'selected' : '' }}>Zimbabwe</option>
                                        <option value="Zambia" {{ old('country') === 'Zambia' ? 'selected' : '' }}>Zambia</option>
                                    </select>
                                </div>

                                <div></div>

                                <div>
                                    <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                                    <input type="date" name="start_date" id="start_date" value="{{ old('start_date') }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                </div>

                                <div>
                                    <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                                    <input type="date" name="end_date" id="end_date" value="{{ old('end_date') }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                </div>
                            </div>
                        </div>

                        <!-- Travelers -->
                        <div class="mb-8">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-medium text-gray-900">Travelers</h3>
                                <button type="button" @click="addTraveler()"
                                    class="inline-flex items-center px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                    + Add Traveler
                                </button>
                            </div>

                            <template x-for="(traveler, index) in travelers" :key="index">
                                <div class="p-4 border rounded-lg mb-4 relative">
                                    <button type="button" x-show="travelers.length > 1" @click="removeTraveler(index)"
                                        class="absolute top-2 right-2 text-red-600 hover:text-red-800">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>

                                    <div class="text-sm font-medium text-gray-500 mb-3" x-text="index === 0 ? 'Lead Traveler' : 'Traveler ' + (index + 1)"></div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">First Name</label>
                                            <input type="text" :name="'travelers[' + index + '][first_name]'" x-model="traveler.first_name"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Last Name</label>
                                            <input type="text" :name="'travelers[' + index + '][last_name]'" x-model="traveler.last_name"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Email</label>
                                            <input type="email" :name="'travelers[' + index + '][email]'" x-model="traveler.email"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Phone</label>
                                            <input type="text" :name="'travelers[' + index + '][phone]'" x-model="traveler.phone"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Date of Birth</label>
                                            <input type="date" :name="'travelers[' + index + '][dob]'" x-model="traveler.dob"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <div class="flex justify-end gap-4">
                            <a href="{{ route('bookings.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest bg-white hover:bg-gray-50">
                                Cancel
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                Create Booking
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function bookingForm() {
            return {
                travelers: [{ first_name: '', last_name: '', email: '', phone: '', dob: '' }],
                addTraveler() {
                    this.travelers.push({ first_name: '', last_name: '', email: '', phone: '', dob: '' });
                },
                removeTraveler(index) {
                    this.travelers.splice(index, 1);
                }
            }
        }
    </script>
</x-app-layout>
