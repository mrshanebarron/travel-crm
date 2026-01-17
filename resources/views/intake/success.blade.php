<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You - {{ $booking->booking_number }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center">
    <div class="max-w-md mx-auto px-4 text-center">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-8">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>

            <h1 class="text-2xl font-bold text-slate-900 mb-2">Thank You!</h1>
            <p class="text-slate-600 mb-6">
                Your traveler information has been submitted successfully for booking <strong>{{ $booking->booking_number }}</strong>.
            </p>

            <div class="bg-orange-50 border border-orange-200 rounded-lg p-4 text-left">
                <h2 class="font-semibold text-orange-800 mb-2">Your Safari Details</h2>
                <ul class="text-sm text-orange-700 space-y-1">
                    <li><strong>Destination:</strong> {{ $booking->country }}</li>
                    <li><strong>Dates:</strong> {{ $booking->start_date->format('M j') }} - {{ $booking->end_date->format('M j, Y') }}</li>
                </ul>
            </div>

            <p class="text-sm text-slate-500 mt-6">
                A team member from Tapestry of Africa will be in touch with next steps.
            </p>
        </div>
    </div>
</body>
</html>
