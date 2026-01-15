import { chromium } from 'playwright';

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
  
  await page.goto('https://travelcrm.demo.sbarron.com/reports');
  await page.waitForTimeout(1000);
  await page.screenshot({ path: 'mobile-reports.png', fullPage: true });
  console.log('Screenshot: mobile-reports.png');
  
  await browser.close();
})();
