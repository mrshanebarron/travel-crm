# Twilio WhatsApp Integration Setup

This document explains how to set up Twilio WhatsApp notifications for the Safari CRM system.

## Overview

The system will automatically send WhatsApp messages to team members when:
- A task is assigned TO them (not tasks they assign to others)
- This includes both immediate assignments and scheduled assignments via the daily job

## Prerequisites

You'll need:
1. A Twilio account with WhatsApp Business API access
2. Phone numbers for all team members who should receive notifications
3. Admin access to the production server

## Step 1: Create Twilio Account

1. Go to [twilio.com/console](https://console.twilio.com/)
2. Create an account or log in
3. Navigate to **Console Dashboard**

## Step 2: Get Your Credentials

From the Twilio Console Dashboard, you'll need:

1. **Account SID** - Found on the main dashboard
2. **Auth Token** - Found on the main dashboard (click the eye icon to reveal)

## Step 3: Set Up WhatsApp Business API

1. In Twilio Console, go to **Messaging** → **Try it out** → **Send a WhatsApp message**
2. Follow Twilio's WhatsApp setup process
3. You'll get a WhatsApp-enabled phone number (usually starts with +1415...)

**Important:** Twilio WhatsApp has sandbox mode for testing and production mode for live use. For production, you'll need to go through Twilio's WhatsApp Business API approval process.

## Step 4: Configure the CRM System

### Production Server (.env file)

Add these variables to your production `.env` file:

```bash
# Twilio WhatsApp Integration
TWILIO_ACCOUNT_SID=your_account_sid_here
TWILIO_AUTH_TOKEN=your_auth_token_here
TWILIO_WHATSAPP_FROM=+14155238886
```

Replace with your actual credentials:
- `your_account_sid_here` - Your Account SID from step 2
- `your_auth_token_here` - Your Auth Token from step 2
- `+14155238886` - Your WhatsApp-enabled phone number

### Team Member Phone Numbers

Each team member needs their phone number added to their user profile in the CRM:

1. Log into the CRM as admin
2. Go to user management
3. Edit each user and add their phone number
4. Use international format: +1234567890 (US) or +254123456789 (Kenya)

## Step 5: Test the Integration

SSH into your production server and run:

```bash
cd /var/www/your-crm-directory
php artisan twilio:test
```

This will test your Twilio connection and show any configuration issues.

## Step 6: WhatsApp Message Approval (For Recipients)

**Important:** Before users can receive WhatsApp messages from your Twilio number, they need to opt-in:

### For Testing (Sandbox Mode)
1. Each team member texts "join [sandbox-keyword]" to your Twilio WhatsApp number
2. The sandbox keyword is shown in your Twilio Console

### For Production
1. Send each team member a message from your Twilio WhatsApp number first
2. They need to reply to opt-in to receiving messages
3. Or use Twilio's opt-in templates

## Message Format

When a task is assigned, team members will receive a WhatsApp message like:

```
🔔 *New Task Assigned*

*Task:* 90 Day payment received
*Due:* Feb 15, 2026
*Booking:* Smith, John

*Details:*
Payment reminder for 90-day milestone

📱 View in CRM: https://your-crm-url.com
```

## Troubleshooting

### Common Issues

1. **Messages not sending**
   - Check Twilio credentials are correct
   - Verify WhatsApp number format (+1234567890)
   - Ensure recipient has opted-in to receive messages

2. **"Connection refused" error**
   - Double-check Account SID and Auth Token
   - Make sure credentials don't have extra spaces

3. **"Phone number not verified" error**
   - In sandbox mode, recipient must join the sandbox first
   - In production, ensure WhatsApp Business API is approved

### Testing Individual Messages

You can test sending a message to a specific user:

```bash
# SSH into production server
php artisan tinker

# Test sending to a user
$user = App\Models\User::where('email', 'user@example.com')->first();
$task = App\Models\Task::first();
$twilioService = app(App\Services\TwilioService::class);
$result = $twilioService->sendTaskAssignedNotification($task, $user);
var_dump($result);
```

## Costs

Twilio WhatsApp messaging costs vary by region:
- US: ~$0.005 per message
- International: varies by country
- Check current rates at [twilio.com/whatsapp/pricing](https://www.twilio.com/whatsapp/pricing)

## Security Notes

- Keep your Auth Token secure - never share it or commit it to version control
- Auth Tokens can be regenerated if compromised
- Consider setting up Twilio webhook signatures for added security

## Admin Responsibilities

As the Twilio account owner, you'll need to:
1. Maintain the Twilio account and billing
2. Add new team members' phone numbers to their user profiles
3. Handle WhatsApp opt-in process for new team members
4. Monitor usage and costs in Twilio Console

## Support

If you need help with the setup:
1. Check the troubleshooting section above
2. Run `php artisan twilio:test` to diagnose issues
3. Check Twilio Console logs for detailed error messages
4. Contact the development team with specific error messages