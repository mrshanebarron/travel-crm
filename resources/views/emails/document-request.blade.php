<x-mail::message>
# Documents Needed

Dear {{ $traveler->first_name }},

To ensure a smooth safari experience, we need to collect some important documents from you.

## Documents Required

@if(count($documentsNeeded) > 0)
@foreach($documentsNeeded as $doc)
- {{ $doc }}
@endforeach
@else
- Copy of your passport (valid for at least 6 months after travel)
- Passport-sized photo for visa application
- Travel insurance documentation
- Emergency contact information
- Any dietary restrictions or medical conditions we should know about
@endif

## Trip Details

**Destination:** {{ $booking->destination_country }}
**Travel Dates:** {{ $booking->start_date->format('F j, Y') }} - {{ $booking->end_date->format('F j, Y') }}

## Important Notes

@if(str_contains(strtolower($booking->destination_country), 'kenya') || str_contains(strtolower($booking->destination_country), 'uganda'))
**Visa Information:** You will need a visa for {{ $booking->destination_country }}. We can assist with the application process once we receive your documents.

**Yellow Fever:** A Yellow Fever vaccination certificate may be required. Please check with your healthcare provider.
@endif

Please send your documents by replying to this email or uploading them through our secure portal.

<x-mail::button :url="''">
Upload Documents
</x-mail::button>

If you have any questions, we're here to help!

Best regards,<br>
The Tapestry of Africa Team
</x-mail::message>
