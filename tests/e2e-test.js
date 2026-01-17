import { chromium } from 'playwright';
import fs from 'fs';
import path from 'path';

const BASE_URL = 'https://travel-crm.test';
const SCREENSHOTS_DIR = './tests/screenshots';

// Test results tracking
const results = {
    passed: [],
    failed: [],
    timestamp: new Date().toISOString()
};

async function screenshot(page, name) {
    const filepath = path.join(SCREENSHOTS_DIR, `${name}.png`);
    await page.screenshot({ path: filepath, fullPage: true });
    console.log(`  Screenshot: ${name}.png`);
    return filepath;
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
    await page.fill('input[name="email"]', 'admin@example.com');
    await page.fill('input[name="password"]', 'password');
    await page.click('button[type="submit"]');
    await page.waitForURL(`${BASE_URL}/**`);
    console.log('  Logged in successfully');
}

async function runTests() {
    console.log('='.repeat(60));
    console.log('TRAVEL CRM - COMPREHENSIVE E2E TESTS');
    console.log('='.repeat(60));

    const browser = await chromium.launch({ headless: true });
    const context = await browser.newContext({
        viewport: { width: 1920, height: 1080 },
        ignoreHTTPSErrors: true
    });
    const page = await context.newPage();

    // Login first
    await login(page);
    await screenshot(page, '00-dashboard');

    // ============================================
    // BOOKINGS PAGE TESTS
    // ============================================
    console.log('\n' + '='.repeat(40));
    console.log('BOOKINGS PAGE');
    console.log('='.repeat(40));

    await test('Bookings - Page loads', async () => {
        await page.goto(`${BASE_URL}/bookings`);
        await page.waitForSelector('h1:has-text("Bookings")');
        await screenshot(page, '01-bookings-list');
    });

    await test('Bookings - Status tab filtering', async () => {
        await page.goto(`${BASE_URL}/bookings`);
        // Click Upcoming tab
        await page.click('button:has-text("Upcoming")');
        await page.waitForTimeout(500);
        await screenshot(page, '02-bookings-upcoming-filter');

        // Click All tab
        await page.click('button:has-text("All")');
        await page.waitForTimeout(500);
    });

    await test('Bookings - Open create modal', async () => {
        await page.goto(`${BASE_URL}/bookings`);
        await page.click('button:has-text("New Booking")');
        await page.waitForSelector('h3:has-text("Create New Booking")');
        await screenshot(page, '03-bookings-create-modal');
    });

    await test('Bookings - Add traveler in modal', async () => {
        // Modal should still be open from previous test
        await page.click('button:has-text("Add Traveler")');
        await page.waitForTimeout(300);
        await screenshot(page, '04-bookings-add-traveler');
    });

    await test('Bookings - Close create modal', async () => {
        await page.click('button:has-text("Cancel")');
        await page.waitForTimeout(300);
    });

    await test('Bookings - Create new booking', async () => {
        await page.goto(`${BASE_URL}/bookings`);
        await page.click('button:has-text("New Booking")');
        await page.waitForSelector('h3:has-text("Create New Booking")');

        // Fill form
        await page.fill('input[wire\\:model="country"]', 'Kenya');
        await page.fill('input[wire\\:model="startDate"]', '2026-03-01');
        await page.fill('input[wire\\:model="endDate"]', '2026-03-10');
        await page.fill('input[wire\\:model="travelers.0.first_name"]', 'Test');
        await page.fill('input[wire\\:model="travelers.0.last_name"]', 'User');
        await page.fill('input[wire\\:model="travelers.0.email"]', 'test@example.com');

        await screenshot(page, '05-bookings-create-filled');

        // Submit
        await page.click('button:has-text("Create Booking")');
        await page.waitForURL(`${BASE_URL}/bookings/*`);
        await screenshot(page, '06-booking-created-detail');
    });

    // ============================================
    // BOOKING DETAIL PAGE TESTS
    // ============================================
    console.log('\n' + '='.repeat(40));
    console.log('BOOKING DETAIL PAGE');
    console.log('='.repeat(40));

    await test('Booking Detail - Tasks tab loads', async () => {
        // Should be on booking detail page from creation
        await page.click('button:has-text("Tasks")');
        await page.waitForTimeout(500);
        await screenshot(page, '07-booking-tasks-tab');
    });

    await test('Booking Detail - Add task modal', async () => {
        await page.click('button:has-text("Add Task")');
        await page.waitForSelector('h3:has-text("Add Task")');
        await screenshot(page, '08-booking-add-task-modal');
        await page.click('button:has-text("Cancel")');
    });

    await test('Booking Detail - Toggle task complete', async () => {
        // Find a task checkbox and click it
        const taskCheckbox = await page.locator('button[title="Mark complete"]').first();
        if (await taskCheckbox.isVisible()) {
            await taskCheckbox.click();
            await page.waitForTimeout(500);
            await screenshot(page, '09-booking-task-completed');
        }
    });

    await test('Booking Detail - Ledger tab loads', async () => {
        await page.click('button:has-text("Ledger")');
        await page.waitForTimeout(500);
        await screenshot(page, '10-booking-ledger-tab');
    });

    await test('Booking Detail - Documents tab loads', async () => {
        await page.click('button:has-text("Documents")');
        await page.waitForTimeout(500);
        await screenshot(page, '11-booking-documents-tab');
    });

    await test('Booking Detail - Activity tab loads', async () => {
        await page.click('button:has-text("Activity")');
        await page.waitForTimeout(500);
        await screenshot(page, '12-booking-activity-tab');
    });

    // ============================================
    // VENDORS PAGE TESTS
    // ============================================
    console.log('\n' + '='.repeat(40));
    console.log('VENDORS PAGE');
    console.log('='.repeat(40));

    await test('Vendors - Page loads', async () => {
        await page.goto(`${BASE_URL}/vendors`);
        await page.waitForSelector('h1:has-text("Vendors")');
        await screenshot(page, '13-vendors-list');
    });

    await test('Vendors - Search filtering', async () => {
        await page.fill('input[wire\\:model\\.live\\.debounce\\.300ms="search"]', 'Serena');
        await page.waitForTimeout(500);
        await screenshot(page, '14-vendors-search');
        await page.fill('input[wire\\:model\\.live\\.debounce\\.300ms="search"]', '');
    });

    await test('Vendors - Category filter', async () => {
        await page.selectOption('select[wire\\:model\\.live="category"]', 'Lodge');
        await page.waitForTimeout(500);
        await screenshot(page, '15-vendors-category-filter');
        await page.selectOption('select[wire\\:model\\.live="category"]', '');
    });

    await test('Vendors - Open create modal', async () => {
        await page.click('button:has-text("Add Vendor")');
        await page.waitForSelector('h3:has-text("Add New Vendor")');
        await screenshot(page, '16-vendors-create-modal');
    });

    await test('Vendors - Create new vendor', async () => {
        // Fill required fields
        await page.fill('input[wire\\:model="name"]', 'Test Safari Lodge');
        await page.selectOption('select[wire\\:model="vendorCategory"]', 'Lodge');
        await page.fill('input[wire\\:model="country"]', 'Kenya');
        await page.fill('input[wire\\:model="contactName"]', 'John Manager');
        await page.fill('input[wire\\:model="email"]', 'lodge@test.com');

        await screenshot(page, '17-vendors-create-filled');

        // Submit
        await page.click('button:has-text("Create Vendor")');
        await page.waitForURL(`${BASE_URL}/vendors/*`);
        await screenshot(page, '18-vendor-created-detail');
    });

    // ============================================
    // CLIENTS PAGE TESTS
    // ============================================
    console.log('\n' + '='.repeat(40));
    console.log('CLIENTS PAGE');
    console.log('='.repeat(40));

    await test('Clients - Page loads', async () => {
        await page.goto(`${BASE_URL}/clients`);
        await page.waitForSelector('h1:has-text("Clients")');
        await screenshot(page, '19-clients-list');
    });

    await test('Clients - Search filtering', async () => {
        await page.fill('input[wire\\:model\\.live\\.debounce\\.300ms="search"]', 'Test');
        await page.waitForTimeout(500);
        await screenshot(page, '20-clients-search');
    });

    await test('Clients - View client modal', async () => {
        // Clear search first to see all clients
        await page.fill('input[wire\\:model\\.live\\.debounce\\.300ms="search"]', '');
        await page.waitForTimeout(500);

        // Click first view button
        const viewBtn = await page.locator('button:has-text("View")').first();
        if (await viewBtn.isVisible()) {
            await viewBtn.click();
            await page.waitForSelector('text=Payment Information');
            await screenshot(page, '21-clients-view-modal');

            // Close modal
            const closeBtn = await page.locator('button:has-text("Close")');
            if (await closeBtn.isVisible()) {
                await closeBtn.click();
            }
        }
    });

    await test('Clients - Edit client modal', async () => {
        const editBtn = await page.locator('[wire\\:click*="openEditModal"]').first();
        if (await editBtn.isVisible()) {
            await editBtn.click();
            await page.waitForSelector('h3:has-text("Edit Client")');
            await screenshot(page, '22-clients-edit-modal');
            await page.click('button:has-text("Cancel")');
        }
    });

    // ============================================
    // TRANSFERS PAGE TESTS
    // ============================================
    console.log('\n' + '='.repeat(40));
    console.log('TRANSFERS PAGE');
    console.log('='.repeat(40));

    await test('Transfers - Page loads', async () => {
        await page.goto(`${BASE_URL}/transfers`);
        await page.waitForSelector('h1:has-text("Transfers")');
        await screenshot(page, '23-transfers-list');
    });

    await test('Transfers - Status tab filtering', async () => {
        // Click Draft tab
        await page.click('button:has-text("Draft")');
        await page.waitForTimeout(500);
        await screenshot(page, '24-transfers-draft-filter');

        // Click All tab
        await page.click('button:has-text("All")');
        await page.waitForTimeout(500);
    });

    // ============================================
    // USERS PAGE TESTS
    // ============================================
    console.log('\n' + '='.repeat(40));
    console.log('USERS PAGE');
    console.log('='.repeat(40));

    await test('Users - Page loads', async () => {
        await page.goto(`${BASE_URL}/users`);
        await page.waitForSelector('h1:has-text("User Management")');
        await screenshot(page, '25-users-list');
    });

    await test('Users - Open add user modal', async () => {
        await page.click('button:has-text("Add User")');
        await page.waitForSelector('h3:has-text("Add User")');
        await screenshot(page, '26-users-add-modal');
        await page.click('button:has-text("Cancel")');
    });

    await test('Users - Open edit user modal', async () => {
        const editBtn = await page.locator('[wire\\:click*="openEditModal"]').first();
        if (await editBtn.isVisible()) {
            await editBtn.click();
            await page.waitForSelector('h3:has-text("Edit User")');
            await screenshot(page, '27-users-edit-modal');
            await page.click('button:has-text("Cancel")');
        }
    });

    // ============================================
    // TASKS PAGE TESTS
    // ============================================
    console.log('\n' + '='.repeat(40));
    console.log('TASKS PAGE');
    console.log('='.repeat(40));

    await test('Tasks - Page loads', async () => {
        await page.goto(`${BASE_URL}/tasks`);
        await page.waitForSelector('h1:has-text("Tasks")');
        await screenshot(page, '28-tasks-list');
    });

    await test('Tasks - Filter tabs', async () => {
        // Click Mine tab
        const mineTab = await page.locator('button:has-text("Mine")');
        if (await mineTab.isVisible()) {
            await mineTab.click();
            await page.waitForTimeout(500);
            await screenshot(page, '29-tasks-mine-filter');
        }

        // Click Open tab
        await page.click('button:has-text("Open")');
        await page.waitForTimeout(500);
    });

    await test('Tasks - Open create modal', async () => {
        await page.click('button:has-text("Create Task")');
        await page.waitForSelector('h3:has-text("Create Task")');
        await screenshot(page, '30-tasks-create-modal');
        await page.click('button:has-text("Cancel")');
    });

    // ============================================
    // DASHBOARD TESTS
    // ============================================
    console.log('\n' + '='.repeat(40));
    console.log('DASHBOARD');
    console.log('='.repeat(40));

    await test('Dashboard - Page loads', async () => {
        await page.goto(`${BASE_URL}/dashboard`);
        await page.waitForSelector('text=Dashboard');
        await screenshot(page, '31-dashboard');
    });

    // Cleanup and report
    await browser.close();

    // Print summary
    console.log('\n' + '='.repeat(60));
    console.log('TEST SUMMARY');
    console.log('='.repeat(60));
    console.log(`Passed: ${results.passed.length}`);
    console.log(`Failed: ${results.failed.length}`);

    if (results.failed.length > 0) {
        console.log('\nFailed tests:');
        results.failed.forEach(f => {
            console.log(`  - ${f.name}: ${f.error}`);
        });
    }

    // Save results to file
    fs.writeFileSync(
        path.join(SCREENSHOTS_DIR, 'test-results.json'),
        JSON.stringify(results, null, 2)
    );
    console.log(`\nResults saved to ${SCREENSHOTS_DIR}/test-results.json`);
    console.log(`Screenshots saved to ${SCREENSHOTS_DIR}/`);
}

runTests().catch(console.error);
