// What a visitor does: switch language and theme, use the menu, filter, search and send forms.
import { test, before, after } from 'node:test';
import assert from 'node:assert/strict';
import { startSite } from './helpers.mjs';

let site;
before(async () => { site = await startSite(); });
after(async () => { await site.close(); });

const opened = (page) => page.evaluate(() => window.__opened.map((u) => decodeURIComponent(u)));

test('language switch keeps the page and the query string, and remembers nothing it should not', async () => {
  const page = await site.page();
  await page.goto(`${site.base}/destinations/sylhet?x=1`);
  await page.click('.lang-switch a[hreflang=bn]');
  await page.waitForURL(/\/bn\/destinations\/sylhet\?x=1/);
  assert.equal(await page.locator('html').getAttribute('lang'), 'bn');
  assert.equal(await page.locator('h1').innerText(), 'সিলেট');
  await page.click('.lang-switch a[hreflang=en]');
  await page.waitForURL(/\/destinations\/sylhet\?x=1/);
  assert.deepEqual(page.problems, []);
});

test('dark mode toggles, is announced and survives a reload', async () => {
  const page = await site.page();
  await page.goto(site.base);
  const toggle = page.locator('#theme-toggle');
  assert.equal(await toggle.getAttribute('aria-pressed'), 'false');
  await toggle.click();
  assert.equal(await page.locator('html').getAttribute('data-theme'), 'dark');
  assert.equal(await toggle.getAttribute('aria-pressed'), 'true');
  await page.reload();
  assert.equal(await page.locator('html').getAttribute('data-theme'), 'dark');
});

test('the mobile menu opens, closes with Escape and a link works', async () => {
  const page = await site.page({ width: 390, height: 800 });
  await page.goto(site.base);
  const button = page.locator('#nav-toggle');
  assert.equal(await button.getAttribute('aria-expanded'), 'false');
  await button.click();
  assert.equal(await button.getAttribute('aria-expanded'), 'true');
  await page.keyboard.press('Escape');
  assert.equal(await button.getAttribute('aria-expanded'), 'false');
  await button.click();
  await page.locator('#nav-menu >> text=Packages').first().click();
  await page.waitForURL(/\/packages$/);
});

test('the skip link is the first thing keyboard users reach', async () => {
  const page = await site.page();
  await page.goto(site.base + '/blog');
  await page.keyboard.press('Tab');
  assert.equal(await page.evaluate(() => document.activeElement.className), 'skip-link');
  await page.keyboard.press('Enter');
  assert.equal(await page.evaluate(() => location.hash), '#main');
});

test('destinations dropdown lists every destination and highlights the current one', async () => {
  const page = await site.page();
  await page.goto(site.base + '/destinations/kuakata');
  assert.equal(await page.locator('.submenu a').count(), 7);
  assert.deepEqual(await page.locator('.submenu a[aria-current=page]').allTextContents(), ['Kuakata']);
});

test('booking form: errors first, then a saved request and a WhatsApp message', async () => {
  const page = await site.page();
  await page.goto(`${site.base}/packages?plan=Explorer&place=sylhet&guests=4`);
  assert.equal(await page.locator('#b-destination').inputValue(), 'Sylhet');
  assert.equal(await page.locator('#estimate-total').innerText(), '৳50,000');

  await page.click('#booking-form button[type=submit]');
  const errors = await page.locator('#booking-form .form-error').allInnerTexts();
  assert.ok(errors.filter(Boolean).length >= 3, 'name, phone and date are required');

  await page.fill('#b-name', 'Arif Billah');
  await page.fill('#b-phone', '+880 1711-000000');
  await page.fill('#b-date', '2031-04-04');
  await page.click('#booking-form button[type=submit]');
  await page.waitForFunction(() => window.__opened.length >= 2);
  const [first, url] = await opened(page);
  assert.equal(first, '');
  assert.ok(url.startsWith("https://wa.me/8801779440297?text=Hello TravelOrio! I'd like to book a trip."));
  for (const part of ['Name: Arif Billah', 'Destination: Sylhet', 'Package: Explorer', 'Travelers: 4', 'Estimated total: ৳50,000 (4 × ৳12,500)']) {
    assert.ok(url.includes(part), part);
  }
  assert.match(await page.locator('#form-success').innerText(), /Thank you/);
  assert.deepEqual(page.problems, []);
});

