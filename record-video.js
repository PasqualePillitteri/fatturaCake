const puppeteer = require('puppeteer');
const path = require('path');
const fs = require('fs');
const { execSync } = require('child_process');

const BASE_URL = 'http://localhost:8765';
const CREDENTIALS = {
    email: 'superadmin@example.com',
    password: 'pilliTest'
};

const FRAMES_DIR = path.join(__dirname, 'video_frames');
const OUTPUT_VIDEO = path.join(__dirname, 'demo-fattura.mp4');

let frameCount = 0;

async function captureFrame(page, delay = 100) {
    frameCount++;
    const framePath = path.join(FRAMES_DIR, `frame_${String(frameCount).padStart(5, '0')}.png`);
    await page.screenshot({ path: framePath });
    if (delay > 0) await new Promise(r => setTimeout(r, delay));
}

async function captureFrames(page, count, delay = 100) {
    for (let i = 0; i < count; i++) {
        await captureFrame(page, delay);
    }
}

async function typeWithFrames(page, selector, text, frameInterval = 3) {
    for (let i = 0; i < text.length; i++) {
        await page.type(selector, text[i], { delay: 30 });
        if (i % frameInterval === 0) {
            await captureFrame(page, 50);
        }
    }
    await captureFrame(page, 100);
}

