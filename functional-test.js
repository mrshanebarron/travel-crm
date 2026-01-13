import { chromium } from 'playwright';

(async () => {
    const browser = await chromium.launch({ headless: true });
    const context = await browser.newContext();
    const page = await context.newPage();
    
    const baseUrl = 'https://travelcrm.demo.sbarron.com';
    const results = [];
    
    const log = (test, passed, detail = '') => {
        results.push({ test, passed, detail });
        console.log((passed ? '✓' : '✗') + ' ' + test + (detail ? ': ' + detail : ''));
    };
    
    console.log('=== FUNCTIONAL TESTING ===\n');
    
    // Login
    await page.goto(baseUrl + '/login');
    await page.fill('input[name="email"]', 'demo@travelcrm.com');
    await page.fill('input[name="password"]', 'Tr@vel2024!Demo');
    await page.click('button[type="submit"]');
    await page.waitForTimeout(2000);
    
    if (page.url().includes('/login')) {
        console.log('Login failed - cannot continue');
        await browser.close();
        return;
    }
    console.log('Logged in successfully\n');

    // TEST 1: Click a booking row and verify navigation
    console.log('--- Test: Booking row click ---');
    await page.goto(baseUrl + '/bookings');
    await page.waitForTimeout(1000);
    const bookingRows = await page.$$('table tbody tr');
    if (bookingRows.length > 0) {
        const beforeUrl = page.url();
        await bookingRows[0].click();
        await page.waitForTimeout(1500);
        const afterUrl = page.url();
        const navigated = afterUrl !== beforeUrl && afterUrl.includes('/bookings/');
        log('Booking row click navigates', navigated, afterUrl);
    } else {
        log('Booking row click', false, 'No bookings');
    }
    
    // TEST 2: Click a task row and verify navigation
    console.log('\n--- Test: Task row click ---');
    await page.goto(baseUrl + '/tasks');
    await page.waitForTimeout(1000);
    const taskRows = await page.$$('table tbody tr');
    if (taskRows.length > 0) {
        const beforeUrl = page.url();
        await taskRows[0].click();
        await page.waitForTimeout(1500);
        const afterUrl = page.url();
        const navigated = afterUrl !== beforeUrl && afterUrl.includes('/bookings/');
        log('Task row click navigates to booking', navigated, afterUrl);
    } else {
        log('Task row click', false, 'No tasks');
    }
    
    // TEST 3: Click a transfer row and verify navigation
    console.log('\n--- Test: Transfer row click ---');
    await page.goto(baseUrl + '/transfers');
    await page.waitForTimeout(1000);
    const transferRows = await page.$$('table tbody tr');
    if (transferRows.length > 0) {
        const beforeUrl = page.url();
        await transferRows[0].click();
        await page.waitForTimeout(1500);
        const afterUrl = page.url();
        const navigated = afterUrl !== beforeUrl && afterUrl.includes('/transfers/');
        log('Transfer row click navigates', navigated, afterUrl);
    } else {
        log('Transfer row click', false, 'No transfers');
    }
    
    // TEST 4: Add Traveler button works
    console.log('\n--- Test: Add Traveler button ---');
    await page.goto(baseUrl + '/bookings/1/edit');
    await page.waitForTimeout(2000);
    const initialInputs = await page.$$('input[name*="travelers"]');
    const initialCount = initialInputs.length;
    const addBtn = await page.$('button:has-text("Add Traveler")');
    if (addBtn) {
        await addBtn.click();
        await page.waitForTimeout(500);
        const afterInputs = await page.$$('input[name*="travelers"]');
        const afterCount = afterInputs.length;
        log('Add Traveler creates new form', afterCount > initialCount, `${initialCount} -> ${afterCount} inputs`);
    } else {
        log('Add Traveler button', false, 'Button not found');
    }
    
    // TEST 5: Completed tasks tab shows different results
    console.log('\n--- Test: Completed tasks tab ---');
    await page.goto(baseUrl + '/tasks');
    await page.waitForTimeout(1000);
    const completedLink = await page.$('a[href*="filter=completed"]');
    if (completedLink) {
        await completedLink.click();
        await page.waitForTimeout(1000);
        log('Completed tab changes URL', page.url().includes('filter=completed'), page.url());
    } else {
        log('Completed tasks tab', false, 'Link not found');
    }
    
    // TEST 6: Search returns results
    console.log('\n--- Test: Search functionality ---');
    await page.goto(baseUrl + '/dashboard');
    await page.waitForTimeout(500);
    const searchInput = await page.$('input[name="q"]');
    if (searchInput) {
        await searchInput.fill('SAF');
        await page.keyboard.press('Enter');
        await page.waitForTimeout(1500);
        log('Search returns results page', page.url().includes('/search'), page.url());
    } else {
        log('Search', false, 'Input not found');
    }
    
    // TEST 7: Notifications dropdown has content
    console.log('\n--- Test: Notifications dropdown ---');
    await page.goto(baseUrl + '/dashboard');
    await page.waitForTimeout(1000);
    const headerButtons = await page.$$('header button');
    if (headerButtons.length > 0) {
        await headerButtons[0].click();
        await page.waitForTimeout(500);
        const content = await page.content();
        const hasNotifHeader = content.includes('>Notifications<');
        log('Notifications dropdown opens with content', hasNotifHeader);
    } else {
        log('Notifications dropdown', false, 'No header buttons');
    }
    
    // TEST 8: Profile save button styling
    console.log('\n--- Test: Profile button styling ---');
    await page.goto(baseUrl + '/profile');
    await page.waitForTimeout(1000);
    const saveBtns = await page.$$('button:has-text("Save")');
    if (saveBtns.length > 0) {
        const classes = await saveBtns[0].getAttribute('class');
        log('Profile Save button has teal', classes && classes.includes('teal'));
    } else {
        log('Profile Save button', false, 'Button not found');
    }
    
    // TEST 9: Transfer status filter
    console.log('\n--- Test: Transfer status filter ---');
    await page.goto(baseUrl + '/transfers');
    await page.waitForTimeout(500);
    const sentLink = await page.$('a:has-text("Sent")');
    if (sentLink) {
        await sentLink.click();
        await page.waitForTimeout(1000);
        log('Transfer status filter works', page.url().includes('status=sent'), page.url());
    } else {
        log('Transfer status filter', false, 'Sent link not found');
    }
    
    // TEST 10: Booking detail has delete buttons
    console.log('\n--- Test: Booking detail delete buttons ---');
    await page.goto(baseUrl + '/bookings/1');
    await page.waitForTimeout(1000);
    const content = await page.content();
    // Check for delete forms/buttons in ledger, documents, activity sections
    const hasDeleteForms = content.includes('method="POST"') && content.includes('DELETE');
    log('Booking detail has delete forms', hasDeleteForms);
    
    // Summary
    console.log('\n========================================');
    console.log('           RESULTS SUMMARY');
    console.log('========================================');
    const passed = results.filter(r => r.passed).length;
    const failed = results.filter(r => !r.passed).length;
    console.log(`\nPassed: ${passed}/${results.length}`);
    if (failed > 0) {
        console.log('\nFailed tests:');
        results.filter(r => !r.passed).forEach(r => console.log('  - ' + r.test + (r.detail ? ': ' + r.detail : '')));
    } else {
        console.log('\nAll functional tests passed!');
    }
    
    await browser.close();
})();
