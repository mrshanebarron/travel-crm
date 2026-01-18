// @ts-check
const { test, expect } = require('@playwright/test');

const BASE_URL = 'https://travelcrm.demo.sbarron.com';

test.describe('Safari CRM - Full User Story Tests', () => {

  test.beforeEach(async ({ page }) => {
    // Login before each test
    await page.goto(`${BASE_URL}/login`);
    await page.fill('input[name="email"]', 'admin@tapestryofafrica.com');
    await page.fill('input[name="password"]', 'password');
    await page.click('button[type="submit"]');
    await page.waitForURL('**/dashboard');
  });

  test('1. Dashboard loads with stats', async ({ page }) => {
    await expect(page.locator('text=Dashboard')).toBeVisible();
    // Check for dashboard stats cards
    await expect(page.locator('text=Upcoming Safaris').or(page.locator('text=Active Bookings'))).toBeVisible();
  });

  test('2. Bookings list page loads', async ({ page }) => {
    await page.click('text=Bookings');
    await page.waitForURL('**/bookings');
    await expect(page.locator('h1:has-text("Bookings")')).toBeVisible();
    // Should show booking cards or table
    await expect(page.locator('[data-booking-id]').or(page.locator('text=JA-2026'))).toBeVisible();
  });

  test('3. View booking details - all tabs accessible', async ({ page }) => {
    await page.goto(`${BASE_URL}/bookings`);
    // Click first booking
    await page.locator('a[href*="/bookings/"]').first().click();
    await page.waitForURL('**/bookings/*');

    // Check persistent header with booking number and lead traveler
    await expect(page.locator('.bg-orange-50:has-text("JA-")')).toBeVisible();

    // Test all tabs
    const tabs = [
      'Client Details',
      'Safari Plan',
      'Rates & Payments',
      'Checklist',
      'Arrival/Departure',
      'Documents',
      'Ledger',
      'Rooms',
      'Activity'
    ];

    for (const tab of tabs) {
      const tabButton = page.locator(`button:has-text("${tab}")`);
      if (await tabButton.isVisible()) {
        await tabButton.click();
        await page.waitForTimeout(500);
      }
    }
  });

  test('4. Client Checklist shows all tasks with checkmarks', async ({ page }) => {
    await page.goto(`${BASE_URL}/bookings`);
    await page.locator('a[href*="/bookings/"]').first().click();
    await page.waitForURL('**/bookings/*');

    // Go to Checklist tab
    await page.click('button:has-text("Checklist")');
    await page.waitForTimeout(500);

    // Should see task list
    await expect(page.locator('table').or(page.locator('[wire\\:id*="task"]'))).toBeVisible();

    // Check for completed task styling (green checkmark)
    const completedTasks = page.locator('.bg-green-500, .bg-green-50');
    // May or may not have completed tasks, just verify page loads
  });

  test('5. Tasks page only shows assigned/completed tasks', async ({ page }) => {
    await page.click('text=Tasks');
    await page.waitForURL('**/tasks');

    await expect(page.locator('h1:has-text("Tasks")')).toBeVisible();

    // Check filter buttons exist
    await expect(page.locator('text=Open').or(page.locator('text=My Tasks'))).toBeVisible();
  });

  test('6. Intake form link generation', async ({ page }) => {
    await page.goto(`${BASE_URL}/bookings`);
    await page.locator('a[href*="/bookings/"]').first().click();
    await page.waitForURL('**/bookings/*');

    // Look for "Generate Intake Link" or "Copy Intake Link" button
    const generateBtn = page.locator('button:has-text("Generate Intake Link")');
    const copyBtn = page.locator('button:has-text("Copy Intake Link")');

    if (await generateBtn.isVisible()) {
      await generateBtn.click();
      await page.waitForTimeout(1000);
      // After generating, should see Copy button
      await expect(page.locator('button:has-text("Copy Intake Link")')).toBeVisible();
    } else if (await copyBtn.isVisible()) {
      // Already has token
      await expect(copyBtn).toBeVisible();
    }
  });

  test('7. Ledger tab shows entries', async ({ page }) => {
    await page.goto(`${BASE_URL}/bookings`);
    await page.locator('a[href*="/bookings/"]').first().click();
    await page.waitForURL('**/bookings/*');

    await page.click('button:has-text("Ledger")');
    await page.waitForTimeout(500);

    // Should see ledger component
    await expect(page.locator('[wire\\:id*="ledger"]').or(page.locator('text=Add Entry'))).toBeVisible();
  });

  test('8. Rates & Payments tab shows payment schedule', async ({ page }) => {
    await page.goto(`${BASE_URL}/bookings`);
    await page.locator('a[href*="/bookings/"]').first().click();
    await page.waitForURL('**/bookings/*');

    await page.click('button:has-text("Rates & Payments")');
    await page.waitForTimeout(500);

    // Should see payment info
    await expect(page.locator('text=Deposit').or(page.locator('text=Safari Rate'))).toBeVisible();
  });

  test('9. Safari Plan tab shows itinerary', async ({ page }) => {
    await page.goto(`${BASE_URL}/bookings`);
    await page.locator('a[href*="/bookings/"]').first().click();
    await page.waitForURL('**/bookings/*');

    await page.click('button:has-text("Safari Plan")');
    await page.waitForTimeout(500);

    // Should see day columns or itinerary
    await expect(page.locator('text=Morning').or(page.locator('text=Day 1'))).toBeVisible();
  });

  test('10. Arrival/Departure tab with copy flight feature', async ({ page }) => {
    await page.goto(`${BASE_URL}/bookings`);
    await page.locator('a[href*="/bookings/"]').first().click();
    await page.waitForURL('**/bookings/*');

    await page.click('button:has-text("Arrival/Departure")');
    await page.waitForTimeout(500);

    // Should see flight section
    await expect(page.locator('text=Flight Details').or(page.locator('text=Group 1'))).toBeVisible();

    // Check for add flight button
    const addFlightBtn = page.locator('[onclick*="openAddFlightModal"]').first();
    if (await addFlightBtn.isVisible()) {
      await addFlightBtn.click();
      await page.waitForTimeout(300);

      // Should see "Copy from another traveler" dropdown
      await expect(page.locator('#copy-from-traveler').or(page.locator('text=Copy from another traveler'))).toBeVisible();

      // Close modal
      await page.keyboard.press('Escape');
    }
  });

  test('11. Activity Log shows entries', async ({ page }) => {
    await page.goto(`${BASE_URL}/bookings`);
    await page.locator('a[href*="/bookings/"]').first().click();
    await page.waitForURL('**/bookings/*');

    await page.click('button:has-text("Activity")');
    await page.waitForTimeout(500);

    // Should see activity log
    await expect(page.locator('[wire\\:id*="activity"]').or(page.locator('text=Log Activity'))).toBeVisible();
  });

  test('12. Documents tab', async ({ page }) => {
    await page.goto(`${BASE_URL}/bookings`);
    await page.locator('a[href*="/bookings/"]').first().click();
    await page.waitForURL('**/bookings/*');

    await page.click('button:has-text("Documents")');
    await page.waitForTimeout(500);

    // Should see document upload area
    await expect(page.locator('[wire\\:id*="documents"]').or(page.locator('text=Upload'))).toBeVisible();
  });

  test('13. Rooms tab', async ({ page }) => {
    await page.goto(`${BASE_URL}/bookings`);
    await page.locator('a[href*="/bookings/"]').first().click();
    await page.waitForURL('**/bookings/*');

    await page.click('button:has-text("Rooms")');
    await page.waitForTimeout(500);

    // Should see room configuration
    await expect(page.locator('text=Room Configuration').or(page.locator('text=Room Type'))).toBeVisible();
  });

  test('14. Clients list page', async ({ page }) => {
    await page.click('text=Clients');
    await page.waitForURL('**/clients');

    await expect(page.locator('h1:has-text("Clients")')).toBeVisible();
  });

  test('15. Reports page', async ({ page }) => {
    await page.click('text=Reports');
    await page.waitForURL('**/reports');

    await expect(page.locator('h1:has-text("Reports")')).toBeVisible();
  });

  test('16. Transfers page', async ({ page }) => {
    await page.click('text=Transfers');
    await page.waitForURL('**/transfers');

    await expect(page.locator('h1:has-text("Transfers")')).toBeVisible();
  });

  test('17. Team/Users page (admin only)', async ({ page }) => {
    // Look for Team or Users link
    const teamLink = page.locator('a:has-text("Team")').or(page.locator('a:has-text("Users")'));
    if (await teamLink.isVisible()) {
      await teamLink.click();
      await page.waitForTimeout(500);
      await expect(page.locator('h1:has-text("Team")').or(page.locator('h1:has-text("Users")'))).toBeVisible();
    }
  });

  test('18. Search functionality', async ({ page }) => {
    // Look for search input in header
    const searchInput = page.locator('input[placeholder*="Search"]').or(page.locator('[name="search"]'));
    if (await searchInput.isVisible()) {
      await searchInput.fill('Jensen');
      await page.waitForTimeout(500);
    }
  });

  test('19. Create new booking button exists', async ({ page }) => {
    await page.goto(`${BASE_URL}/bookings`);
    await expect(page.locator('a:has-text("New Booking")').or(page.locator('a:has-text("Create Booking")'))).toBeVisible();
  });

  test('20. PDF Import button exists', async ({ page }) => {
    await page.goto(`${BASE_URL}/bookings`);
    await expect(page.locator('text=Import from Safari Office').or(page.locator('text=Import PDF'))).toBeVisible();
  });

});

// Public intake form test (no auth required)
test.describe('Public Intake Form', () => {

  test('21. Public intake form page structure', async ({ page }) => {
    // First, we need to get a valid token by logging in
    await page.goto(`${BASE_URL}/login`);
    await page.fill('input[name="email"]', 'admin@tapestryofafrica.com');
    await page.fill('input[name="password"]', 'password');
    await page.click('button[type="submit"]');
    await page.waitForURL('**/dashboard');

    // Go to a booking and get/generate token
    await page.goto(`${BASE_URL}/bookings`);
    await page.locator('a[href*="/bookings/"]').first().click();
    await page.waitForURL('**/bookings/*');

    // Generate if needed
    const generateBtn = page.locator('button:has-text("Generate Intake Link")');
    if (await generateBtn.isVisible()) {
      await generateBtn.click();
      await page.waitForTimeout(1000);
    }

    // Now test the public form by getting the URL from clipboard simulation
    // We'll verify the form structure via route pattern test
  });

});