async function run() {
    // Clean and create frames directory
    if (fs.existsSync(FRAMES_DIR)) {
        fs.rmSync(FRAMES_DIR, { recursive: true });
    }
    fs.mkdirSync(FRAMES_DIR, { recursive: true });

    console.log('Launching browser...');
    const browser = await puppeteer.launch({
        headless: 'new',
        args: ['--no-sandbox', '--disable-setuid-sandbox']
    });

    const page = await browser.newPage();
    await page.setViewport({ width: 1400, height: 900 });

    try {
        // 1. LOGIN
        console.log('Step 1: Login...');
        await page.goto(`${BASE_URL}/users/login`, { waitUntil: 'networkidle0' });
        await captureFrames(page, 10); // Show login page

        await page.type('input[name="email"]', CREDENTIALS.email, { delay: 30 });
        await captureFrames(page, 5);
        await page.type('input[name="password"]', CREDENTIALS.password, { delay: 30 });
        await captureFrames(page, 5);

        await Promise.all([
            page.waitForNavigation({ waitUntil: 'networkidle0' }),
            page.click('button[type="submit"]')
        ]);
        await captureFrames(page, 15); // Show dashboard after login
        console.log('Logged in!');

        // 2. GO TO NEW INVOICE
        console.log('Step 2: Navigate to new invoice...');
        await page.goto(`${BASE_URL}/fatture/add`, { waitUntil: 'networkidle0' });
        await new Promise(r => setTimeout(r, 500));
        await captureFrames(page, 20); // Show empty form

        // 3. FILL INVOICE FORM
        console.log('Step 3: Filling invoice form...');

        // Select document type (already TD01)
        await captureFrames(page, 5);

        // Select date
        const today = new Date().toISOString().split('T')[0];
        await page.evaluate((date) => {
            const dateInput = document.querySelector('input[name="data"]');
            if (dateInput) {
                dateInput.value = date;
                dateInput.dispatchEvent(new Event('change', { bubbles: true }));
            }
        }, today);
        await captureFrames(page, 10);

        // Select client from dropdown
        console.log('Selecting client...');
        await page.click('select[name="anagrafica_id"]');
        await captureFrames(page, 5);
        await page.select('select[name="anagrafica_id"]', '2'); // Select second client
        await captureFrames(page, 10);

        // 4. ADD INVOICE LINE
        console.log('Step 4: Adding invoice line...');

        // Wait for the line to be visible
        await new Promise(r => setTimeout(r, 300));

        // Select product in first row
        const productSelect = await page.$('select[name="righe[0][prodotto_id]"]');
        if (productSelect) {
            await productSelect.click();
            await captureFrames(page, 5);
            await page.select('select[name="righe[0][prodotto_id]"]', '1');
            await captureFrames(page, 10);
        }

        // Fill quantity
        const qtyInput = await page.$('input[name="righe[0][quantita]"]');
        if (qtyInput) {
            await qtyInput.click({ clickCount: 3 }); // Select all
            await page.keyboard.type('2');
            await captureFrames(page, 10);
        }

        // Wait for auto-calculation
        await new Promise(r => setTimeout(r, 500));
        await captureFrames(page, 15);

        // Add another line
        console.log('Adding second line...');
        const addButton = await page.$('#btn-add-riga');
        if (addButton) {
            await addButton.click();
            await new Promise(r => setTimeout(r, 300));
            await captureFrames(page, 10);

            // Fill second row
            const productSelect2 = await page.$('select[name="righe[1][prodotto_id]"]');
            if (productSelect2) {
                await productSelect2.click();
                await captureFrames(page, 3);
                await page.select('select[name="righe[1][prodotto_id]"]', '3');
                await captureFrames(page, 5);
            }

            const qtyInput2 = await page.$('input[name="righe[1][quantita]"]');
            if (qtyInput2) {
                await qtyInput2.click({ clickCount: 3 });
                await page.keyboard.type('5');
                await captureFrames(page, 10);
            }
        }
        await captureFrames(page, 10);

        // Scroll to see totals
        await page.evaluate(() => window.scrollBy(0, 300));
        await captureFrames(page, 15);

        // 5. SAVE INVOICE
        console.log('Step 5: Saving invoice...');

        // Find and click submit button
        const submitBtn = await page.$('button[type="submit"]');
        if (submitBtn) {
            await captureFrames(page, 10);

            await Promise.all([
                page.waitForNavigation({ waitUntil: 'networkidle0', timeout: 10000 }).catch(() => {}),
                submitBtn.click()
            ]);
        }

        await new Promise(r => setTimeout(r, 1000));
        await captureFrames(page, 20);

        // 6. VIEW THE CREATED INVOICE
        console.log('Step 6: Viewing created invoice...');

        // Go to fatture list and find the latest
        await page.goto(`${BASE_URL}/fatture`, { waitUntil: 'networkidle0' });
        await new Promise(r => setTimeout(r, 500));
        await captureFrames(page, 15);

        // Click on "Vedi" button of first invoice
        const viewLink = await page.$('a[href*="/fatture/view/"]');
        if (viewLink) {
            await Promise.all([
                page.waitForNavigation({ waitUntil: 'networkidle0' }),
                viewLink.click()
            ]);
        } else {
            // Fallback: go directly to view/1
            await page.goto(`${BASE_URL}/fatture/view/1`, { waitUntil: 'networkidle0' });
        }

        await new Promise(r => setTimeout(r, 500));
        await captureFrames(page, 30); // Show invoice view longer

        // Scroll down to show full invoice
        await page.evaluate(() => window.scrollBy(0, 300));
        await captureFrames(page, 20);

        console.log(`Total frames captured: ${frameCount}`);

    } catch (error) {
        console.error('Error:', error.message);
        await captureFrames(page, 5); // Capture error state
    } finally {
        await browser.close();
    }

    // 7. CREATE VIDEO WITH FFMPEG
    console.log('Creating video with ffmpeg...');
    try {
        execSync(`ffmpeg -y -framerate 10 -i "${FRAMES_DIR}/frame_%05d.png" -c:v libx264 -pix_fmt yuv420p -crf 23 "${OUTPUT_VIDEO}"`, {
            stdio: 'inherit'
        });
        console.log(`\nVideo saved to: ${OUTPUT_VIDEO}`);

        // Cleanup frames
        fs.rmSync(FRAMES_DIR, { recursive: true });
        console.log('Frames cleaned up.');
    } catch (err) {
        console.error('FFmpeg error:', err.message);
        console.log(`Frames are still available in: ${FRAMES_DIR}`);
    }
}

run();
