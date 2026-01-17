# Safari CRM - Requirements Analysis from Client Videos
**Date:** 2026-01-16
**Client:** Dr. Matthew Jensen
**Source:** 3 Loom Videos (transcribed)

---

## Executive Summary

Matt speaks quickly and makes decisions on-the-fly during the videos. This document tracks his thought process, any contradictions or changes of mind, and extracts the **FINAL DECISION** for each requirement.

---

## VIDEO 1: Traveler Display & Payment Logic

### 1. Daily Itinerary Display - MAJOR REDESIGN

**Matt's thought process:**
- Started by saying "we want to display as much information as we can on each day"
- Initially mentioned: "morning activities, midday activities, afternoon activities, and evening"
- Then noticed existing morning/afternoon: "Oh, I can see your morning and afternoon. Oh, that's great."
- Changed direction: "maybe we can use up this white space by putting morning here, midday here, afternoon there"
- Clarified midday default: "We can always default lunch... we don't really do other meals because they're at the lodges"
- But wants flexibility: "we want the ability to add... midday, we could maybe do handicraft shopping and lunch"

**FINAL DECISION:**
- **4 time slots per day:** Morning, Midday (default: Lunch), Afternoon, Evening
- **Layout change:** COLUMNS instead of stacking - "these boxes become skinnier, right? So we can see more days at a time"
- **Multiple activities per slot:** "We want to go have more than one activity in each area"
- **Add Location/Destination field:** "where are they going to be? What park or area they're going to be in"

---

### 2. Payment Auto-Adjustment Logic

**Matt's requirements (clear, no contradictions):**
- Payment schedule: **25% deposit, 25% at 90-day, 50% at 45-day (balance)**
- "When you update a date, I would assume that should now make a 90-day payment due, and a 45-payment due"
- "They should always auto-adjust based on any change in date or booking date"
- If booking < 90 days out: "deposit's always gonna be 50%" (meaning deposit covers 90-day too)

**FINAL DECISION:**
- Auto-recalculate all payment due dates when arrival/booking date changes
- Payment tiers based on time until arrival:
  - \> 90 days: 25% deposit now, 25% at 90-day, 50% at 45-day
  - 45-90 days: 50% deposit now, 50% at 45-day
  - < 45 days: 100% due now
  - Past arrival: 100% due immediately

---

## VIDEO 2: Add-Ons & Activity Logs

### 3. Add-On Payment Handling - EXECUTIVE DECISION

**Matt's thought process (lots of back-and-forth):**
1. First: "if we add it to the total, then I would just update the Safari rate"
2. Then: "in most cases, when they add a hot air balloon ride... sometimes they're adding it like two days before they arrive"
3. Considered: "if it's past the arrival date, then there's no payment, right? They just pay 100%"
4. Noticed existing feature: "I liked how on this, you said 'Mark as paid'. I think we should also have that here on these add-ons"
5. **EXECUTIVE DECISION:** "I'm going to make an executive decision right here... we will never put these add-ons in the original rate"
6. Then reconsidered mark-as-paid: "I want to decide if they add it after they booked, do we want to just include it on their payments?"
7. Final answer: "I think that's the way to do it, is we add an add-on, and then now the new total rate is what we determine the deposit"
8. Reversed on mark-as-paid: "I guess there's no need to mark it as paid because it'll be added into their payment schedule"

**FINAL DECISION:**
- **NEVER change the original rate** - only change it if they switch lodges/camps
- Add-ons are tracked separately but **roll into total for payment calculations**
- Display: Original Rate + Add-on Totals = New Total Rate
- Payment schedule recalculates based on new total
- If deposit paid < 25% of new total: adjust 90-day payment to compensate
- **NO separate "mark as paid" for add-ons** - they're part of the payment schedule
- Past arrival date = 100% due immediately (no payment schedule)

---

### 4. Activity Log Requirements

**Matt's requirements (clear):**
- "We want ALL changes of any sort to show up in the activity log"
- Specific items to log:
  - Add-ons added
  - Start date changes
  - Rate changes
  - Travelers added
  - Tasks completed
  - Task assignments

**What NOT to log:**
- "Initial booking creation, that's all we need"
- Safari Office uploads - "that's okay"

**FINAL DECISION:**
- Log ALL modifications after initial creation
- Initial booking creation = single "Booking created" entry
- Safari Office import = single "Imported from Safari Office" entry
- Everything else gets logged with timestamp and who made the change

