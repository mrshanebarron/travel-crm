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
    console.log('USERS PAGE TESTS');
    console.log('='.repeat(40));

    const browser = await chromium.launch({ headless: true });
    const context = await browser.newContext({
        viewport: { width: 1920, height: 1080 },
        ignoreHTTPSErrors: true
    });
    const page = await context.newPage();

    await login(page);

    await test('Users - Page loads', async () => {
        await page.goto(`${BASE_URL}/users`);
        await page.waitForSelector('h1:has-text("User Management")');
        await screenshot(page, 'users-01-list');
    });

    await test('Users - Role legend displayed', async () => {
        // Check role badges are visible in the legend
        const superAdmin = await page.locator('text=Super Admin').first().isVisible();
        const admin = await page.locator('text=Admin').first().isVisible();
        const user = await page.locator('text=User').first().isVisible();

        if (!superAdmin) throw new Error('Super Admin role not visible');
        if (!admin) throw new Error('Admin role not visible');
        if (!user) throw new Error('User role not visible');

        await screenshot(page, 'users-02-roles-legend');
    });

    await test('Users - Users table displayed', async () => {
        // Check table headers
        const nameHeader = await page.locator('th:has-text("Name")').isVisible();
        const emailHeader = await page.locator('th:has-text("Email")').isVisible();
        const roleHeader = await page.locator('th:has-text("Role")').isVisible();

        if (!nameHeader) throw new Error('Name header not visible');
        if (!emailHeader) throw new Error('Email header not visible');
        if (!roleHeader) throw new Error('Role header not visible');

        await screenshot(page, 'users-03-table');
    });

    await test('Users - Open add user modal', async () => {
        await page.click('button:has-text("Add User")');
        await page.waitForSelector('h3:has-text("Add User")');
        await screenshot(page, 'users-04-add-modal');
    });

    await test('Users - Add modal has required fields', async () => {
        const nameInput = await page.locator('input[wire\\:model="addName"]').isVisible();
        const emailInput = await page.locator('input[wire\\:model="addEmail"]').isVisible();
        const passwordInput = await page.locator('input[wire\\:model="addPassword"]').isVisible();
        const roleSelect = await page.locator('select[wire\\:model="addRole"]').isVisible();

        if (!nameInput) throw new Error('Name field not visible');
        if (!emailInput) throw new Error('Email field not visible');
        if (!passwordInput) throw new Error('Password field not visible');
        if (!roleSelect) throw new Error('Role dropdown not visible');

        await screenshot(page, 'users-05-add-fields');
    });

    await test('Users - Close add modal', async () => {
        await page.locator('button:has-text("Cancel"):visible').click();
        await page.waitForTimeout(300);
        const modalVisible = await page.locator('h3:has-text("Add User")').isVisible();
        if (modalVisible) throw new Error('Modal still visible');
        await screenshot(page, 'users-06-add-closed');
    });

    await test('Users - Open edit user modal', async () => {
        // Click Edit button on a user row (not the current user to avoid issues)
        const editBtn = await page.locator('table button:has-text("Edit")').first();
        if (await editBtn.isVisible()) {
            await editBtn.click();
            await page.waitForSelector('h3:has-text("Edit User")');
            await screenshot(page, 'users-07-edit-modal');
        } else {
            throw new Error('Edit button not found');
        }
    });

    await test('Users - Edit modal has correct fields', async () => {
        const nameInput = await page.locator('input[wire\\:model="editName"]').isVisible();
        const emailInput = await page.locator('input[wire\\:model="editEmail"]').isVisible();
        const passwordInput = await page.locator('input[wire\\:model="editPassword"]').isVisible();
        const roleSelect = await page.locator('select[wire\\:model="editRole"]').isVisible();

        if (!nameInput) throw new Error('Name field not visible');
        if (!emailInput) throw new Error('Email field not visible');
        if (!passwordInput) throw new Error('Password field not visible');
        if (!roleSelect) throw new Error('Role dropdown not visible');

        await screenshot(page, 'users-08-edit-fields');
    });

    await test('Users - Close edit modal', async () => {
        await page.locator('button:has-text("Cancel"):visible').click();
        await page.waitForTimeout(300);
        const modalVisible = await page.locator('h3:has-text("Edit User")').isVisible();
        if (modalVisible) throw new Error('Modal still visible');
        await screenshot(page, 'users-09-edit-closed');
    });

    await test('Users - Edit/Delete buttons visible in table', async () => {
        // Check Edit buttons exist
        const editBtns = await page.locator('table button:has-text("Edit")').count();
        const deleteBtns = await page.locator('table button:has-text("Delete")').count();

        console.log(`  Found ${editBtns} edit buttons, ${deleteBtns} delete buttons`);
        if (editBtns === 0) throw new Error('No edit buttons found');
        // Delete button count may vary (current user doesn't have delete)

        await screenshot(page, 'users-10-table-buttons');
    });

    await browser.close();

    fs.writeFileSync(
        path.join(SCREENSHOTS_DIR, 'users-results.json'),
        JSON.stringify(results, null, 2)
    );

    console.log('\n' + '='.repeat(40));
    console.log(`Passed: ${results.passed.length}, Failed: ${results.failed.length}`);
    if (results.failed.length > 0) {
        results.failed.forEach(f => console.log(`  FAILED: ${f.name} - ${f.error}`));
    }
}

runTests().catch(console.error);
