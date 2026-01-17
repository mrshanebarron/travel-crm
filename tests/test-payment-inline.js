import { chromium } from 'playwright';
import fs from 'fs';
import path from 'path';

const BASE_URL = 'https://travel-crm.test';
const SCREENSHOTS_DIR = './tests/screenshots';

async function screenshot(page, name) {
    const filepath = path.join(SCREENSHOTS_DIR, `${name}.png`);
    await page.screenshot({ path: filepath, fullPage: true });
    console.log(`  Screenshot: ${name}.png`);
}

async function runTest() {
    console.log('PAYMENT INLINE EDIT TEST');
    console.log('='.repeat(40));

    const browser = await chromium.launch({ headless: true });
    const context = await browser.newContext({
        viewport: { width: 1920, height: 1080 },
        ignoreHTTPSErrors: true
    });
    const page = await context.newPage();

    // Login
    await page.goto(`${BASE_URL}/login`);
    await page.click('button[type="submit"]');
    await page.waitForURL(`${BASE_URL}/dashboard`);
    console.log('  Logged in successfully');

    // Navigate to a booking detail
    await page.goto(`${BASE_URL}/bookings`);
    await page.waitForSelector('h1:has-text("Bookings")');
    await page.waitForTimeout(500);

    // Click View on first booking
    const viewBtn = await page.locator('table a:has-text("View")').first();
    await viewBtn.click();
    await page.waitForURL(/\/bookings\/\d+$/);
    console.log('  Navigated to booking detail');

    // Get the current URL to check for page refresh later
    const originalUrl = page.url();

    // Click on Rates & Payments tab
    await page.click('button[data-tab="payment-details"]');
    await page.waitForTimeout(500);
    await screenshot(page, 'payment-01-initial');
    console.log('  Opened Rates & Payments tab');

    // Look for a Livewire edit button - these have wire:click="startEditing"
    const editBtn = await page.locator('button[wire\\:click*="startEditing"]').first();

    if (await editBtn.isVisible()) {
        console.log('  Found edit button, clicking...');
        await editBtn.click();
        await page.waitForTimeout(500);
        await screenshot(page, 'payment-02-editing');

        // Check if edit input appeared (Livewire should show input without refresh)
        const editInput = await page.locator('input[wire\\:model="editingSafariRate"]');
        if (await editInput.isVisible()) {
            console.log('  ✓ Edit input appeared (Livewire working)');

            // Store original value and change it
            const originalValue = await editInput.inputValue();
            await editInput.fill('5500');
            await screenshot(page, 'payment-03-changed');
            console.log(`  Changed rate from ${originalValue} to 5500`);

            // Click Save
            await page.locator('button:has-text("Save")').first().click();
            await page.waitForTimeout(1000);
            await screenshot(page, 'payment-04-saved');

            // Check URL hasn't changed (no page refresh)
            const newUrl = page.url();
            if (newUrl === originalUrl) {
                console.log('  ✓ PASSED: No page refresh occurred!');

                // Restore original value
                const editBtn2 = await page.locator('button.text-xs.text-orange-600:has(svg)').first();
                await editBtn2.click();
                await page.waitForTimeout(300);
                await page.locator('input[wire\\:model="editingSafariRate"]').fill(originalValue);
                await page.locator('button:has-text("Save")').first().click();
                await page.waitForTimeout(500);
                console.log(`  Restored rate to ${originalValue}`);
            } else {
                console.log('  ✗ FAILED: Page URL changed (refresh occurred)');
            }
        } else {
            console.log('  ✗ FAILED: Edit input not visible - Livewire may not be working');
            await screenshot(page, 'payment-02-no-input');
        }
    } else {
        console.log('  ⚠ No edit button found');
        await screenshot(page, 'payment-02-no-button');
    }

    await browser.close();
    console.log('\n' + '='.repeat(40));
    console.log('Test complete');
}

runTest().catch(console.error);
