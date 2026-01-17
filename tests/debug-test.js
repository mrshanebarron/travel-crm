import { chromium } from 'playwright';

const BASE_URL = 'https://travel-crm.test';

async function debug() {
    const browser = await chromium.launch({ headless: true });
    const context = await browser.newContext({
        viewport: { width: 1920, height: 1080 },
        ignoreHTTPSErrors: true
    });
    const page = await context.newPage();

    console.log('Going to login page...');
    await page.goto(`${BASE_URL}/login`);
    await page.screenshot({ path: './tests/screenshots/debug-01-login.png', fullPage: true });
    console.log('Screenshot: debug-01-login.png');

    // Use pre-filled demo credentials - just click the button
    console.log('Clicking Log in button (using pre-filled demo credentials)...');
    await page.click('button[type="submit"]');

    // Wait and see where we end up
    await page.waitForTimeout(3000);
    console.log('Current URL:', page.url());
    await page.screenshot({ path: './tests/screenshots/debug-02-after-login.png', fullPage: true });
    console.log('Screenshot: debug-02-after-login.png');

    // Try going to bookings
    console.log('Going to bookings...');
    await page.goto(`${BASE_URL}/bookings`);
    await page.waitForTimeout(2000);
    console.log('Current URL:', page.url());
    await page.screenshot({ path: './tests/screenshots/debug-03-bookings.png', fullPage: true });
    console.log('Screenshot: debug-03-bookings.png');

    await browser.close();
    console.log('Done');
}

debug().catch(console.error);
