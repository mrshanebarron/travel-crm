<x-app-layout>
    <!-- Page Title -->
    <div class="mb-6 sm:mb-8">
        <a href="{{ route('bookings.index') }}" class="text-orange-600 hover:text-orange-800 text-sm flex items-center gap-1 mb-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Back to Bookings
        </a>
        <h1 class="text-xl sm:text-2xl font-bold text-slate-900">New Booking</h1>
        <p class="text-slate-500 text-sm sm:text-base">Create a new safari booking</p>
    </div>

    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="p-4 sm:p-6">
                <form method="POST" action="{{ route('bookings.store') }}" x-data="bookingForm()">
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

                    <!-- Trip Details -->
                    <div class="mb-6 sm:mb-8">
                        <h3 class="text-lg font-semibold text-slate-900 mb-4">Trip Details</h3>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="country" class="text-xs font-medium text-slate-500 uppercase tracking-wide">Country *</label>
                                <select name="country" id="country" class="w-full rounded-lg border-slate-300 focus:border-orange-500 focus:ring-orange-500" required>
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

                            <div>
                                <label for="start_date" class="text-xs font-medium text-slate-500 uppercase tracking-wide">Start Date *</label>
                                <input type="date" name="start_date" id="start_date" value="{{ old('start_date') }}"
                                    class="w-full rounded-lg border-slate-300 focus:border-orange-500 focus:ring-orange-500" required>
                            </div>

                            <div>
                                <label for="end_date" class="text-xs font-medium text-slate-500 uppercase tracking-wide">End Date *</label>
                                <input type="date" name="end_date" id="end_date" value="{{ old('end_date') }}"
                                    class="w-full rounded-lg border-slate-300 focus:border-orange-500 focus:ring-orange-500" required>
                            </div>
                        </div>
                    </div>

                    <!-- Travelers -->
                    <div class="mb-6 sm:mb-8">
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-4">
                            <h3 class="text-lg font-semibold text-slate-900">Travelers</h3>
                            <x-action-button type="adduser" size="sm" @click="addTraveler()" class="w-full sm:w-auto justify-center" />
                        </div>

                        <template x-for="(traveler, index) in travelers" :key="index">
                            <div class="p-3 sm:p-4 border border-slate-200 rounded-lg mb-4 relative">
                                <button type="button" x-show="travelers.length > 1" @click="removeTraveler(index)"
                                    class="absolute top-3 right-3 text-red-500 hover:text-red-700">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>

                                <div class="text-sm font-medium text-slate-500 mb-3" x-text="index === 0 ? 'Lead Traveler' : 'Traveler ' + (index + 1)"></div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">First Name *</label>
                                        <input type="text" :name="'travelers[' + index + '][first_name]'" x-model="traveler.first_name"
                                            class="w-full rounded-lg border-slate-300 focus:border-orange-500 focus:ring-orange-500" required>
                                    </div>

                                    <div>
                                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Last Name *</label>
                                        <input type="text" :name="'travelers[' + index + '][last_name]'" x-model="traveler.last_name"
                                            class="w-full rounded-lg border-slate-300 focus:border-orange-500 focus:ring-orange-500" required>
                                    </div>

                                    <div>
                                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Email</label>
                                        <input type="email" :name="'travelers[' + index + '][email]'" x-model="traveler.email"
                                            class="w-full rounded-lg border-slate-300 focus:border-orange-500 focus:ring-orange-500">
                                    </div>

                                    <div>
                                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Phone</label>
                                        <input type="text" :name="'travelers[' + index + '][phone]'" x-model="traveler.phone"
                                            class="w-full rounded-lg border-slate-300 focus:border-orange-500 focus:ring-orange-500">
                                    </div>

                                    <div>
                                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Date of Birth</label>
                                        <input type="date" :name="'travelers[' + index + '][dob]'" x-model="traveler.dob"
                                            class="w-full rounded-lg border-slate-300 focus:border-orange-500 focus:ring-orange-500">
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 pt-4 border-t border-slate-200">
                        <x-action-button type="cancel" :href="route('bookings.index')" class="w-full sm:w-auto justify-center" />
                        <x-action-button type="create" label="Create Booking" :submit="true" class="w-full sm:w-auto justify-center" />
                    </div>
                </form>
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
