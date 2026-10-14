// The admin panel from a browser: login, roles of pages, a content edit that shows on the site.
import { test, before, after } from 'node:test';
import assert from 'node:assert/strict';
import { startSite } from './helpers.mjs';

let site;
before(async () => { site = await startSite({ admin: true }); });
after(async () => { await site.close(); });

test('the panel is closed to visitors and has no site CSP (it needs its own scripts)', async () => {
  const page = await site.page();
  const response = await page.goto(site.base + '/admin');
  assert.match(page.url(), /\/admin\/login$/);
  assert.equal(response.headers()['content-security-policy'], undefined);
  assert.equal(response.headers()['x-robots-tag'], 'noindex, nofollow');
  assert.equal(response.headers()['x-frame-options'], 'SAMEORIGIN');
});

test('wrong password is refused, right password reaches the dashboard', async () => {
  const page = await site.page();
  await page.goto(site.base + '/admin/login');
  await page.fill('input[type=email]', 'e2e@example.com');
  await page.fill('input[type=password]', 'wrong-password');
  await page.click('button[type=submit]');
  await page.waitForSelector('.fi-fo-field-wrp-error-message');
  assert.match(page.url(), /login/);

  await page.fill('input[type=password]', 'e2e-password-123');
  await page.click('button[type=submit]');
  await page.waitForURL(/\/admin$/);
  assert.match(await page.title(), /Dashboard/);
  assert.ok((await page.locator('.fi-sidebar-item-label').allInnerTexts()).includes('Site settings'), 'owner sees site settings');
});

test('an edit in the admin shows on the public site', async () => {
  const page = await site.page();
  await page.goto(site.base + '/admin/login');
  await page.fill('input[type=email]', 'e2e@example.com');
  await page.fill('input[type=password]', 'e2e-password-123');
  await page.click('button[type=submit]');
  await page.waitForURL(/\/admin$/);

  await page.goto(site.base + '/admin/packages/2/edit');
  const price = page.locator('input[type=number]').first();       // Price (৳ per person)
  await price.waitFor();
  const fields = await page.locator('input[type=number]').evaluateAll((els) => els.map((e) => e.id));
  assert.ok(fields.length >= 4);
  const priceField = page.locator('input[id$="price"]');
  await priceField.fill('13500');
  await page.click('button:has-text("Save changes")');
  await page.waitForSelector('text=Saved');

  const html = await (await fetch(site.base + '/packages')).text();
  assert.ok(html.includes('13,500'));
  assert.ok(!html.includes('12,500'));
});

test('a signed-in owner can open every admin list without errors', async () => {
  const page = await site.page();
  await page.goto(site.base + '/admin/login');
  await page.fill('input[type=email]', 'e2e@example.com');
  await page.fill('input[type=password]', 'e2e-password-123');
  await page.click('button[type=submit]');
  await page.waitForURL(/\/admin$/);
  for (const path of ['destinations', 'packages', 'addons', 'reviews', 'posts', 'post-categories', 'inquiries', 'users', 'site-settings', 'destinations/1/edit', 'posts/1/edit']) {
    const response = await page.goto(`${site.base}/admin/${path}`);
    assert.equal(response.status(), 200, path);
  }
  assert.deepEqual(page.problems.filter((p) => !/Failed to load resource/.test(p)), []);
});
