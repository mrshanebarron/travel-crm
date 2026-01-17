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
    console.log('TRANSFERS PAGE TESTS');
    console.log('='.repeat(40));

    const browser = await chromium.launch({ headless: true });
    const context = await browser.newContext({
        viewport: { width: 1920, height: 1080 },
        ignoreHTTPSErrors: true
    });
    const page = await context.newPage();

    await login(page);

    await test('Transfers - Page loads', async () => {
        await page.goto(`${BASE_URL}/transfers`);
        await page.waitForSelector('h1:has-text("Transfer Requests")');
        await screenshot(page, 'transfers-01-list');
    });

    await test('Transfers - Status tab filtering (Draft)', async () => {
        await page.click('button:has-text("Draft")');
        await page.waitForTimeout(500);
        await screenshot(page, 'transfers-02-draft');
    });

    await test('Transfers - Status tab filtering (Sent)', async () => {
        await page.click('button:has-text("Sent")');
        await page.waitForTimeout(500);
        await screenshot(page, 'transfers-03-sent');
    });

    await test('Transfers - Status tab filtering (Transfer Done)', async () => {
        await page.click('button:has-text("Transfer Done")');
        await page.waitForTimeout(500);
        await screenshot(page, 'transfers-04-transfer-done');
    });

    await test('Transfers - Status tab filtering (Paid)', async () => {
        await page.click('button:has-text("Paid")');
        await page.waitForTimeout(500);
        await screenshot(page, 'transfers-05-paid');
    });

    await test('Transfers - Status tab filtering (All)', async () => {
        await page.click('button.tab:has-text("All")');
        await page.waitForTimeout(500);
        await screenshot(page, 'transfers-06-all');
    });

    await test('Transfers - Navigate to create page', async () => {
        await page.click('a:has-text("New Transfer")');
        await page.waitForSelector('h1:has-text("New Transfer Request")');
        await screenshot(page, 'transfers-07-create-page');
    });

    await test('Transfers - Create form has required fields', async () => {
        // Check for request date field (the only field on create form)
        const requestDate = await page.locator('input[type="date"]').isVisible();

        if (!requestDate) throw new Error('Request date field not visible');

        await screenshot(page, 'transfers-08-create-form');
    });

    await test('Transfers - View/Edit links visible on list', async () => {
        await page.goto(`${BASE_URL}/transfers`);
        await page.waitForSelector('h1:has-text("Transfer Requests")');
        await page.waitForTimeout(500);

        // Check View/Edit buttons exist in the table
        const viewLinks = await page.locator('table a[href*="/transfers/"]:not([href*="/edit"])').count();
        const editLinks = await page.locator('table a[href*="/transfers/"][href*="/edit"]').count();

        console.log(`  Found ${viewLinks} view links, ${editLinks} edit links`);
        if (viewLinks === 0) throw new Error('No view links found');

        await screenshot(page, 'transfers-09-list-with-buttons');
    });

    await test('Transfers - Click View navigates to show page', async () => {
        // Click on a transfer number link in the table
        const transferLink = await page.locator('table a.text-orange-600').first();
        if (await transferLink.isVisible()) {
            await transferLink.click();
            await page.waitForURL(/\/transfers\/\d+$/);
            await screenshot(page, 'transfers-10-show-page');
        } else {
            throw new Error('No transfer link found');
        }
    });

    await browser.close();

    fs.writeFileSync(
        path.join(SCREENSHOTS_DIR, 'transfers-results.json'),
        JSON.stringify(results, null, 2)
    );

    console.log('\n' + '='.repeat(40));
    console.log(`Passed: ${results.passed.length}, Failed: ${results.failed.length}`);
    if (results.failed.length > 0) {
        results.failed.forEach(f => console.log(`  FAILED: ${f.name} - ${f.error}`));
    }
}

runTests().catch(console.error);
