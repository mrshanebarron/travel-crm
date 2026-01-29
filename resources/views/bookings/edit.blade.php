<x-app-layout>
    <!-- Page Title -->
    <div class="mb-6 sm:mb-8">
        <a href="{{ route('bookings.show', $booking) }}" class="text-orange-600 hover:text-orange-800 text-sm flex items-center gap-1 mb-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Back to Booking
        </a>
        <h1 class="text-xl sm:text-2xl font-bold text-slate-900">Edit {{ $booking->booking_number }}</h1>
        <p class="text-slate-500 text-sm sm:text-base">Update booking details and travelers</p>
    </div>

    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="p-4 sm:p-6">
                <form method="POST" action="{{ route('bookings.update', $booking) }}" x-data="bookingEditForm()">
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
                    <div class="mb-6 sm:mb-8">
                        <h3 class="text-lg font-semibold text-slate-900 mb-4">Trip Details</h3>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="country" class="text-xs font-medium text-slate-500 uppercase tracking-wide">Country *</label>
                                <select name="country" id="country" class="w-full rounded-lg border-slate-300 focus:border-orange-500 focus:ring-orange-500" required>
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
                                <label for="status" class="text-xs font-medium text-slate-500 uppercase tracking-wide">Status *</label>
                                <select name="status" id="status" class="w-full rounded-lg border-slate-300 focus:border-orange-500 focus:ring-orange-500" required>
                                    <option value="upcoming" {{ $booking->status === 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                                    <option value="active" {{ $booking->status === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="completed" {{ $booking->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                </select>
                            </div>

                            <div>
                                <label for="start_date" class="text-xs font-medium text-slate-500 uppercase tracking-wide">Start Date *</label>
                                <input type="date" name="start_date" id="start_date" value="{{ $booking->start_date->format('Y-m-d') }}"
                                    class="w-full rounded-lg border-slate-300 focus:border-orange-500 focus:ring-orange-500" required>
                            </div>

                            <div>
                                <label for="end_date" class="text-xs font-medium text-slate-500 uppercase tracking-wide">End Date *</label>
                                <input type="date" name="end_date" id="end_date" value="{{ $booking->end_date->format('Y-m-d') }}"
                                    class="w-full rounded-lg border-slate-300 focus:border-orange-500 focus:ring-orange-500" required>
                            </div>
                        </div>
                    </div>

                    <!-- Guides by Country -->
                    <div class="mb-6 sm:mb-8" x-data="guidesForm()">
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-4">
                            <h3 class="text-lg font-semibold text-slate-900">Guides by Country</h3>
                            <button type="button" @click="addGuide()"
                                class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-medium text-orange-600 hover:text-orange-800 border border-orange-300 hover:border-orange-400 rounded-lg transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Add Guide
                            </button>
                        </div>

                        <template x-if="guides.length === 0">
                            <p class="text-slate-500 text-sm">No guides assigned yet. Click "Add Guide" to assign guides for each country visited.</p>
                        </template>

                        <template x-for="(guide, index) in guides" :key="index">
                            <div class="p-4 border border-slate-200 rounded-lg mb-3">
                                <div class="flex flex-col lg:flex-row gap-3">
                                    <!-- Country Selection -->
                                    <div class="lg:w-1/4">
                                        <label class="block text-xs font-medium text-slate-700 mb-1">Country</label>
                                        <select :name="'guides[' + index + '][country]'" x-model="guide.country" @change="updateGuideOptions(index)"
                                            class="w-full rounded-lg border-slate-300 focus:border-orange-500 focus:ring-orange-500 text-sm">
                                            <option value="">Select Country</option>
                                            <option value="Kenya">Kenya</option>
                                            <option value="Tanzania">Tanzania</option>
                                            <option value="Uganda">Uganda</option>
                                        </select>
                                    </div>

                                    <!-- Guide Selection -->
                                    <div class="lg:w-1/4">
                                        <label class="block text-xs font-medium text-slate-700 mb-1">Guide</label>
                                        <select :name="'guides[' + index + '][name]'" x-model="guide.name"
                                            class="w-full rounded-lg border-slate-300 focus:border-orange-500 focus:ring-orange-500 text-sm">
                                            <option value="">Select Guide</option>
                                            <template x-if="guide.country === 'Kenya'">
                                                <optgroup label="Kenya Guides">
                                                    <option value="Sammy">Sammy</option>
                                                    <option value="George">George</option>
                                                    <option value="Vincent">Vincent</option>
                                                    <option value="Joseph">Joseph</option>
                                                    <option value="Kim">Kim</option>
                                                    <option value="Wanderi">Wanderi</option>
                                                    <option value="Boniface">Boniface</option>
                                                    <option value="Moses">Moses</option>
                                                </optgroup>
                                            </template>
                                            <template x-if="guide.country === 'Tanzania'">
                                                <optgroup label="Tanzania Guides">
                                                    <option value="Yuda">Yuda</option>
                                                    <option value="Josephat">Josephat</option>
                                                    <option value="Vincent">Vincent</option>
                                                    <option value="Ernest">Ernest</option>
                                                    <option value="Eddie">Eddie</option>
                                                    <option value="Andrew">Andrew</option>
                                                    <option value="Amos">Amos</option>
                                                    <option value="Tony">Tony</option>
                                                </optgroup>
                                            </template>
                                            <template x-if="guide.country === 'Uganda'">
                                                <optgroup label="Uganda Guides">
                                                    <option value="Francis">Francis</option>
                                                </optgroup>
                                            </template>
                                        </select>
                                    </div>

                                    <!-- Date Range -->
                                    <div class="lg:w-1/3 flex gap-2">
                                        <div class="flex-1">
                                            <label class="block text-xs font-medium text-slate-700 mb-1">From Date</label>
                                            <input type="date" :name="'guides[' + index + '][from_date]'" x-model="guide.from_date"
                                                class="w-full rounded-lg border-slate-300 focus:border-orange-500 focus:ring-orange-500 text-sm">
                                        </div>
                                        <div class="flex-1">
                                            <label class="block text-xs font-medium text-slate-700 mb-1">To Date</label>
                                            <input type="date" :name="'guides[' + index + '][to_date]'" x-model="guide.to_date"
                                                class="w-full rounded-lg border-slate-300 focus:border-orange-500 focus:ring-orange-500 text-sm">
                                        </div>
                                    </div>

                                    <!-- Remove Button -->
                                    <div class="lg:w-auto flex items-end">
                                        <button type="button" @click="removeGuide(index)"
                                            class="text-red-500 hover:text-red-700 p-2 rounded-lg hover:bg-red-50 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>
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

                                <div class="flex items-center gap-2 mb-3">
                                    <span class="text-sm font-medium text-slate-500" x-text="traveler.is_lead ? 'Lead Traveler' : 'Traveler ' + (index + 1)"></span>
                                    <template x-if="traveler.is_lead">
                                        <span class="badge badge-info text-xs">Lead</span>
                                    </template>
                                </div>

                                <input type="hidden" :name="'travelers[' + index + '][id]'" x-model="traveler.id">

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

                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-4 border-t border-slate-200">
                        <button type="button" onclick="document.getElementById('delete-form').submit()" class="text-red-600 hover:text-red-800 font-medium text-sm order-2 sm:order-1">
                            Delete Booking
                        </button>

                        <div class="flex flex-col-reverse sm:flex-row sm:items-center gap-3 order-1 sm:order-2">
                            <x-action-button type="cancel" :href="route('bookings.show', $booking)" class="w-full sm:w-auto justify-center" />
                            <x-action-button type="save" label="Save Changes" :submit="true" class="w-full sm:w-auto justify-center" />
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

    @php
        $travelersJson = $booking->travelers->map(function($t) {
            return [
                'id' => $t->id,
                'first_name' => $t->first_name,
                'last_name' => $t->last_name,
                'email' => $t->email,
                'phone' => $t->phone,
                'dob' => $t->dob ? $t->dob->format('Y-m-d') : '',
                'is_lead' => $t->is_lead,
            ];
        })->values();

        // Convert guides array for the form (handle both old and new formats)
        $guidesJson = collect($booking->guides ?? [])->map(function($guide, $key) {
            // If it's the old format (country => name), convert to new format
            if (is_string($guide)) {
                return ['country' => $key, 'name' => $guide, 'from_date' => '', 'to_date' => ''];
            }
            // If it's already new format, ensure all fields are present
            return [
                'country' => $guide['country'] ?? '',
                'name' => $guide['name'] ?? '',
                'from_date' => $guide['from_date'] ?? '',
                'to_date' => $guide['to_date'] ?? '',
            ];
        })->values();
    @endphp
    <script>
        function bookingEditForm() {
            return {
                travelers: @json($travelersJson),
                addTraveler() {
                    this.travelers.push({ id: '', first_name: '', last_name: '', email: '', phone: '', dob: '', is_lead: false });
                },
                removeTraveler(index) {
                    this.travelers.splice(index, 1);
                }
            }
        }

        function guidesForm() {
            return {
                guides: @json($guidesJson),
                addGuide() {
                    this.guides.push({ country: '', name: '', from_date: '', to_date: '' });
                },
                removeGuide(index) {
                    this.guides.splice(index, 1);
                },
                updateGuideOptions(index) {
                    // Clear guide name when country changes
                    this.guides[index].name = '';
                }
            }
        }
    </script>
</x-app-layout>
