# Twilio WhatsApp Production Setup

This document provides the steps to configure Twilio WhatsApp integration on the production server.

## Environment Variables Required

Add these three environment variables to the production `.env` file:

```bash
TWILIO_ACCOUNT_SID=your_account_sid_here
TWILIO_AUTH_TOKEN=your_auth_token_here
TWILIO_WHATSAPP_FROM=your_whatsapp_number_here
```

## Setup Steps

1. **SSH to Production Server**
   ```bash
   ssh root@138.197.105.147
   cd /var/www/demos/travelcrm
   ```

2. **Backup Current Environment**
   ```bash
   cp .env .env.backup
   ```

3. **Add Twilio Variables**
   ```bash
   nano .env
   # Add the three variables above at the end of the file
   ```

4. **Test Connection**
   ```bash
   php artisan twilio:test
   ```

## Next Steps

1. **Add Team Member Phone Numbers**
   - Log into the CRM as admin
   - Edit each user profile to add WhatsApp phone numbers
   - Use international format: +1234567890

2. **WhatsApp Opt-in Process**
   - For sandbox: Team members text "join [keyword]" to Twilio number
   - For production: Send test message first, team members reply to opt-in

3. **Test Notifications**
   - Create a test booking
   - Assign a task to a team member with a phone number
   - Verify WhatsApp message is sent

## Status
✅ Twilio integration code complete
✅ Connection test successful
✅ Ready for production deployment

## Documentation
See `TWILIO_SETUP.md` for complete setup guide and troubleshooting.