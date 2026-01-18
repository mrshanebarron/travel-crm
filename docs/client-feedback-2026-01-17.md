# Safari CRM - Client Feedback
**Date:** 2026-01-17
**Client:** Dr. Matthew Jensen
**Source:** WhatsApp Messages

---

## ISSUES REPORTED

### 1. Mom Cox Deposit Still Incorrect
- On the Adam Cox booking, the deposit for Mom Cox didn't change back to 25%
- Matt is asking if he needs to login as superadmin to make changes to locked portions
- He logged in under his own credentials but doesn't see how to make changes to the deposit info

**Status:** Previously manually corrected, but Matt is seeing old/incorrect values

---

## PDF IMPORT ENHANCEMENTS

### Safari Office Time Slot Mapping
Safari Office uses **9 time divisions**:
1. Early Morning
2. Morning
3. Late Morning
4. Midday
5. Early Afternoon
6. Afternoon
7. Late Afternoon
8. Evening
9. Late Evening

CRM uses **4 time slots**:
1. Morning
2. Midday
3. Afternoon
4. Evening

**Requested Mapping Logic:**
- Early Morning, Morning, Late Morning → **Morning** (preserve chronological order)
- Midday → **Midday**
- Early Afternoon, Afternoon, Late Afternoon → **Afternoon** (preserve chronological order)
- Evening, Late Evening → **Evening** (preserve chronological order)

**Example:** Hot air balloon rides happen in Early Morning, followed by a game drive in Morning. Both should appear in the "Morning" slot, but balloon ride listed FIRST to preserve order.

### Import Day Activities
- Can we pull each day's activities from the PDF into the Safari Plan?
- Reference: Cox Booking.pdf shows Activities Day 1 with Midday, Early Afternoon, Afternoon, Evening slots

---

## TASKS PAGE FILTER LOGIC

**Current Issue:** Tasks page showing future tasks that haven't "activated" yet

**Correct Behavior:**
- Only show tasks that are **due today or past due**
- Do NOT show future tasks until their due date arrives
- Example: "Collect final payment" task should NOT appear until 45 days before departure, even if it's already assigned in the system

---

## TRANSFER WORKFLOW (Complete Specification)

Matt clarified the exact transfer workflow step-by-step:

### Step 1: Transfer Request Creation
- When creating a transfer request with expenses
- Button should be labeled "**Send**" (not "Save")

### Step 2: Send Creates Task for Matt
- Clicking "Send" auto-generates task assigned to **Matt**: "Make transfer"

### Step 3: Matt Completes Transfer
- Matt marks his task as Complete

### Step 4: Auto-Create Task for Hilda
- Completing Matt's task auto-creates task for **Hilda**: "Transfer completed – make payments"

### Step 5: Hilda Completes Payments
- Hilda marks her task as Complete

### Step 6: Expenses Post to Ledgers
- System auto-populates all associated expenses
- Apply them to correct booking ledgers
- Ensure each expense assigned to appropriate client account

**Summary Chain:**
```
Send transfer request → Task for Matt → Task for Hilda → Expenses flow into client ledgers
```

---

## ACTION ITEMS

- [ ] Verify Mom Cox deposit is correctly showing 25% ($1,523.75)
- [ ] Update PDF import to map 9 Safari Office slots → 4 CRM slots (preserving order)
- [ ] Fix Tasks page to only show due today or past due
- [ ] Implement Transfer workflow: Send button → Matt task → Hilda task → Ledger posting

---

## Matt's Notes
- "If you need to make a video to better explain any of this just let me know..."
- Wants to ensure we are "fully aligned" on Transfer workflow
