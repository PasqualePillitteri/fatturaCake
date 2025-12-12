const puppeteer = require('puppeteer');

(async () => {
    const browser = await puppeteer.launch({ headless: 'new' });
    const page = await browser.newPage();
    await page.setViewport({ width: 1400, height: 900 });

    // Login
    await page.goto('http://localhost:8765/users/login', { waitUntil: 'networkidle0' });
    await page.type('input[name="email"]', 'superadmin@example.com');
    await page.type('input[name="password"]', 'pilliTest');
    await Promise.all([
        page.waitForNavigation({ waitUntil: 'networkidle0' }),
        page.click('button[type="submit"]')
    ]);

    // Dashboard
    await page.goto('http://localhost:8765/dashboard', { waitUntil: 'networkidle0' });
    await new Promise(r => setTimeout(r, 1000));
    await page.screenshot({ path: 'screenshots/01-dashboard.png' });
    console.log('Dashboard screenshot updated!');

    await browser.close();
})();
