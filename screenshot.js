import { chromium } from 'playwright';

const pages = [
  { name: 'transfer-show', path: '/transfers/1' },
  { name: 'transfer-edit', path: '/transfers/1/edit' },
  { name: 'booking-create', path: '/bookings/create' },
  { name: 'transfer-create', path: '/transfers/create' },
];

(async () => {
  const browser = await chromium.launch();
  const context = await browser.newContext({
    viewport: { width: 375, height: 812 },
    deviceScaleFactor: 2
  });
  const page = await context.newPage();
  
  // Login first
  await page.goto('https://travelcrm.demo.sbarron.com/login');
  await page.click('button[type="submit"]');
  await page.waitForTimeout(2000);
  
  for (const p of pages) {
    await page.goto(`https://travelcrm.demo.sbarron.com${p.path}`);
    await page.waitForTimeout(500);
    await page.screenshot({ path: `mobile-${p.name}.png`, fullPage: true });
    console.log(`Screenshot: mobile-${p.name}.png`);
  }
  
  await browser.close();
})();
