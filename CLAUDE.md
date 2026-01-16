# Safari CRM - Project Context

## Client: Dr. Matthew Jensen / Tapestry of Africa

**Company:** Tapestry of Africa (Safari Company)
**Location:** Arizona, USA
**Website:** tapestryofafrica.com
**Upwork Job:** #481 (Custom Safari Booking CRM)

### Background Story (ABC 15 News Segment)

Dr. Matthew Jensen is a dentist from Arizona who first visited East Africa 5 years ago. He set up a mobile dental clinic with 3 other providers:
- **Year 1:** Treated 500 patients
- **Year 2:** Doubled to 1,000+ patients

The need was massive - dental care in remote villages is "almost non-existent." They were removing diseased teeth and ending pain people had lived with for years.

**The Problem:** The cost of continuing care was unsustainable through donations alone.

**The Solution:** Dr. Jensen launched **Tapestry of Africa** - a safari company where **every trip funds dental care** for the communities they visit.

### Business Model

- Premium safari experiences in Kenya and Uganda
- Approximately $25,000/person trips
- Every safari funds permanent dental clinics in remote villages
- "Wanderlust with a purpose" - adventure tourism meets humanitarian impact
- Travelers see the communities they're helping (children waving, visiting clinics)

### What They've Built

- Started with mobile dental clinics
- Now building **permanent dental clinics and facilities** in the areas that need them
- Multi-generational family trips (example: mother booking for twins to show them "lasting impact of giving back")

### Why This CRM Matters

This is an **enterprise-grade booking system** for a high-stakes operation:
- $326K+ spent on Upwork (serious client)
- Complex multi-group safari bookings
- Vendor payment tracking (lodges, guides, flights, park fees)
- Financial ledger for payment schedules (25% deposit, 25% at 90 days, 50% at 45 days)
- Document management for travel logistics
- The CRM helps run a business that literally funds healthcare in East Africa

### Key Quote

> "It started as a mission to end pain. But what he's now built is bringing smiles to faces all across the globe and showing just how far compassion can travel."

---

## Technical Notes

- **Stack:** Laravel 11, Blade components, Vite (NO CDN)
- **Local Dev:** Laravel Herd
- **Database:** MySQL (NEVER SQLite for deployment)
- **Deployment:** Git-based (NEVER rsync for Laravel)

## Audit Status

### Initial Audit (January 13, 2026)
All 8 audit items from gap analysis completed:
- Dashboard cleanup ✓
- Ledger category dropdowns ✓
- Document categories ✓
- Room age breakdown ✓
- Safari Plan fields ✓
- Room assignment per group ✓
- Arrival/Departure by group ✓
- Activity Log auto-logging ✓

### Second Pass Audit (January 13, 2026)
Deep review against 19-page spec - additional improvements:
- **Master Checklist**: Now auto-populates 22 default tasks when booking created (payment reminders, document collection, vendor confirmations, pre/post-trip tasks)
- **Transfer Workflow**: Auto-creates tasks when status changes:
  - Draft → Sent: Creates "Make transfer" task
  - Sent → Transfer Completed: Marks task 1 complete, creates "Make vendor payments" task
  - Transfer Completed → Vendor Payments Complete: Marks task 2 complete, auto-posts ledger entries
- **Transfer Booking Dropdown**: Now shows "Last Name, First Name (Start Date)" format per spec
- **Transfer Expense Description**: Added accessor for proper ledger entry descriptions
- **Transfer Show Page**: Added "Associated Tasks" section to track workflow progress
- **UI Consistency**: Updated all teal focus colors to orange across transfer views

### Security & Code Quality Pass (January 13, 2026)
Based on external audit feedback:
- **XSS Fix**: Changed `{!! json_encode() !!}` to `@json()` in bookings/edit.blade.php
- **Authorization Policies**: Added BookingPolicy and TransferPolicy
  - Bookings: Can't delete if payments received, no force delete
  - Transfers: Can't modify completed transfers, can only delete drafts
- **Config Externalization**: Moved default task list to `config/booking_tasks.php`

### UI Polish (January 13, 2026)
- **Tab Icons**: Fixed alignment - added flexbox to tab buttons so icons display inline with text

---

## Client Relationship Notes

**Communication Style**: Values prompt responses above all. Previous developers left him waiting days/weeks for updates. He's patient but needs acknowledgment and regular updates.

**Long-term Potential**:
- Interested in ongoing website maintenance (tapestryofafrica.com)
- Content updates: rotating packages, homepage features, keeping things current
- Looking for a reliable developer for continued work after CRM completion

**Project Status**: HIRED - Job funded, building final product

---

## Demo Site

**URL:** https://travelcrm.demo.sbarron.com
**Login:** Pre-filled on login page
**Server:** 138.197.105.147 (Demo server)
**Path:** /var/www/demos/travelcrm

---

## Production Deployment Checklist

When deploying to client's production environment, update the following:

### Mail Configuration (REQUIRED)
Currently using Shane's Zoho SMTP for demo. Client needs their own mail service:
```
MAIL_MAILER=smtp
MAIL_HOST=[client's SMTP host]
MAIL_PORT=[465 for SSL, 587 for TLS]
MAIL_USERNAME=[client's email]
MAIL_PASSWORD=[client's password]
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=[client's from address]
MAIL_FROM_NAME="Tapestry of Africa"
```

Options for client:
- **Zoho Mail** ($1/user/month) - what sbarron.com uses
- **Google Workspace** ($6/user/month) - if they use Gmail
- **Resend** (free tier 100/day) - simple API-based
- **Mailgun/SendGrid** - transactional email services

---

## Production Hosting Plan (Pending Matt's Confirmation)

**Date:** January 16, 2026

### Current Situation
Matt uses **Rocket.net** for tapestryofafrica.com - this is WordPress-optimized hosting that won't properly support Laravel.

### Rocket.net SSH Access (for reference)
- **Host:** 131.153.236.180
- **Username:** umiybqa
- **Auth:** SSH key (~/.ssh/id_ed25519)
- **PHP Available:** 7.4 (default), 8.0, 8.1, 8.2, 8.3
- **Composer:** Installed at /home/umiybqa/bin/composer

### Why Rocket.net Won't Work for Laravel
- WordPress-optimized, not general PHP hosting
- No queue workers or process control
- Document root configuration limited
- Would be hacky/fragile setup

### Recommended Solution: Dedicated DigitalOcean Droplet
**Plan:** Create a $6/mo droplet, set up everything, then transfer ownership to Matt

**Steps:**
1. Create droplet under Shane's DO account
2. Set up: PHP 8.2+, MySQL, Nginx, SSL, git deployment
3. Deploy CRM and verify everything works
4. Matt creates DigitalOcean account + adds payment method
5. Transfer droplet ownership to Matt's account
6. Matt pays ~$6/mo directly to DigitalOcean
7. We retain SSH access for ongoing maintenance

**DNS:** Matt points `crm.tapestryofafrica.com` to the new droplet IP

**Status:** WAITING - Need Matt's confirmation he's okay with ~$6/mo hosting cost

### Rocket.net Control Panel Access
- **URL:** control.rocket.net
- **Email:** mrshanebarron@gmail.com
- **Password:** Rat9chet!

---

## Recent Features Added

### Create Booking from Safari Office PDF (January 16, 2026)
Users can now upload a Safari Office PDF directly from the Bookings list page to automatically create a booking with:
- Lead traveler name extracted from PDF
- All travelers (with placeholder names for non-lead)
- Start/end dates and country
- Full itinerary with lodges, meals, activities
- Payment records with extracted rates
- Default task checklist

**Location:** "Import from Safari Office" button on Bookings index page
