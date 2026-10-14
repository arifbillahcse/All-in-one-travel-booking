// Builds docs/TravelOrio-Company-Profile.pdf from the /company-profile page.
// Usage: node tools/build-company-profile.mjs [--url https://travelorio.com]   (default: a throw-away local copy)
// Needs the same setup as the browser tests (php, playwright + Chromium).
import { startSite } from '../tests/e2e/helpers.mjs';
import { resolve } from 'node:path';
import { chromium } from 'playwright';

const out = resolve(import.meta.dirname, '../docs/TravelOrio-Company-Profile.pdf');
const urlArg = process.argv.indexOf('--url') > -1 ? process.argv[process.argv.indexOf('--url') + 1] : null;

let site = null;
let browser = null;
try {
  const base = urlArg ?? (site = await startSite()).base;
  browser = await chromium.launch();
  const page = await browser.newPage();
  await page.goto(base.replace(/\/$/, '') + '/company-profile', { waitUntil: 'load' });
  await page.emulateMedia({ media: 'print' });
  await page.pdf({ path: out, format: 'A4', printBackground: true, preferCSSPageSize: true, margin: { top: 0, right: 0, bottom: 0, left: 0 } });
  console.log('Wrote ' + out);
} catch (e) {
  console.error(e);
  process.exitCode = 1;
} finally {
  await browser?.close();
  await site?.stop?.();
  process.exit(process.exitCode ?? 0);
}
