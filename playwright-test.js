import { chromium } from 'playwright';
import fs from 'fs';

(async () => {
    const browser = await chromium.launch({ headless: true });
    const context = await browser.newContext();
    const page = await context.newPage();
    
    // Login
    await page.goto('https://travelcrm.demo.sbarron.com/login');
    await page.fill('input[name="email"]', 'mrshanebarron@gmail.com');
    await page.fill('input[name="password"]', 'password');
    await page.click('button[type="submit"]');
    await page.waitForTimeout(2000);
    
    // Go to booking edit
    await page.goto('https://travelcrm.demo.sbarron.com/bookings/1/edit');
    await page.waitForTimeout(2000);
    
    // Save full HTML
    const content = await page.content();
    fs.writeFileSync('/tmp/booking-edit.html', content);
    console.log('Saved HTML to /tmp/booking-edit.html');
    console.log('HTML length: ' + content.length);
    
    // Search for specific patterns
    console.log('Contains "Travelers": ' + content.includes('Travelers'));
    console.log('Contains "Add Traveler": ' + content.includes('Add Traveler'));
    console.log('Contains "x-data": ' + content.includes('x-data'));
    console.log('Contains "bookingEditForm": ' + content.includes('bookingEditForm'));
    console.log('Contains "<script>": ' + content.includes('<script>'));
    
    await browser.close();
})();
