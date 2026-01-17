import { chromium } from 'playwright';
import path from 'path';

const BASE_URL = 'https://travel-crm.test';
const SCREENSHOTS_DIR = './tests/screenshots';

async function screenshot(page, name) {
    const filepath = path.join(SCREENSHOTS_DIR, `${name}.png`);
    await page.screenshot({ path: filepath, fullPage: true });
    console.log(`  Screenshot: ${name}.png`);
}

async function runTest() {
    console.log('PDF IMPORT TEST');
    console.log('='.repeat(40));

    const browser = await chromium.launch({ headless: true });
    const context = await browser.newContext({
        viewport: { width: 1920, height: 1080 },
        ignoreHTTPSErrors: true
    });
    const page = await context.newPage();

    // Listen for network responses
    page.on('response', async response => {
        if (response.url().includes('create-from-pdf')) {
            console.log(`  [Network] ${response.status()} ${response.url()}`);
        }
    });

    // Login
    await page.goto(`${BASE_URL}/login`);
    await page.click('button[type="submit"]');
    await page.waitForURL(`${BASE_URL}/dashboard`);
    console.log('  Logged in successfully');

    // Navigate to bookings
    await page.goto(`${BASE_URL}/bookings`);
    await page.waitForSelector('h1:has-text("Bookings")');
    await screenshot(page, 'pdf-01-bookings-page');
    console.log('  On bookings page');

    // Click the Import from Safari Office button
    const importBtn = await page.locator('button:has-text("Import from Safari Office")');
    if (await importBtn.isVisible()) {
        console.log('  Found import button, clicking...');
        await importBtn.click();
        await page.waitForTimeout(500);
        await screenshot(page, 'pdf-02-modal-opened');

        // Check if modal appeared
        const modal = await page.locator('h3:has-text("Import from Safari Office")');
        if (await modal.isVisible()) {
            console.log('  ✓ Import modal opened successfully');

            // Check for file input
            const fileInput = await page.locator('input[type="file"][name="pdf"]');
            if (await fileInput.count() > 0) {
                console.log('  ✓ File input found');
            } else {
                console.log('  ✗ File input NOT found');
            }

            // Check for submit button
            const submitBtn = await page.locator('button[type="submit"]:has-text("Create Booking")');
            if (await submitBtn.isVisible()) {
                console.log('  ✓ Submit button found');
            } else {
                console.log('  ✗ Submit button NOT found');
            }

            // Check the form action
            const form = await page.locator('form[action*="create-from-pdf"]');
            if (await form.count() > 0) {
                const action = await form.getAttribute('action');
                console.log(`  Form action: ${action}`);
            } else {
                console.log('  ✗ Form with create-from-pdf action NOT found');
                // Let's see what form we have
                const anyForm = await page.locator('form').first();
                const anyAction = await anyForm.getAttribute('action');
                console.log(`  Found form with action: ${anyAction}`);
            }

            // Try uploading a PDF - use the form directly
            console.log('\n  Attempting to upload PDF...');

            // Get the file input and set files using the page method
            const [fileChooser] = await Promise.all([
                page.waitForEvent('filechooser'),
                page.locator('input[type="file"][name="pdf"]').click()
            ]);
            await fileChooser.setFiles('./Sample_PDF_Booking_1.pdf');

            await page.waitForTimeout(300);
            await screenshot(page, 'pdf-03-file-selected');
            console.log('  File selected');

            // Wait a bit longer to ensure file is attached
            await page.waitForTimeout(500);

            // Debug: Check if file input has files
            const hasFiles = await page.evaluate(() => {
                const input = document.querySelector('input[type="file"][name="pdf"]');
                return input && input.files && input.files.length > 0 ? input.files[0].name : 'no files';
            });
            console.log(`  File input status: ${hasFiles}`);

            // Submit the form by clicking the submit button within the modal form
            const modalForm = await page.locator('form[action*="create-from-pdf"]');
            const submitBtnUpload = await modalForm.locator('button[type="submit"]');
            await submitBtnUpload.click();
            console.log('  Form submitted, waiting for response...');

            // Wait for navigation or error
            try {
                await page.waitForURL(/\/bookings\/\d+$/, { timeout: 15000 });
                await screenshot(page, 'pdf-04-booking-created');
                console.log('  ✓ SUCCESS: Booking created from PDF!');
                console.log(`  New URL: ${page.url()}`);
            } catch (e) {
                await screenshot(page, 'pdf-04-error');
                console.log('  ✗ FAILED: Did not navigate to new booking');

                // Check for ANY flash messages in page content
                const pageContent = await page.content();
                if (pageContent.includes('error') || pageContent.includes('Error')) {
                    // Try to find validation errors
                    const validationErrors = await page.locator('.text-red-500, .text-red-600, [class*="invalid"]').all();
                    for (const err of validationErrors) {
                        const text = await err.textContent();
                        if (text.trim()) console.log(`  Validation error: ${text.trim()}`);
                    }
                }

                // Check for session flash messages (Laravel style)
                const alerts = await page.locator('[role="alert"], .alert, .flash-message').all();
                for (const alert of alerts) {
                    const text = await alert.textContent();
                    if (text.trim()) console.log(`  Alert: ${text.trim()}`);
                }

                // Check page HTML for flash divs
                const flashSuccess = await page.locator('div:has-text("success")').first();
                const flashError = await page.locator('div:has-text("error")').first();

                // Log any visible error indicators
                const allText = await page.locator('body').textContent();
                if (allText.includes('Could not extract')) {
                    console.log('  Found: "Could not extract" error message');
                }
                if (allText.includes('validation')) {
                    console.log('  Found: validation related text');
                }

                // Check current URL
                console.log(`  Current URL: ${page.url()}`);
            }
        } else {
            console.log('  ✗ Import modal did NOT open');
        }
    } else {
        console.log('  ✗ Import button not found');
    }

    await browser.close();
    console.log('\n' + '='.repeat(40));
    console.log('Test complete');
}

runTest().catch(console.error);
