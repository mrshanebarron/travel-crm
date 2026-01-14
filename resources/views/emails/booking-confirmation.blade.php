<x-mail::message>
# Booking Confirmation

Dear {{ $traveler->first_name }},

Thank you for booking your safari adventure with Tapestry of Africa! We're thrilled to have you join us.

## Trip Details

**Destination:** {{ $booking->destination_country }}
**Dates:** {{ $booking->start_date->format('F j, Y') }} - {{ $booking->end_date->format('F j, Y') }}
**Duration:** {{ $booking->start_date->diffInDays($booking->end_date) + 1 }} days

@if($booking->groups->count() > 0)
**Your Group:** {{ $booking->groups->first()->name ?? 'Main Group' }}
@endif

## Payment Schedule

Your trip follows our standard payment schedule:
- **25% Deposit:** Due upon booking
- **25% Second Payment:** Due 90 days before departure
- **50% Final Payment:** Due 45 days before departure

@if($traveler->total_cost)
**Your Total:** ${{ number_format($traveler->total_cost, 2) }}
@endif

## What's Next

1. We'll send you detailed information about required documents
2. Your personal safari consultant will be in touch shortly
3. We'll provide your complete itinerary closer to departure

If you have any questions, please don't hesitate to reach out.

<x-mail::button :url="''">
View Your Booking
</x-mail::button>

With gratitude,<br>
The Tapestry of Africa Team

*Every journey funds dental care for communities in East Africa*
</x-mail::message>
