<x-mail::message>
# Payment Reminder

Dear {{ $traveler->first_name }},

This is a friendly reminder about an upcoming payment for your {{ $booking->destination_country }} safari.

## Payment Details

@php
$typeLabels = [
    'deposit' => '25% Deposit',
    'second' => 'Second Payment (25%)',
    'final' => 'Final Payment (50%)',
];
@endphp

**Payment Type:** {{ $typeLabels[$paymentType] ?? 'Payment' }}
**Amount Due:** ${{ number_format($amountDue, 2) }}
**Due Date:** {{ $dueDate }}

## Trip Summary

**Destination:** {{ $booking->destination_country }}
**Travel Dates:** {{ $booking->start_date->format('F j, Y') }} - {{ $booking->end_date->format('F j, Y') }}

## Payment Methods

We accept the following payment methods:
- Bank Transfer (wire transfer)
- Credit Card

Please contact us for payment instructions or if you need to discuss alternative arrangements.

<x-mail::button :url="''">
Make Payment
</x-mail::button>

Thank you for your prompt attention to this matter.

Warm regards,<br>
The Tapestry of Africa Team
</x-mail::message>
