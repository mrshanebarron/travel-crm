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
    console.log('VENDORS PAGE TESTS');
    console.log('='.repeat(40));

    const browser = await chromium.launch({ headless: true });
    const context = await browser.newContext({
        viewport: { width: 1920, height: 1080 },
        ignoreHTTPSErrors: true
    });
    const page = await context.newPage();

    await login(page);

    await test('Vendors - Page loads', async () => {
        await page.goto(`${BASE_URL}/vendors`);
        await page.waitForSelector('h1:has-text("Vendors")');
        await screenshot(page, 'vendors-01-list');
    });

    await test('Vendors - Search filtering (live)', async () => {
        await page.fill('input[placeholder="Search vendors..."]', 'Lodge');
        await page.waitForTimeout(500);
        await screenshot(page, 'vendors-02-search');
        // Clear search
        await page.fill('input[placeholder="Search vendors..."]', '');
        await page.waitForTimeout(300);
    });

    await test('Vendors - Category filter', async () => {
        await page.selectOption('select[wire\\:model\\.live="category"]', 'Lodge');
        await page.waitForTimeout(500);
        await screenshot(page, 'vendors-03-category-filter');
        // Clear filter
        await page.selectOption('select[wire\\:model\\.live="category"]', '');
        await page.waitForTimeout(300);
    });

    await test('Vendors - Open create modal', async () => {
        await page.click('button:has-text("Add Vendor")');
        await page.waitForSelector('h3:has-text("Add New Vendor")');
        await screenshot(page, 'vendors-04-create-modal');
    });

    await test('Vendors - Fill create form', async () => {
        await page.fill('input[wire\\:model="name"]', 'Test Safari Lodge');
        await page.selectOption('select[wire\\:model="vendorCategory"]', 'Lodge');
        await page.fill('input[wire\\:model="country"]', 'Tanzania');
        await page.fill('input[wire\\:model="contactName"]', 'John Manager');
        await page.fill('input[wire\\:model="email"]', 'test@safarilodge.com');
        await page.fill('input[wire\\:model="phone"]', '+255 123 456 789');
        await screenshot(page, 'vendors-05-form-filled');
    });

    await test('Vendors - Close modal via X', async () => {
        await page.locator('[wire\\:click="closeCreateModal"]').first().click();
        await page.waitForTimeout(500);
        const modalVisible = await page.locator('h3:has-text("Add New Vendor")').isVisible();
        if (modalVisible) throw new Error('Modal still visible');
        await screenshot(page, 'vendors-06-modal-closed');
    });

    await test('Vendors - Create new vendor (full flow)', async () => {
        await page.click('button:has-text("Add Vendor")');
        await page.waitForSelector('h3:has-text("Add New Vendor")');

        await page.fill('input[wire\\:model="name"]', 'Playwright Test Lodge');
        await page.selectOption('select[wire\\:model="vendorCategory"]', 'Lodge');
        await page.fill('input[wire\\:model="country"]', 'Kenya');
        await page.fill('input[wire\\:model="contactName"]', 'Test Contact');
        await page.fill('input[wire\\:model="email"]', 'playwright@test.com');

        await screenshot(page, 'vendors-07-create-filled');

        await page.locator('button:has-text("Create Vendor")').click();

        await page.waitForTimeout(2000);
        await screenshot(page, 'vendors-08-after-submit');

        try {
            await page.waitForURL(`${BASE_URL}/vendors/*`, { timeout: 10000 });
            await screenshot(page, 'vendors-09-created-success');
        } catch (e) {
            await screenshot(page, 'vendors-08-submit-error');
            throw new Error('Form submission did not navigate. Check screenshot for errors.');
        }
    });

    await test('Vendors - View/Edit buttons visible on list', async () => {
        await page.goto(`${BASE_URL}/vendors`);
        await page.waitForSelector('h1:has-text("Vendors")');
        await page.waitForTimeout(500);

        // Check buttons exist in the table (they use action-button component with icons)
        const viewBtns = await page.locator('a[href*="/vendors/"]').count();
        const editBtns = await page.locator('a[href*="/vendors/"][href*="/edit"]').count();

        console.log(`  Found ${viewBtns} view links, ${editBtns} edit links`);
        if (viewBtns === 0) throw new Error('No view buttons found');

        await screenshot(page, 'vendors-10-list-with-buttons');
    });

    await browser.close();

    fs.writeFileSync(
        path.join(SCREENSHOTS_DIR, 'vendors-results.json'),
        JSON.stringify(results, null, 2)
    );

    console.log('\n' + '='.repeat(40));
    console.log(`Passed: ${results.passed.length}, Failed: ${results.failed.length}`);
    if (results.failed.length > 0) {
        results.failed.forEach(f => console.log(`  FAILED: ${f.name} - ${f.error}`));
    }
}

runTests().catch(console.error);
