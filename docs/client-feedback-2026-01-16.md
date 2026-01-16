# Safari CRM - Client Feedback Consolidated
**Date:** 2026-01-16
**Client:** Dr. Matthew Jensen
**Source:** 3 Loom Videos

## Video Links
1. https://www.loom.com/share/dc52d893e42f475fa46e11a34bba8fb5 (Traveler Display & Payment Logic)
2. https://www.loom.com/share/c7b379a834974779bce51288ac5913e3 (Add-Ons & Activity Logs)
3. https://www.loom.com/share/63854e5cf5644a54b24861e14945590c (Transfers & Task Management)

---

## BUGS TO FIX

### 1. Transfer Creation Error (HIGH - BLOCKING)
- Getting an error when trying to add an expense to a transfer
- Steps to reproduce: Create transfer → Add booking (e.g., Robert Johnson) → Add Expense → **ERROR**
- **Blocking issue** - client can't test the transfer workflow until fixed

### 2. Add-ons Not Syncing to Ledger (HIGH)
- Hot air balloon ride, nighttime safari, etc. were added but NOT reflected in what's due
- Add-ons need to affect payment calculations

---

## FEATURE REQUESTS

### Daily Itinerary Display
- [ ] Add **4 time slots** per day: Morning, Midday, Afternoon, Evening
- [ ] Add **Location/Destination** field (which park or area)
- [ ] Change layout to **columns instead of stacking** - makes boxes skinnier, shows more days at a time
- [ ] Each time slot should support multiple activities

### Payment Logic
- [ ] **Auto-adjust payment dates** when booking/arrival date changes
- [ ] Payment schedule (reminder): Deposit 25%, 90-day 25%, 45-day 50%
- [ ] When add-ons increase total: recalculate if deposit < 25% of new rate
- [ ] If past arrival date = 100% due (no payment schedule)
- [ ] Add "**Mark as Paid**" option on add-ons (like it exists elsewhere)

### Add-on Handling (Executive Decision from Matt)
- [ ] Add-ons added after booking → include in payment calculations
- [ ] Do NOT track add-ons as separately "paid" - roll into total rate
- [ ] Only change **original rate** if they change lodges/camps
- [ ] Show: Original Rate + Add-ons = New Total Rate

### Activity Log
- [ ] Log ALL changes:
  - Add-ons added
  - Start date changes
  - Rate changes
  - Travelers added
  - Tasks completed
  - Task assignments
- [ ] NOT needed for: initial booking creation, Safari Office uploads

### Checklist UI
- [ ] Move columns **closer to item name**
- [ ] Make **larger notes input area**
- [ ] Reference Google Sheet for all checklist items
- [ ] Show pending tasks at top, completed by date below

### Tasks / Left Menu
- [ ] Add **"Tasks"** to left sidebar menu
- [ ] Master list view: all tasks from all bookings, line-by-line
- [ ] Show who assigned to whom
- [ ] Pending at top, completed by date descending

### Dashboard
- [ ] Rename "Assigned" to **"Tasks I Assigned to Others"**
- [ ] Add ability to **create a task** from dashboard (maybe a button near task section)
- [ ] Make it clearer who tasks are assigned to (for training new team members)

### Transfers
- [ ] Fix the add expense error (bug above)
- [ ] Clarify "Reports" in transfers - should show list of completed/reconciled transfers
- [ ] Support **infinite expenses** per transfer (not just one)

---

## PRIORITY ORDER (Suggested)

### High Priority (Blocking)
1. Fix transfer creation error
2. Add-ons syncing to ledger/payments
3. Payment date auto-adjustment

### Medium Priority
4. Activity log for all changes
5. Daily itinerary display (4 time slots + location)
6. Tasks in left menu + dashboard improvements

### Lower Priority
7. Checklist UI improvements
8. Layout changes (columns vs stacking)

---

## Notes from Matt
- "I hope this is the last video. I don't like to use up all your time on videos"
- Will wait for fixes before logging in to test under his own login
- Wants to see the task assignment flow working
- Appreciates the progress so far - "great job, a lot of progress"
