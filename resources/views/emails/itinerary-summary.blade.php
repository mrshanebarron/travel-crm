<x-mail::message>
# Your Safari Itinerary

Dear {{ $traveler->first_name }},

Your adventure awaits! Here's your complete safari itinerary for {{ $booking->destination_country }}.

## Trip Overview

**Destination:** {{ $booking->destination_country }}
**Dates:** {{ $booking->start_date->format('F j, Y') }} - {{ $booking->end_date->format('F j, Y') }}
**Duration:** {{ $booking->start_date->diffInDays($booking->end_date) + 1 }} days

---

## Day-by-Day Itinerary

@foreach($booking->safariDays->sortBy('day_number') as $day)
### Day {{ $day->day_number }}: {{ $day->date?->format('l, F j') }}

@if($day->location)
**Location:** {{ $day->location }}
@endif

@if($day->lodge)
**Accommodation:** {{ $day->lodge }}
@endif

@if($day->morning_activity)
**Morning:** {{ $day->morning_activity }}
@endif

@if($day->afternoon_activity)
**Afternoon:** {{ $day->afternoon_activity }}
@endif

@if($day->meal_plan)
**Meals:** {{ $day->meal_plan }}
@endif

@if($day->notes)
*{{ $day->notes }}*
@endif

---

@endforeach

## What to Pack

- Neutral-colored clothing (khaki, olive, tan)
- Comfortable walking shoes
- Sun hat and sunglasses
- Sunscreen (SPF 50+)
- Insect repellent
- Camera with extra batteries
- Binoculars
- Light jacket for cool mornings

## Important Reminders

- Check passport validity (6+ months from travel date)
- Confirm travel insurance coverage
- Bring copies of all important documents
- Notify your bank of international travel

We're so excited for your journey!

<x-mail::button :url="''">
View Full Booking Details
</x-mail::button>

Safe travels,<br>
The Tapestry of Africa Team

*Every journey funds dental care for communities in East Africa*
</x-mail::message>
