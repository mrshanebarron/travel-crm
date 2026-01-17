import { chromium } from 'playwright';
import fs from 'fs';
import path from 'path';

const BASE_URL = 'https://travel-crm.test';
const SCREENSHOTS_DIR = './tests/screenshots';

const results = { passed: [], failed: [], timestamp: new Date().toISOString() };

async function screenshot(page, name) {
    const filepath = path.join(SCREENSHOTS_DIR, `${name}.png`);
    await page.screenshot({ path: filepath, fullPage: true });
    console.log(`  Screenshot: ${name}.png`);
}

async function test(name, fn) {
    console.log(`\nTesting: ${name}`);
    try {
        await fn();
        results.passed.push(name);
        console.log(`  ✓ PASSED`);
    } catch (error) {
        results.failed.push({ name, error: error.message });
        console.log(`  ✗ FAILED: ${error.message}`);
    }
}

async function login(page) {
    await page.goto(`${BASE_URL}/login`);
    await page.click('button[type="submit"]');
    await page.waitForURL(`${BASE_URL}/dashboard`);
    console.log('  Logged in successfully');
}

async function runTests() {
    console.log('BOOKING DETAIL PAGE TESTS');
    console.log('='.repeat(40));

    const browser = await chromium.launch({ headless: true });
    const context = await browser.newContext({
        viewport: { width: 1920, height: 1080 },
        ignoreHTTPSErrors: true
    });
    const page = await context.newPage();

    await login(page);

    // First, navigate to a booking detail page
    await test('Booking Detail - Navigate to booking', async () => {
        // Go to bookings list first
        await page.goto(`${BASE_URL}/bookings`);
        await page.waitForSelector('h1:has-text("Bookings")');
        await page.waitForTimeout(500);

        // Click on a View button to go to detail page
        const viewBtn = await page.locator('table a:has-text("View")').first();
        if (await viewBtn.isVisible()) {
            await viewBtn.click();
            await page.waitForURL(/\/bookings\/\d+$/);
            await screenshot(page, 'booking-detail-01-page');
        } else {
            throw new Error('No View button found');
        }
    });

    await test('Booking Detail - Header shows booking info', async () => {
        // Check header has booking number (JA-20XX-XXX format)
        const bookingNumber = await page.locator('h1').textContent();
        if (!bookingNumber.match(/[A-Z]{2}-\d{4}-\d{3}/)) {
            throw new Error(`Booking number format unexpected: ${bookingNumber}`);
        }

        // Check status badge is visible
        const statusBadge = await page.locator('.badge').first().isVisible();
        if (!statusBadge) throw new Error('Status badge not visible');

        await screenshot(page, 'booking-detail-02-header');
    });

    await test('Booking Detail - Tab navigation visible', async () => {
        const clientTab = await page.locator('button[data-tab="client-details"]').isVisible();
        const safariTab = await page.locator('button[data-tab="safari-plan"]').isVisible();
        const checklistTab = await page.locator('button[data-tab="master-checklist"]').isVisible();
        const documentsTab = await page.locator('button[data-tab="documents"]').isVisible();

        if (!clientTab) throw new Error('Client Details tab not visible');
        if (!safariTab) throw new Error('Safari Plan tab not visible');
        if (!checklistTab) throw new Error('Checklist tab not visible');
        if (!documentsTab) throw new Error('Documents tab not visible');

        await screenshot(page, 'booking-detail-03-tabs');
    });

    await test('Booking Detail - Client Details tab (default)', async () => {
        // This should be the default active tab
        const activeTab = await page.locator('button.tab.active').getAttribute('data-tab');
        if (activeTab !== 'client-details') {
            throw new Error(`Expected client-details tab, got ${activeTab}`);
        }

        // Check for traveler info section
        const travelerSection = await page.locator('text=Travelers').first().isVisible() ||
                               await page.locator('text=Lead Traveler').first().isVisible();

        await screenshot(page, 'booking-detail-04-client-details');
    });

    await test('Booking Detail - Navigate to Safari Plan tab', async () => {
        await page.click('button[data-tab="safari-plan"]');
        await page.waitForTimeout(300);

        // Check tab is now active
        const isActive = await page.locator('button[data-tab="safari-plan"].active').isVisible();
        if (!isActive) throw new Error('Safari Plan tab not active');

        await screenshot(page, 'booking-detail-05-safari-plan');
    });

    await test('Booking Detail - Navigate to Checklist tab', async () => {
        await page.click('button[data-tab="master-checklist"]');
        await page.waitForTimeout(300);

        const isActive = await page.locator('button[data-tab="master-checklist"].active').isVisible();
        if (!isActive) throw new Error('Checklist tab not active');

        // Check for task list content (BookingTaskList component)
        await screenshot(page, 'booking-detail-06-checklist');
    });

    await test('Booking Detail - Checklist has tasks', async () => {
        // Look for task items or empty state
        const taskCount = await page.locator('[wire\\:key*="task-"]').count();
        console.log(`  Found ${taskCount} tasks`);

        // Should have tasks from default task creation
        if (taskCount === 0) {
            // Check for any task-related content
            const hasTaskContent = await page.locator('text=No tasks').isVisible() ||
                                   await page.locator('input[type="checkbox"]').count() > 0;
            if (!hasTaskContent) throw new Error('No task content found');
        }

        await screenshot(page, 'booking-detail-07-tasks');
    });

    await test('Booking Detail - Navigate to Documents tab', async () => {
        await page.click('button[data-tab="documents"]');
        await page.waitForTimeout(300);

        const isActive = await page.locator('button[data-tab="documents"].active').isVisible();
        if (!isActive) throw new Error('Documents tab not active');

        // Check for upload button or document list
        const uploadArea = await page.locator('text=Upload').first().isVisible() ||
                          await page.locator('input[type="file"]').first().isVisible();

        await screenshot(page, 'booking-detail-08-documents');
    });

    await test('Booking Detail - Navigate to Rates & Payments tab', async () => {
        await page.click('button[data-tab="payment-details"]');
        await page.waitForTimeout(300);

        const isActive = await page.locator('button[data-tab="payment-details"].active').isVisible();
        if (!isActive) throw new Error('Rates & Payments tab not active');

        await screenshot(page, 'booking-detail-09-payments');
    });

    await test('Booking Detail - Navigate to Ledger tab', async () => {
        await page.click('button[data-tab="ledger"]');
        await page.waitForTimeout(300);

        const isActive = await page.locator('button[data-tab="ledger"].active').isVisible();
        if (!isActive) throw new Error('Ledger tab not active');

        // Check for ledger content (BookingLedger component)
        await screenshot(page, 'booking-detail-10-ledger');
    });

    await test('Booking Detail - Navigate to Activity tab', async () => {
        await page.click('button[data-tab="activity-log"]');
        await page.waitForTimeout(300);

        const isActive = await page.locator('button[data-tab="activity-log"].active').isVisible();
        if (!isActive) throw new Error('Activity tab not active');

        // Check for activity log content (BookingActivityLog component)
        await screenshot(page, 'booking-detail-11-activity');
    });

    await test('Booking Detail - Navigate to Arrival/Departure tab', async () => {
        await page.click('button[data-tab="arrival-departure"]');
        await page.waitForTimeout(300);

        const isActive = await page.locator('button[data-tab="arrival-departure"].active').isVisible();
        if (!isActive) throw new Error('Arrival/Departure tab not active');

        await screenshot(page, 'booking-detail-12-arrival-departure');
    });

    await test('Booking Detail - Navigate to Rooms tab', async () => {
        await page.click('button[data-tab="rooms"]');
        await page.waitForTimeout(300);

        const isActive = await page.locator('button[data-tab="rooms"].active').isVisible();
        if (!isActive) throw new Error('Rooms tab not active');

        await screenshot(page, 'booking-detail-13-rooms');
    });

    await test('Booking Detail - Edit button works', async () => {
        // Click the Edit button in the header
        const editBtn = await page.locator('a:has-text("Edit")').first();
        if (await editBtn.isVisible()) {
            await editBtn.click();
            await page.waitForURL(/\/bookings\/\d+\/edit$/);
            await screenshot(page, 'booking-detail-14-edit-page');
        } else {
            throw new Error('Edit button not found');
        }
    });

    await browser.close();

    fs.writeFileSync(
        path.join(SCREENSHOTS_DIR, 'booking-detail-results.json'),
        JSON.stringify(results, null, 2)
    );

    console.log('\n' + '='.repeat(40));
    console.log(`Passed: ${results.passed.length}, Failed: ${results.failed.length}`);
    if (results.failed.length > 0) {
        results.failed.forEach(f => console.log(`  FAILED: ${f.name} - ${f.error}`));
    }
}

runTests().catch(console.error);
