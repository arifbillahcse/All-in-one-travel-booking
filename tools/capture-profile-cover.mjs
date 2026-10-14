// Saves the hero landscape (sun, islands, sea, ship) as a still picture for the company-profile cover.
// Usage: node tools/capture-profile-cover.mjs   (needs the same setup as the browser tests)
import { startSite } from '../tests/e2e/helpers.mjs';
import { resolve } from 'node:path';

const site = await startSite();
try {
  const page = await site.page({ width: 1600, height: 900 });
  await page.goto(site.base + '/');
  // CSSOM changes are allowed by the site's CSP (an injected <style> is not)
  await page.evaluate(() => {
    for (const sel of ['.hero__content', '.hero__chips', '.hero__search', '.navbar', '.whatsapp-float', '.back-to-top', '.scroll-progress']) {
      document.querySelectorAll(sel).forEach((el) => { el.style.display = 'none'; });
    }
    document.body.style.animation = 'none';
    const ship = document.querySelector('.hero__ship');
    if (ship) { ship.style.animation = 'none'; ship.style.translate = '40vw 0'; }
  });
  await page.waitForTimeout(2500);
  const box = await page.locator('.hero__scene').boundingBox();
  await page.screenshot({ clip: { x: box.x, y: box.y, width: box.width, height: box.height - 44 }, path: resolve(import.meta.dirname, '../public/images/profile-cover.png') });
} catch (e) {
  console.error(e);
  process.exitCode = 1;
} finally {
  await site.stop?.();
  process.exit(process.exitCode ?? 0);
}