---

### 5. Checklist UI Improvements

**Matt's requirements:**
- "Move these columns closer to the name of the item"
- "We have a much larger input area to see the notes"
- Reference Google Sheet for complete checklist items
- Show status: completed, pending, date assigned
- Organization: pending at top, completed by date below

**FINAL DECISION:**
- Tighter column layout (item name + status columns close together)
- Larger notes textarea
- Pull all items from Google Sheet reference
- Sort: Pending first, then completed (most recent at top)

---

## VIDEO 3: Transfers & Task Management

### 6. Transfer Creation Bug - BLOCKING

**The problem:**
- Matt tried to add an expense to a transfer and got an error
- "I push Expense, it gives me an error code"
- "I can't really go forward on this process until we get that fixed"

**FINAL DECISION:**
- **HIGH PRIORITY BUG** - blocking client testing
- Support "infinite amount of deposits for one transfer" (multiple expenses)

---

### 7. Transfer Reports Clarification

**Matt's uncertainty:**
- "With reports in the transfer, maybe I'll have you explain that to me better"
- "Maybe that shows the list of transfers that have been completed and reconciled. I'm not sure."

**FINAL DECISION:**
- Clarify what Reports shows (likely: completed/reconciled transfers list)
- Matt likes the vendor list feature: "I like making a list of vendors. That's great."

---

### 8. Tasks in Left Menu - NEW FEATURE

**Matt's request:**
- "Can we put on the left menu... Tasks"
- Master view: "just master line-by-line list of all tasks from all clients"
- Show: who assigned to whom
- Sort: "pending transfers at the top, then completed by date... most recent stuff on the way down"

**FINAL DECISION:**
- Add "Tasks" to left sidebar navigation
- Master task list view showing:
  - All tasks across all bookings
  - Assignee and assigner
  - Pending at top, completed below (descending by completion date)

---

### 9. Dashboard Task Section Improvements

**Matt's requests:**
- Rename "Assigned" to **"Tasks I Assigned to Others"**
- "As I'm training people on this, they might not know who it's assigned to"
- "There's no place to create a task... I don't see how to assign a task"
- Suggestion: "Maybe we put it up here, over the task button"

**FINAL DECISION:**
- Rename section header for clarity
- Add "Create Task" button on dashboard (near task section)
- Make assignee clear for training purposes

---

## PRIORITY MATRIX

### BLOCKING (Must fix first)
1. **Transfer expense creation error** - Matt can't test transfers at all

### HIGH PRIORITY (Core functionality)
2. **Add-ons syncing to payment calculations** - Hot air balloon not showing in ledger
3. **Payment date auto-adjustment** - Dates changed but payments didn't recalculate
4. **Activity log for all changes** - Currently missing many change types

### MEDIUM PRIORITY (UX improvements)
5. **Daily itinerary redesign** - 4 time slots, columns layout, location field
6. **Tasks in left menu** - Master task list view
7. **Dashboard improvements** - Clearer labels, create task button

### LOWER PRIORITY (Polish)
8. **Checklist UI** - Column spacing, larger notes area
9. **Transfer reports clarification** - Documentation/explanation

---

## CONTRADICTIONS & DECISIONS RESOLVED

| Topic | Initial Thought | Changed To | Final Decision |
|-------|----------------|------------|----------------|
| Add-on "Mark as Paid" | "I think we should have that here on add-ons" | "I guess there's no need to mark it as paid" | NO separate paid status - rolls into payment schedule |
| Original rate changes | "if we add it to the total, then I would just update the Safari rate" | "we will never put these add-ons in the original rate" | NEVER change original rate except for lodge/camp changes |
| Itinerary layout | Stacked (current) | "columns instead of stacking" | COLUMNS - skinnier boxes, more days visible |

---

## NOTES FROM MATT

- "I hope this is the last video. I don't like to use up all your time on videos"
- Will test under his own login after fixes are done
- "Great job, a lot of progress on this"
- Appreciates the work so far

---

## NEXT STEPS

1. Fix transfer expense bug (unblock client testing)
2. Implement add-on → payment calculation sync
3. Auto-adjust payment dates on booking date changes
4. Expand activity log coverage
5. Redesign daily itinerary display
6. Add Tasks to left menu
7. Dashboard improvements
