// Accessibility checks that need a real browser: text contrast, keyboard focus, reduced motion.
import { test, before, after } from 'node:test';
import assert from 'node:assert/strict';
import { startSite } from './helpers.mjs';

let site;
before(async () => { site = await startSite(); });
after(async () => { await site.close(); });

/** Text elements whose colour is too close to the background behind them (WCAG AA). Runs in the page. */
function lowContrast() {
  const parse = (c) => { const m = c.match(/rgba?\(([^)]+)\)/); if (!m) return null; const v = m[1].split(/[ ,/]+/).map(Number); return { r: v[0], g: v[1], b: v[2], a: v[3] ?? 1 }; };
  const lum = ({ r, g, b }) => { const f = (x) => { x /= 255; return x <= 0.03928 ? x / 12.92 : ((x + 0.055) / 1.055) ** 2.4; }; return 0.2126 * f(r) + 0.7152 * f(g) + 0.0722 * f(b); };
  const mix = (top, bottom) => ({ r: top.r * top.a + bottom.r * (1 - top.a), g: top.g * top.a + bottom.g * (1 - top.a), b: top.b * top.a + bottom.b * (1 - top.a), a: 1 });
  const background = (el) => {
    const layers = [];
    for (let n = el; n; n = n.parentElement) {
      const cs = getComputedStyle(n);
      if (cs.backgroundImage !== 'none') return null;           // photos and gradients are checked by eye
      const c = parse(cs.backgroundColor);
      if (c && c.a > 0) { layers.push(c); if (c.a >= 1) break; }
    }
    return layers.reverse().reduce((acc, l) => mix(l, acc), { r: 255, g: 255, b: 255, a: 1 });
  };
  const found = [];
  document.querySelectorAll('body *').forEach((el) => {
    if (el.closest('.hero,.page-hero,.dest-hero,.cta-band,.footer,.navbar,svg,script,style,dialog,[aria-hidden=true],.sr-only,.skip-link,.review__stars,.rating-card__stars')) return;
    if (![...el.childNodes].some((n) => n.nodeType === 3 && n.nodeValue.trim().length > 1)) return;
    const box = el.getBoundingClientRect();
    const cs = getComputedStyle(el);
    if (!box.width || !box.height || cs.visibility === 'hidden' || cs.display === 'none') return;
    const bg = background(el); let fg = parse(cs.color);
    if (!bg || !fg) return;
    fg = mix({ ...fg, a: fg.a * (parseFloat(cs.opacity) || 1) }, bg);
    const [a, b] = [lum(fg), lum(bg)];
    const ratio = (Math.max(a, b) + 0.05) / (Math.min(a, b) + 0.05);
    const size = parseFloat(cs.fontSize);
    const large = size >= 24 || (size >= 18.66 && parseInt(cs.fontWeight) >= 700);
    if (ratio < (large ? 3 : 4.5)) found.push(`${el.tagName.toLowerCase()}.${String(el.className).split(' ')[0]} ${ratio.toFixed(2)} "${el.textContent.trim().slice(0, 24)}"`);
  });
  return [...new Set(found)];
}

for (const theme of ['light', 'dark']) {
  for (const path of ['/', '/packages', '/reviews', '/contact', '/why-us', '/blog', '/blog/cox-bazar-3-days', '/destinations/sylhet', '/bn/packages']) {
    test(`text contrast, ${theme}: ${path}`, async () => {
      const page = await site.page({ theme });
      await page.goto(site.base + path);
      await page.waitForTimeout(500);
      await page.evaluate(() => {
        document.querySelectorAll('[data-reveal]').forEach((e) => e.classList.add('is-visible'));
        document.querySelectorAll('*').forEach((e) => { e.style.animation = 'none'; e.style.transition = 'none'; });   // CSSOM changes are allowed by the CSP
      });
      assert.deepEqual(await page.evaluate(lowContrast), []);
      await page.context().close();
    });
  }
}

test('every control can be reached with the keyboard and shows where the focus is', async () => {
  const page = await site.page();
  await page.goto(site.base + '/contact');
  const seen = new Set();
  for (let i = 0; i < 60; i++) {
    await page.keyboard.press('Tab');
    const state = await page.evaluate(() => {
      const el = document.activeElement;
      if (!el || el === document.body) return null;
      const cs = getComputedStyle(el);
      const visible = cs.outlineStyle !== 'none' && parseFloat(cs.outlineWidth) > 0 || cs.boxShadow !== 'none';
      return { id: `${el.tagName}#${el.id || el.getAttribute('name') || el.textContent.trim().slice(0, 15)}`, visible, hidden: !el.offsetParent && cs.position !== 'fixed' };
    });
    if (!state) continue;
    seen.add(state.id);
    assert.equal(state.hidden, false, `focus landed on a hidden element: ${state.id}`);
    assert.equal(state.visible, true, `no focus indicator on ${state.id}`);
  }
  for (const needed of ['INPUT#c-name', 'INPUT#c-phone', 'SELECT#c-topic', 'TEXTAREA#c-message']) assert.ok(seen.has(needed), `${needed} is reachable`);
});

test('people who ask for less motion see the content without waiting for animations', async () => {
  const page = await site.page();
  await page.emulateMedia({ reducedMotion: 'reduce' });
  await page.goto(site.base + '/why-us');
  const hidden = await page.evaluate(() => [...document.querySelectorAll('[data-reveal]')].filter((e) => getComputedStyle(e).opacity === '0').length);
  assert.equal(hidden, 0);
});

test('tap targets on a phone are at least 32px tall (WCAG 2.2 asks for 24)', async () => {
  const page = await site.page({ width: 390, height: 800 });
  for (const path of ['/', '/packages', '/contact']) {
    await page.goto(site.base + path);
    const small = await page.evaluate(() => [...document.querySelectorAll('a, button, select, input:not([type=hidden]):not([type=radio]), summary')]
      .filter((e) => { const r = e.getBoundingClientRect(); const cs = getComputedStyle(e); return r.width > 0 && r.height > 0 && r.height < 32 && cs.display !== 'inline' && !e.closest('.footer, .sr-only, .skip-link, p, li > a:only-child, .search__field, .hp') && cs.position !== 'absolute'; })
      .map((e) => `${e.tagName}.${String(e.className).split(' ')[0]} ${Math.round(e.getBoundingClientRect().height)}px "${e.textContent.trim().slice(0, 20)}"`));
    assert.deepEqual(small, [], path);
    // in the hero search card the label and the control are one touch area (tapping the label opens the control)
    const fields = await page.$$eval('.search__field', (f) => f.map((x) => Math.round(x.getBoundingClientRect().height)));
    assert.ok(fields.every((h) => h >= 44), `search fields ${fields}`);
  }
});
