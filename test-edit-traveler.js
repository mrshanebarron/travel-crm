import puppeteer from 'puppeteer';

(async () => {
    const browser = await puppeteer.launch({ headless: true });
    const page = await browser.newPage();
    await page.setViewport({ width: 1400, height: 900 });
    
    // Login
    await page.goto('http://travel-crm.test/login');
    await page.type('input[name="email"]', 'admin@safari.test');
    await page.type('input[name="password"]', 'password');
    await page.click('button[type="submit"]');
    await page.waitForNavigation();
    
    // Go to a booking with travelers
    await page.goto('http://travel-crm.test/bookings/1');
    await page.waitForSelector('.tab-btn');
    
    // Click Client Details tab
    await page.click('[data-tab="client-details"]');
    await new Promise(r => setTimeout(r, 500));
    
    // Screenshot the client details section
    await page.screenshot({ path: 'edit-traveler-button.png', fullPage: false });
    
    console.log('Screenshot saved: edit-traveler-button.png');
    await browser.close();
})();
