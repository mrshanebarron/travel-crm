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
    console.log('CLIENTS PAGE TESTS');
    console.log('='.repeat(40));

    const browser = await chromium.launch({ headless: true });
    const context = await browser.newContext({
        viewport: { width: 1920, height: 1080 },
        ignoreHTTPSErrors: true
    });
    const page = await context.newPage();

    await login(page);

    await test('Clients - Page loads', async () => {
        await page.goto(`${BASE_URL}/clients`);
        await page.waitForSelector('h1:has-text("Clients")');
        await screenshot(page, 'clients-01-list');
    });

    await test('Clients - Search filtering (live)', async () => {
        await page.fill('input[placeholder="Search by name, email, or booking number..."]', 'Test');
        await page.waitForTimeout(500);
        await screenshot(page, 'clients-02-search');
        // Clear search
        await page.fill('input[placeholder="Search by name, email, or booking number..."]', '');
        await page.waitForTimeout(300);
    });

    await test('Clients - Open view modal', async () => {
        // Desktop table View buttons - target the table's visible buttons
        // The desktop table is hidden md:block, inside that we need visible buttons
        const viewBtn = await page.locator('table button:has-text("View")').first();
        if (await viewBtn.isVisible()) {
            await viewBtn.click();
            await page.waitForTimeout(500);
            await screenshot(page, 'clients-03-view-modal');
        } else {
            throw new Error('View button not found');
        }
    });

    await test('Clients - View modal shows client details', async () => {
        // Check for modal content - view modal shows Email, Phone, Date of Birth labels
        const emailLabel = await page.locator('text=Email').first().isVisible();
        const phoneLabel = await page.locator('text=Phone').first().isVisible();
        if (!emailLabel && !phoneLabel) throw new Error('Client details not visible in modal');
        await screenshot(page, 'clients-04-view-details');
    });

    await test('Clients - Close view modal', async () => {
        // Close button is in the modal footer - look for visible Close button
        await page.locator('button:has-text("Close"):visible').click();
        await page.waitForTimeout(300);
        await screenshot(page, 'clients-05-view-closed');
    });

    await test('Clients - Open edit modal', async () => {
        // Desktop table Edit buttons - target the table's visible buttons
        const editBtn = await page.locator('table button:has-text("Edit")').first();
        if (await editBtn.isVisible()) {
            await editBtn.click();
            await page.waitForSelector('h3:has-text("Edit Client")');
            await screenshot(page, 'clients-06-edit-modal');
        } else {
            throw new Error('Edit button not found');
        }
    });

    await test('Clients - Edit form has correct fields', async () => {
        // Check required fields exist
        const firstName = await page.locator('input[wire\\:model="editFirstName"]').isVisible();
        const lastName = await page.locator('input[wire\\:model="editLastName"]').isVisible();

        if (!firstName) throw new Error('First name field not visible');
        if (!lastName) throw new Error('Last name field not visible');

        await screenshot(page, 'clients-07-edit-fields');
    });

    await test('Clients - Close edit modal', async () => {
        // Cancel button is in the modal - look for visible Cancel button
        await page.locator('button:has-text("Cancel"):visible').click();
        await page.waitForTimeout(300);
        const modalVisible = await page.locator('h3:has-text("Edit Client")').isVisible();
        if (modalVisible) throw new Error('Modal still visible');
        await screenshot(page, 'clients-08-edit-closed');
    });

    await test('Clients - Update client (full flow)', async () => {
        // Open edit modal again - use table button
        const editBtn = await page.locator('table button:has-text("Edit")').first();
        await editBtn.click();
        await page.waitForSelector('h3:has-text("Edit Client")');

        // Modify a field - phone input uses wire:model="editPhone"
        const phoneInput = await page.locator('input[wire\\:model="editPhone"]');
        await phoneInput.fill('+1 555 123 4567');
        await screenshot(page, 'clients-09-edit-filled');

        // Save - the submit button contains "Save" text
        await page.locator('button[type="submit"]:has-text("Save")').click();
        await page.waitForTimeout(1000);
        await screenshot(page, 'clients-10-edit-saved');
    });

    await browser.close();

    fs.writeFileSync(
        path.join(SCREENSHOTS_DIR, 'clients-results.json'),
        JSON.stringify(results, null, 2)
    );

    console.log('\n' + '='.repeat(40));
    console.log(`Passed: ${results.passed.length}, Failed: ${results.failed.length}`);
    if (results.failed.length > 0) {
        results.failed.forEach(f => console.log(`  FAILED: ${f.name} - ${f.error}`));
    }
}

runTests().catch(console.error);