test('bangla booking from a destination page sends a Bangla message with Bangla digits', async () => {
  const page = await site.page();
  await page.goto(site.base + '/bn/destinations/kuakata');
  await page.fill('#t-name', 'প্রিয়া');
  await page.fill('#t-phone', '+8801711000000');
  await page.fill('#t-date', '2031-01-05');
  await page.click('#trip-form button[type=submit]');
  await page.waitForFunction(() => window.__opened.length >= 2);
  const url = (await opened(page))[1];
  for (const part of ['গন্তব্য: কুয়াকাটা', 'যাত্রী: ২', 'আনুমানিক মোট: ৳২৫,০০০']) assert.ok(url.includes(part), part);
});

test('contact form shows server-side errors in the page language', async () => {
  const page = await site.page();
  await page.goto(site.base + '/bn/contact');
  await page.fill('#c-name', 'নুসরাত');
  await page.fill('#c-phone', '01711000000');
  await page.fill('#c-message', 'আমরা বারো জন সহকর্মী ভ্রমণ করতে চাই।');
  // tamper with the select so only the server can notice
  await page.evaluate(() => { document.querySelector('#c-topic').innerHTML = '<option value="Hacked">x</option>'; });
  await page.click('#contact-form button[type=submit]');
  await page.waitForFunction(() => [...document.querySelectorAll('#contact-form .form-error')].some((e) => e.textContent.trim()));
  assert.deepEqual(await page.locator('#contact-form .form-error').allInnerTexts().then((t) => t.filter(Boolean)), ['অনুগ্রহ করে একটি বিষয় বেছে নিন।']);
  assert.ok((await opened(page)).includes('CLOSED'), 'the WhatsApp tab is closed again');
});

test('reviews: filter, sort and show more work with plain links', async () => {
  const page = await site.page();
  await page.goto(site.base + '/reviews');
  assert.equal(await page.locator('.review--card').count(), 6);
  await page.click('#reviews-more');
  assert.equal(await page.locator('.review--card').count(), 12);
  await page.goto(site.base + '/reviews');
  await page.click('#review-filters a:has-text("Sylhet")');
  assert.equal(await page.locator('.review--card').count(), 2);
  await Promise.all([page.waitForNavigation(), page.selectOption('#review-sort', 'highest')]);
  assert.match(page.url(), /sort=highest/);
  assert.equal(await page.locator('#review-filters a[aria-current=true]').innerText(), 'Sylhet (2)');
});

test('blog: category, search with no results, article table of contents and progress bar', async () => {
  const page = await site.page();
  await page.goto(site.base + '/blog');
  assert.equal(await page.locator('.blog-card--featured').count(), 1);
  await page.click('#blog-filters a:has-text("Guides")');
  assert.equal(await page.locator('.blog-card').count(), 3);
  await page.fill('#blog-search', 'zzzz');
  await page.press('#blog-search', 'Enter');
  await page.waitForLoadState();
  assert.match(await page.locator('#blog-grid').innerText(), /No articles match/);
  await page.goto(site.base + '/blog/cox-bazar-3-days');
  assert.ok(await page.locator('.toc li').count() >= 3);
  await page.evaluate(() => scrollTo(0, 1200));
  await page.waitForFunction(() => Math.abs(scrollY - 1200) < 2);   // the page scrolls smoothly
  assert.match(await page.locator('#read-progress').evaluate((e) => e.style.transform), /scaleX\(0\.[1-9]/);
});

test('destination gallery opens in a lightbox that closes with Escape', async () => {
  const page = await site.page();
  await page.goto(site.base + '/destinations/sundarbans');
  await page.locator('.gallery__item').first().click();
  assert.equal(await page.locator('dialog.lightbox').evaluate((d) => d.open), true);
  await page.keyboard.press('ArrowRight');
  assert.match(await page.locator('.lightbox__cap').innerText(), /2 \/ 6/);
  await page.keyboard.press('Escape');
  assert.equal(await page.locator('dialog.lightbox').evaluate((d) => d.open), false);
});
