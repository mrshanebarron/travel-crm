<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Traveler;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class IntakeFormController extends Controller
{
    /**
     * Generate a new intake token for a booking.
     */
    public function generateToken(Booking $booking)
    {
        $booking->update([
            'intake_token' => Str::random(32),
        ]);

        return redirect()->back()->with('success', 'Intake form link generated.');
    }

    /**
     * Show the public intake form.
     */
    public function show(string $token)
    {
        $booking = Booking::where('intake_token', $token)
            ->with(['groups.travelers'])
            ->firstOrFail();

        $leadTraveler = $booking->groups->flatMap->travelers->firstWhere('is_lead', true);

        return view('intake.form', [
            'booking' => $booking,
            'leadTraveler' => $leadTraveler,
            'token' => $token,
        ]);
    }

    /**
     * Handle intake form submission.
     */
    public function submit(Request $request, string $token)
    {
        $booking = Booking::where('intake_token', $token)
            ->with(['groups.travelers'])
            ->firstOrFail();

        $validated = $request->validate([
            'travelers' => 'required|array',
            'travelers.*.id' => 'required|exists:travelers,id',
            'travelers.*.first_name' => 'required|string|max:255',
            'travelers.*.last_name' => 'required|string|max:255',
            'travelers.*.email' => 'nullable|email|max:255',
            'travelers.*.phone' => 'nullable|string|max:50',
            'travelers.*.dob' => 'nullable|date',
            'travelers.*.passport_number' => 'nullable|string|max:50',
            'travelers.*.passport_expiry' => 'nullable|date',
            'travelers.*.nationality' => 'nullable|string|max:100',
            'travelers.*.dietary_requirements' => 'nullable|string|max:500',
            'travelers.*.medical_conditions' => 'nullable|string|max:500',
            'travelers.*.emergency_contact_name' => 'nullable|string|max:255',
            'travelers.*.emergency_contact_phone' => 'nullable|string|max:50',
        ]);

        // Verify all travelers belong to this booking
        $bookingTravelerIds = $booking->groups->flatMap->travelers->pluck('id')->toArray();

        foreach ($validated['travelers'] as $travelerData) {
            if (!in_array($travelerData['id'], $bookingTravelerIds)) {
                abort(403, 'Invalid traveler.');
            }

            $traveler = Traveler::find($travelerData['id']);
            $traveler->update([
                'first_name' => $travelerData['first_name'],
                'last_name' => $travelerData['last_name'],
                'email' => $travelerData['email'] ?? null,
                'phone' => $travelerData['phone'] ?? null,
                'dob' => $travelerData['dob'] ?? null,
                'passport_number' => $travelerData['passport_number'] ?? null,
                'passport_expiry' => $travelerData['passport_expiry'] ?? null,
                'nationality' => $travelerData['nationality'] ?? null,
                'dietary_requirements' => $travelerData['dietary_requirements'] ?? null,
                'medical_conditions' => $travelerData['medical_conditions'] ?? null,
                'emergency_contact_name' => $travelerData['emergency_contact_name'] ?? null,
                'emergency_contact_phone' => $travelerData['emergency_contact_phone'] ?? null,
            ]);
        }

        // Log activity
        $booking->activityLogs()->create([
            'user_id' => null, // Public submission
            'action_type' => 'document_uploaded', // Using existing action type
            'notes' => 'Traveler details submitted via intake form',
        ]);

        return view('intake.success', [
            'booking' => $booking,
        ]);
    }
}
