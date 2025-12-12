const puppeteer = require('puppeteer');
const path = require('path');
const fs = require('fs');

const BASE_URL = 'http://localhost:8765';
const CREDENTIALS = {
    email: 'superadmin@example.com',
    password: 'pilliTest'
};

const SCREENSHOTS_DIR = path.join(__dirname, 'screenshots');

const PAGES_TO_CAPTURE = [
    { name: '01-dashboard', url: '/dashboard', waitFor: '.dashboard, .card, main' },
    { name: '02-fatture-emesse', url: '/fatture?tipo=attiva', waitFor: 'table, .index' },
    { name: '03-fattura-nuova', url: '/fatture/add', waitFor: 'form' },
    { name: '04-anagrafiche', url: '/anagrafiche', waitFor: 'table, .index' },
    { name: '05-prodotti', url: '/prodotti', waitFor: 'table, .index' },
    { name: '06-import', url: '/import/fatture', waitFor: 'form' },
    { name: '07-users', url: '/users', waitFor: 'table, .index' },
];

async function run() {
    // Create screenshots directory
    if (!fs.existsSync(SCREENSHOTS_DIR)) {
        fs.mkdirSync(SCREENSHOTS_DIR, { recursive: true });
    }

    console.log('Launching browser...');
    const browser = await puppeteer.launch({
        headless: 'new',
        args: ['--no-sandbox', '--disable-setuid-sandbox']
    });

    const page = await browser.newPage();
    await page.setViewport({ width: 1400, height: 900 });

    try {
        // Login
        console.log('Logging in...');
        await page.goto(`${BASE_URL}/users/login`, { waitUntil: 'networkidle0' });

        // Screenshot login page
        await page.screenshot({
            path: path.join(SCREENSHOTS_DIR, '00-login.png'),
            fullPage: false
        });
        console.log('Captured: 00-login.png');

        // Fill login form
        await page.type('input[name="email"]', CREDENTIALS.email);
        await page.type('input[name="password"]', CREDENTIALS.password);

        // Submit form
        await Promise.all([
            page.waitForNavigation({ waitUntil: 'networkidle0' }),
            page.click('button[type="submit"]')
        ]);

        console.log('Logged in successfully!');

        // Capture each page
        for (const pageInfo of PAGES_TO_CAPTURE) {
            try {
                console.log(`Capturing ${pageInfo.name}...`);
                await page.goto(`${BASE_URL}${pageInfo.url}`, {
                    waitUntil: 'networkidle0',
                    timeout: 15000
                });

                // Wait a bit for any JS to render
                await new Promise(r => setTimeout(r, 1000));

                await page.screenshot({
                    path: path.join(SCREENSHOTS_DIR, `${pageInfo.name}.png`),
                    fullPage: false
                });
                console.log(`Captured: ${pageInfo.name}.png`);
            } catch (err) {
                console.log(`Warning: Could not capture ${pageInfo.name}: ${err.message}`);
            }
        }

        // Try to capture a fattura view if exists
        try {
            console.log('Trying to capture fattura view...');
            await page.goto(`${BASE_URL}/fatture/view/1`, {
                waitUntil: 'networkidle0',
                timeout: 10000
            });
            await new Promise(r => setTimeout(r, 1000));
            await page.screenshot({
                path: path.join(SCREENSHOTS_DIR, '08-fattura-view.png'),
                fullPage: true
            });
            console.log('Captured: 08-fattura-view.png');
        } catch (err) {
            console.log('No fattura with ID 1 found, skipping view');
        }

    } catch (error) {
        console.error('Error:', error.message);
    } finally {
        await browser.close();
        console.log(`\nScreenshots saved to: ${SCREENSHOTS_DIR}`);
    }
}

run();
