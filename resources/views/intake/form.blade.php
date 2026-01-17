<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Traveler Information - {{ $booking->booking_number }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-100 min-h-screen py-8">
    <div class="max-w-3xl mx-auto px-4">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-slate-900">Traveler Information Form</h1>
            <p class="text-slate-600 mt-2">Please provide details for all travelers in your group</p>
            <div class="mt-4 inline-flex items-center gap-2 bg-orange-100 text-orange-800 px-4 py-2 rounded-lg">
                <span class="font-semibold">{{ $booking->booking_number }}</span>
                <span>|</span>
                <span>{{ $booking->country }}</span>
                <span>|</span>
                <span>{{ $booking->start_date->format('M j') }} - {{ $booking->end_date->format('M j, Y') }}</span>
            </div>
        </div>

        <!-- Form -->
        <form action="{{ route('intake.submit', $token) }}" method="POST" class="space-y-6">
            @csrf

            @foreach($booking->groups as $group)
                @foreach($group->travelers as $index => $traveler)
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                        <div class="bg-slate-50 px-6 py-4 border-b border-slate-200">
                            <h2 class="font-semibold text-slate-900">
                                Traveler {{ $loop->parent->iteration }}.{{ $loop->iteration }}
                                @if($traveler->is_lead)
                                    <span class="ml-2 text-xs bg-orange-100 text-orange-800 px-2 py-1 rounded">Lead Traveler</span>
                                @endif
                            </h2>
                        </div>

                        <div class="p-6 space-y-4">
                            <input type="hidden" name="travelers[{{ $loop->parent->index }}_{{ $loop->index }}][id]" value="{{ $traveler->id }}">

                            <!-- Name Row -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">First Name *</label>
                                    <input type="text" name="travelers[{{ $loop->parent->index }}_{{ $loop->index }}][first_name]" value="{{ old("travelers.{$loop->parent->index}_{$loop->index}.first_name", $traveler->first_name) }}" required class="w-full rounded-lg border-slate-300 focus:border-orange-500 focus:ring-orange-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Last Name *</label>
                                    <input type="text" name="travelers[{{ $loop->parent->index }}_{{ $loop->index }}][last_name]" value="{{ old("travelers.{$loop->parent->index}_{$loop->index}.last_name", $traveler->last_name) }}" required class="w-full rounded-lg border-slate-300 focus:border-orange-500 focus:ring-orange-500">
                                </div>
                            </div>

                            <!-- Contact Row -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                                    <input type="email" name="travelers[{{ $loop->parent->index }}_{{ $loop->index }}][email]" value="{{ old("travelers.{$loop->parent->index}_{$loop->index}.email", $traveler->email) }}" class="w-full rounded-lg border-slate-300 focus:border-orange-500 focus:ring-orange-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Phone</label>
                                    <input type="tel" name="travelers[{{ $loop->parent->index }}_{{ $loop->index }}][phone]" value="{{ old("travelers.{$loop->parent->index}_{$loop->index}.phone", $traveler->phone) }}" class="w-full rounded-lg border-slate-300 focus:border-orange-500 focus:ring-orange-500">
                                </div>
                            </div>

                            <!-- DOB and Nationality -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Date of Birth</label>
                                    <input type="date" name="travelers[{{ $loop->parent->index }}_{{ $loop->index }}][dob]" value="{{ old("travelers.{$loop->parent->index}_{$loop->index}.dob", $traveler->dob?->format('Y-m-d')) }}" class="w-full rounded-lg border-slate-300 focus:border-orange-500 focus:ring-orange-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Nationality</label>
                                    <input type="text" name="travelers[{{ $loop->parent->index }}_{{ $loop->index }}][nationality]" value="{{ old("travelers.{$loop->parent->index}_{$loop->index}.nationality", $traveler->nationality) }}" placeholder="e.g., United States" class="w-full rounded-lg border-slate-300 focus:border-orange-500 focus:ring-orange-500">
                                </div>
                            </div>

                            <!-- Passport Row -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Passport Number</label>
                                    <input type="text" name="travelers[{{ $loop->parent->index }}_{{ $loop->index }}][passport_number]" value="{{ old("travelers.{$loop->parent->index}_{$loop->index}.passport_number", $traveler->passport_number) }}" class="w-full rounded-lg border-slate-300 focus:border-orange-500 focus:ring-orange-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Passport Expiry</label>
                                    <input type="date" name="travelers[{{ $loop->parent->index }}_{{ $loop->index }}][passport_expiry]" value="{{ old("travelers.{$loop->parent->index}_{$loop->index}.passport_expiry", $traveler->passport_expiry?->format('Y-m-d')) }}" class="w-full rounded-lg border-slate-300 focus:border-orange-500 focus:ring-orange-500">
                                </div>
                            </div>

                            <!-- Special Requirements -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Dietary Requirements</label>
                                    <textarea name="travelers[{{ $loop->parent->index }}_{{ $loop->index }}][dietary_requirements]" rows="2" placeholder="e.g., Vegetarian, Gluten-free, Allergies..." class="w-full rounded-lg border-slate-300 focus:border-orange-500 focus:ring-orange-500">{{ old("travelers.{$loop->parent->index}_{$loop->index}.dietary_requirements", $traveler->dietary_requirements) }}</textarea>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Medical Conditions</label>
                                    <textarea name="travelers[{{ $loop->parent->index }}_{{ $loop->index }}][medical_conditions]" rows="2" placeholder="Any conditions we should be aware of..." class="w-full rounded-lg border-slate-300 focus:border-orange-500 focus:ring-orange-500">{{ old("travelers.{$loop->parent->index}_{$loop->index}.medical_conditions", $traveler->medical_conditions) }}</textarea>
                                </div>
                            </div>

                            <!-- Emergency Contact -->
                            <div class="border-t border-slate-200 pt-4 mt-4">
                                <h3 class="text-sm font-semibold text-slate-700 mb-3">Emergency Contact</h3>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-1">Contact Name</label>
                                        <input type="text" name="travelers[{{ $loop->parent->index }}_{{ $loop->index }}][emergency_contact_name]" value="{{ old("travelers.{$loop->parent->index}_{$loop->index}.emergency_contact_name", $traveler->emergency_contact_name) }}" class="w-full rounded-lg border-slate-300 focus:border-orange-500 focus:ring-orange-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-1">Contact Phone</label>
                                        <input type="tel" name="travelers[{{ $loop->parent->index }}_{{ $loop->index }}][emergency_contact_phone]" value="{{ old("travelers.{$loop->parent->index}_{$loop->index}.emergency_contact_phone", $traveler->emergency_contact_phone) }}" class="w-full rounded-lg border-slate-300 focus:border-orange-500 focus:ring-orange-500">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endforeach

            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <h3 class="text-red-800 font-medium mb-2">Please correct the following errors:</h3>
                    <ul class="list-disc list-inside text-sm text-red-700">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="flex justify-center">
                <button type="submit" class="px-8 py-3 bg-orange-600 text-white font-semibold rounded-lg hover:bg-orange-700 transition-colors">
                    Submit Traveler Information
                </button>
            </div>
        </form>

        <p class="text-center text-sm text-slate-500 mt-8">
            Questions? Contact Tapestry of Africa
        </p>
    </div>
</body>
</html>
