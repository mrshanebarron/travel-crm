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
    console.log('BOOKINGS PAGE TESTS');
    console.log('='.repeat(40));

    const browser = await chromium.launch({ headless: true });
    const context = await browser.newContext({
        viewport: { width: 1920, height: 1080 },
        ignoreHTTPSErrors: true
    });
    const page = await context.newPage();

    await login(page);

    await test('Bookings - Page loads', async () => {
        await page.goto(`${BASE_URL}/bookings`);
        await page.waitForSelector('h1:has-text("Bookings")');
        await screenshot(page, 'bookings-01-list');
    });

    await test('Bookings - Status tab filtering (Upcoming)', async () => {
        await page.click('text=Upcoming');
        await page.waitForTimeout(500);
        await screenshot(page, 'bookings-02-upcoming');
    });

    await test('Bookings - Status tab filtering (Active)', async () => {
        await page.click('text=Active');
        await page.waitForTimeout(500);
        await screenshot(page, 'bookings-03-active');
    });

    await test('Bookings - Status tab filtering (Completed)', async () => {
        await page.click('text=Completed');
        await page.waitForTimeout(500);
        await screenshot(page, 'bookings-04-completed');
    });

    await test('Bookings - Status tab filtering (All)', async () => {
        await page.click('button:has-text("All")');
        await page.waitForTimeout(500);
        await screenshot(page, 'bookings-05-all');
    });

    await test('Bookings - Open create modal', async () => {
        await page.click('button:has-text("New Booking")');
        await page.waitForSelector('h3:has-text("New Booking")');
        await screenshot(page, 'bookings-06-create-modal');
    });

    await test('Bookings - Add traveler in modal', async () => {
        await page.click('text=+ Add Traveler');
        await page.waitForTimeout(300);
        await screenshot(page, 'bookings-07-add-traveler');
    });

    await test('Bookings - Remove traveler in modal', async () => {
        // The remove X button has text-red-500 class and is inside the traveler card
        // Find the last traveler section's X button
        const removeBtn = await page.locator('button.text-red-500').last();
        if (await removeBtn.isVisible()) {
            await removeBtn.click();
            await page.waitForTimeout(300);
        }
        await screenshot(page, 'bookings-08-remove-traveler');
    });

    await test('Bookings - Close create modal via X', async () => {
        // Click the X button in the modal header (first of the two close buttons)
        await page.locator('[wire\\:click="closeCreateModal"]').first().click();
        await page.waitForTimeout(500);
        // Verify modal is closed
        const modalVisible = await page.locator('h3:has-text("New Booking")').isVisible();
        if (modalVisible) throw new Error('Modal still visible');
        await screenshot(page, 'bookings-09-modal-closed');
    });

    await test('Bookings - Create new booking (full flow)', async () => {
        await page.click('button:has-text("New Booking")');
        await page.waitForSelector('h3:has-text("New Booking")');

        // Fill the form
        await page.selectOption('select[wire\\:model="country"]', 'Kenya');
        await page.fill('input[wire\\:model="startDate"]', '2026-05-01');
        await page.fill('input[wire\\:model="endDate"]', '2026-05-10');
        await page.fill('input[wire\\:model="travelers.0.first_name"]', 'TestFirst');
        await page.fill('input[wire\\:model="travelers.0.last_name"]', 'TestLast');
        await page.fill('input[wire\\:model="travelers.0.email"]', 'testbooking@example.com');

        await screenshot(page, 'bookings-10-create-filled');

        // Submit using the visible button in the modal (use last one which is in the new booking modal)
        await page.locator('button:has-text("Create Booking")').last().click();
        await page.waitForTimeout(2000);
        await screenshot(page, 'bookings-11-after-submit');

        // Wait for navigation or check current URL
        try {
            await page.waitForURL(`${BASE_URL}/bookings/*`, { timeout: 10000 });
            await screenshot(page, 'bookings-12-created-success');
        } catch (e) {
            // If navigation fails, take screenshot to see state
            await screenshot(page, 'bookings-11-submit-error');
            throw new Error('Form submission did not navigate. Check screenshot for errors.');
        }
    });

    await test('Bookings - Row selection checkbox', async () => {
        await page.goto(`${BASE_URL}/bookings`);
        await page.waitForSelector('h1:has-text("Bookings")');
        await page.waitForTimeout(500);

        const checkbox = await page.locator('tbody input[type="checkbox"]').first();
        if (await checkbox.isVisible()) {
            await checkbox.click();
            await page.waitForTimeout(300);
            await screenshot(page, 'bookings-12-row-selected');
        }
    });

    await browser.close();

    fs.writeFileSync(
        path.join(SCREENSHOTS_DIR, 'bookings-results.json'),
        JSON.stringify(results, null, 2)
    );

    console.log('\n' + '='.repeat(40));
    console.log(`Passed: ${results.passed.length}, Failed: ${results.failed.length}`);
    if (results.failed.length > 0) {
        results.failed.forEach(f => console.log(`  FAILED: ${f.name} - ${f.error}`));
    }
}

runTests().catch(console.error);
