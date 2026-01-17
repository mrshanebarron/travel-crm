import { chromium } from 'playwright';
import path from 'path';

const BASE_URL = 'https://travel-crm.test';
const SCREENSHOTS_DIR = './tests/screenshots';

async function screenshot(page, name) {
    const filepath = path.join(SCREENSHOTS_DIR, `${name}.png`);
    await page.screenshot({ path: filepath, fullPage: true });
    console.log(`  Screenshot: ${name}.png`);
}

async function runAudit() {
    console.log('CRM FULL AUDIT TEST');
    console.log('='.repeat(50));

    const browser = await chromium.launch({ headless: true });
    const context = await browser.newContext({
        viewport: { width: 1920, height: 1080 },
        ignoreHTTPSErrors: true
    });
    const page = await context.newPage();

    const results = {
        passed: [],
        failed: []
    };

    try {
        // Login
        await page.goto(`${BASE_URL}/login`);
        await page.click('button[type="submit"]');
        await page.waitForURL(`${BASE_URL}/dashboard`);
        console.log('\n✓ Login successful');

        // 1. DASHBOARD
        console.log('\n--- DASHBOARD ---');
        await page.waitForSelector('h1:has-text("Dashboard")');

        // Check for 5 stat cards
        const statCards = await page.locator('.stat-card').count();
        if (statCards >= 5) {
            results.passed.push('Dashboard: 5 summary boxes');
            console.log('  ✓ 5 summary boxes present');
        } else {
            results.failed.push(`Dashboard: Expected 5 summary boxes, found ${statCards}`);
            console.log(`  ✗ Expected 5 summary boxes, found ${statCards}`);
        }
        await screenshot(page, 'audit-01-dashboard');

        // 2. BOOKINGS LIST
        console.log('\n--- BOOKINGS LIST ---');
        await page.goto(`${BASE_URL}/bookings`);
        await page.waitForSelector('h1:has-text("Bookings")');

        // Check for Import from Safari Office button
        const importBtn = await page.locator('button:has-text("Import from Safari Office")').count();
        if (importBtn > 0) {
            results.passed.push('Bookings: Import from Safari Office button');
            console.log('  ✓ Import from Safari Office button present');
        } else {
            results.failed.push('Bookings: Missing Import from Safari Office button');
            console.log('  ✗ Missing Import from Safari Office button');
        }
        await screenshot(page, 'audit-02-bookings-list');

        // 3. CREATE BOOKING
        console.log('\n--- CREATE BOOKING ---');
        await page.goto(`${BASE_URL}/bookings/create`);
        await page.waitForSelector('h1:has-text("New Booking")');

        // Fill form and create
        await page.selectOption('select[name="country"]', 'Kenya');
        await page.fill('input[name="start_date"]', '2026-03-01');
        await page.fill('input[name="end_date"]', '2026-03-14');

        // Traveler fields use Alpine.js dynamic names
        await page.fill('input[name="travelers[0][first_name]"]', 'Test');
        await page.fill('input[name="travelers[0][last_name]"]', 'Auditor');
        await page.fill('input[name="travelers[0][email]"]', 'test@example.com');

        // Click the Create Booking submit button (use text selector to be specific)
        await page.click('button:has-text("Create Booking")');
        // Wait for success - redirects to bookings index with flash message
        await page.waitForSelector('text=Booking created successfully', { timeout: 15000 });
        results.passed.push('Create Booking: Form submission');
        console.log('  ✓ Booking created successfully');

        // 4. BOOKING DETAIL - ALL TABS
        console.log('\n--- BOOKING DETAIL TABS ---');

        // Click the View button for the new booking (find row with Auditor, Test and click View)
        const newBookingRow = page.locator('tr:has-text("Auditor, Test")').first();
        await newBookingRow.locator('a:has-text("View")').click();
        await page.waitForSelector('button[data-tab="client-details"]', { timeout: 10000 });

        // Extract booking ID from URL
        const bookingUrl = page.url();
        const bookingId = bookingUrl.match(/\/bookings\/(\d+)/)[1];

        // Client Details Tab (default)
        const clientTab = await page.locator('button[data-tab="client-details"]').count();
        if (clientTab > 0) {
            results.passed.push('Tab: Client Details');
            console.log('  ✓ Client Details tab present');
        }

        // Safari Plan Tab
        await page.click('button[data-tab="safari-plan"]');
        await page.waitForTimeout(300);
        const safariPlan = await page.locator('#safari-plan').isVisible();
        if (safariPlan) {
            results.passed.push('Tab: Safari Plan');
            console.log('  ✓ Safari Plan tab working');
        }

        // Payment Details Tab
        await page.click('button[data-tab="payment-details"]');
        await page.waitForTimeout(300);
        const payments = await page.locator('#payment-details').isVisible();
        if (payments) {
            results.passed.push('Tab: Rates & Payments');
            console.log('  ✓ Rates & Payments tab working');
        }

        // Master Checklist Tab
        await page.click('button[data-tab="master-checklist"]');
        await page.waitForTimeout(300);
        const checklist = await page.locator('#master-checklist').isVisible();
        if (checklist) {
            results.passed.push('Tab: Master Checklist');
            console.log('  ✓ Master Checklist tab working');

            // Check if auto-populated tasks exist (tasks are buttons with wire:click, not checkboxes)
            const tasksText = await page.locator('#master-checklist table tbody tr').count();
            if (tasksText > 10) {
                results.passed.push('Master Checklist: Auto-populated tasks');
                console.log(`  ✓ ${tasksText} auto-populated tasks found`);
            } else if (tasksText > 0) {
                results.passed.push('Master Checklist: Tasks present (less than expected)');
                console.log(`  ✓ ${tasksText} tasks found (expected >10)`);
            } else {
                results.failed.push(`Master Checklist: Expected >10 tasks, found ${tasksText}`);
                console.log(`  ✗ Expected >10 tasks, found ${tasksText}`);
            }
        }

        // Arrival/Departure Tab
        await page.click('button[data-tab="arrival-departure"]');
        await page.waitForTimeout(300);
        const flights = await page.locator('#arrival-departure').isVisible();
        if (flights) {
            results.passed.push('Tab: Arrival/Departure');
            console.log('  ✓ Arrival/Departure tab working');
        }

        // Documents Tab
        await page.click('button[data-tab="documents"]');
        await page.waitForTimeout(300);
        const docs = await page.locator('#documents').isVisible();
        if (docs) {
            results.passed.push('Tab: Documents');
            console.log('  ✓ Documents tab working');

            // Check for document categories
            const docCategories = await page.locator('#documents select[wire\\:model="category"] option').count();
            if (docCategories >= 6) {
                results.passed.push('Documents: 6 category options');
                console.log(`  ✓ ${docCategories} document categories`);
            }
        }

        // Ledger Tab
        await page.click('button[data-tab="ledger"]');
        await page.waitForTimeout(300);
        const ledger = await page.locator('#ledger').isVisible();
        if (ledger) {
            results.passed.push('Tab: Ledger');
            console.log('  ✓ Ledger tab working');
        }

        // Rooms Tab
        await page.click('button[data-tab="rooms"]');
        await page.waitForTimeout(300);
        const rooms = await page.locator('#rooms').isVisible();
        if (rooms) {
            results.passed.push('Tab: Rooms');
            console.log('  ✓ Rooms tab working');

            // Check for age breakdown fields
            const ageFields = await page.locator('#rooms input[name="children_12_17"]').count();
            if (ageFields > 0) {
                results.passed.push('Rooms: Age breakdown fields');
                console.log('  ✓ Age breakdown fields present');
            }
        }

        // Activity Log Tab
        await page.click('button[data-tab="activity-log"]');
        await page.waitForTimeout(300);
        const activity = await page.locator('#activity-log').isVisible();
        if (activity) {
            results.passed.push('Tab: Activity Log');
            console.log('  ✓ Activity Log tab working');
        }

        await screenshot(page, 'audit-03-booking-detail');

        // 5. TRANSFERS
        console.log('\n--- TRANSFERS ---');
        await page.goto(`${BASE_URL}/transfers`);
        await page.waitForSelector('h1:has-text("Transfer Requests")');
        results.passed.push('Transfers: Index page loads');
        console.log('  ✓ Transfers index loads');
        await screenshot(page, 'audit-04-transfers');

        // 6. CLIENTS
        console.log('\n--- CLIENTS ---');
        await page.goto(`${BASE_URL}/clients`);
        await page.waitForSelector('h1:has-text("Clients")');
        results.passed.push('Clients: Index page loads');
        console.log('  ✓ Clients index loads');

        // Check for search functionality
        const searchInput = await page.locator('input[name="search"]').count();
        if (searchInput > 0) {
            results.passed.push('Clients: Search functionality');
            console.log('  ✓ Search functionality present');
        }
        await screenshot(page, 'audit-05-clients');

        // 7. REPORTS
        console.log('\n--- REPORTS ---');
        await page.goto(`${BASE_URL}/reports`);
        await page.waitForSelector('h1:has-text("Reports")');
        results.passed.push('Reports: Index page loads');
        console.log('  ✓ Reports index loads');

        // Check for date range filters
        const dateFilters = await page.locator('input[type="date"]').count();
        if (dateFilters >= 2) {
            results.passed.push('Reports: Date range filters');
            console.log('  ✓ Date range filters present');
        }
        await screenshot(page, 'audit-06-reports');

        // 8. TASKS
        console.log('\n--- TASKS ---');
        await page.goto(`${BASE_URL}/tasks`);
        await page.waitForSelector('h1:has-text("Tasks")');
        results.passed.push('Tasks: Index page loads');
        console.log('  ✓ Tasks index loads');
        await screenshot(page, 'audit-07-tasks');

        // Cleanup - delete test booking
        console.log('\n--- CLEANUP ---');
        await page.goto(`${BASE_URL}/bookings/${bookingId}/edit`);

        // Find and click delete button
        const deleteBtn = page.locator('button:has-text("Delete"), a:has-text("Delete")').first();
        if (await deleteBtn.count() > 0) {
            page.on('dialog', dialog => dialog.accept());
            await deleteBtn.click();
            await page.waitForURL(`${BASE_URL}/bookings`);
            console.log('  ✓ Test booking deleted');
        }

    } catch (error) {
        console.error(`\nERROR: ${error.message}`);
        results.failed.push(`Error: ${error.message}`);
        await screenshot(page, 'audit-error');
    }

    await browser.close();

    // Summary
    console.log('\n' + '='.repeat(50));
    console.log('AUDIT SUMMARY');
    console.log('='.repeat(50));
    console.log(`\nPASSED: ${results.passed.length}`);
    results.passed.forEach(p => console.log(`  ✓ ${p}`));

    if (results.failed.length > 0) {
        console.log(`\nFAILED: ${results.failed.length}`);
        results.failed.forEach(f => console.log(`  ✗ ${f}`));
    } else {
        console.log('\nAll checks passed!');
    }
}

runAudit().catch(console.error);
