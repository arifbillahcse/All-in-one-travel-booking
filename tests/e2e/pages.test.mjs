// Every public page, in English and Bangla, on desktop and a phone: loads cleanly and follows the basics.
import { test, before, after } from 'node:test';
import assert from 'node:assert/strict';
import { startSite, PAGES } from './helpers.mjs';

let site;
before(async () => { site = await startSite(); });
after(async () => { await site.close(); });

const checks = () => ({
  ids: [...document.querySelectorAll('[id]')].map((e) => e.id).filter((x, i, a) => a.indexOf(x) !== i),
  h1: document.querySelectorAll('h1').length,
  noAlt: [...document.querySelectorAll('img:not([alt])')].length,
  unlabeled: [...document.querySelectorAll('input:not([type=hidden]):not([type=search]):not([name=website]),select,textarea')]
    .filter((e) => !(e.id && document.querySelector(`label[for="${e.id}"]`)) && !e.getAttribute('aria-label') && !e.closest('label')).map((e) => e.id || e.name),
  overflow: document.documentElement.scrollWidth > innerWidth,
  lang: document.documentElement.lang,
  title: document.title,
  canonical: document.querySelector('link[rel=canonical]')?.href,
  badLinks: [...document.querySelectorAll('a[href]')].map((a) => a.getAttribute('href')).filter((h) => /\.html(\?|#|$)/.test(h) || h === ''),
  csp: window.__csp,
  currentNav: [...document.querySelectorAll('nav a[aria-current=page]')].length,
});

for (const [lang, prefix, width, theme] of [['en', '', 1440, 'light'], ['bn', '/bn', 390, 'dark']]) {
  for (const path of PAGES) {
    const url = prefix + (path === '/' && prefix ? '' : path);
    test(`${lang} ${path} (${width}px, ${theme})`, async () => {
      const page = await site.page({ width, theme });
      const response = await page.goto(site.base + url);
      assert.equal(response.status(), 200);
      assert.ok(response.headers()['content-security-policy']?.includes("script-src 'self' 'nonce-"), 'CSP header');
      await page.waitForTimeout(700);

      const r = await page.evaluate(checks);
      assert.deepEqual(page.problems, [], 'console or script errors');
      assert.deepEqual(r.csp, [], 'CSP violations');
      assert.equal(r.lang, lang);
      assert.equal(r.h1, 1, 'exactly one h1');
      assert.deepEqual(r.ids, [], 'duplicate ids');
      assert.equal(r.noAlt, 0, 'images without alt');
      assert.deepEqual(r.unlabeled, [], 'form fields without a label');
      assert.deepEqual(r.badLinks, [], 'old .html or empty links');
      assert.equal(r.overflow, false, 'horizontal overflow');
      assert.ok(r.canonical?.startsWith(site.base), 'canonical');
      assert.ok(r.title.includes('TravelOrio'), 'title');
      await page.context().close();
    });
  }
}

test('unknown addresses show the site 404 in both languages', async () => {
  const page = await site.page();
  for (const [url, text] of [['/nowhere', 'Back to home'], ['/bn/nowhere', 'হোমে ফিরে যান']]) {
    const response = await page.goto(site.base + url);
    assert.equal(response.status(), 404);
    assert.ok((await page.content()).includes(text));
  }
  // the browser logs the 404 response itself; anything else would be a real problem
  assert.deepEqual(page.problems.filter((p) => !/status of 404/.test(p)), []);
});

test('sitemap.xml and robots.txt answer', async () => {
  const sitemap = await fetch(`${site.base}/sitemap.xml`);
  assert.match(sitemap.headers.get('content-type'), /xml/);
  assert.equal(((await sitemap.text()).match(/<loc>/g) || []).length, 36);
  assert.match(await (await fetch(`${site.base}/robots.txt`)).text(), /Sitemap: .*\/sitemap\.xml/);
});

test('every internal link on every page leads somewhere that exists', async () => {
  const page = await site.page();
  const seen = new Set(); const queue = ['/', '/bn']; const bad = [];
  while (queue.length) {
    const path = queue.shift();
    if (seen.has(path)) continue;
    seen.add(path);
    const response = await page.goto(site.base + path);
    if (response.status() !== 200) { bad.push(`${path} ${response.status()}`); continue; }
    const links = await page.$$eval('a[href]', (a) => a.map((x) => x.href));
    for (const href of links) {
      const u = new URL(href);
      if (u.origin !== site.base || u.pathname.startsWith('/admin') || u.pathname.startsWith('/storage')) continue;
      const next = u.pathname + u.search;
      if (!seen.has(next)) queue.push(next);
      if (seen.size > 400) throw new Error('crawl did not settle');
    }
  }
  assert.deepEqual(bad, []);
  assert.ok(seen.size >= 36, `crawled ${seen.size} pages`);
});
