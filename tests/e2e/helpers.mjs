// Shared setup for the browser tests: a throw-away SQLite database, a PHP dev server and Chromium.
// Needs: `npm run build` once (Vite manifest), PHP, and `npm i -D playwright` with a Chromium browser.
import { spawn, spawnSync } from 'node:child_process';
import { mkdtempSync, rmSync, existsSync } from 'node:fs';
import { tmpdir } from 'node:os';
import { join, resolve } from 'node:path';
import net from 'node:net';
import { chromium } from 'playwright';

const root = resolve(import.meta.dirname, '..', '..');

function freePort() {
  return new Promise((ok, fail) => {
    const s = net.createServer();
    s.listen(0, '127.0.0.1', () => { const { port } = s.address(); s.close(() => ok(port)); });
    s.on('error', fail);
  });
}

function artisan(args, env) {
  const r = spawnSync('php', ['artisan', ...args], { cwd: root, env: { ...process.env, ...env }, encoding: 'utf8' });
  if (r.status !== 0) throw new Error(`artisan ${args.join(' ')} failed:\n${r.stdout}\n${r.stderr}`);
}

export async function startSite({ admin = false } = {}) {
  if (!existsSync(join(root, 'public/build/manifest.json'))) {
    throw new Error('public/build/manifest.json is missing: run `npm run build` first.');
  }
  const dir = mkdtempSync(join(tmpdir(), 'travelorio-e2e-'));
  const port = await freePort();
  const base = `http://127.0.0.1:${port}`;
  const env = {
    APP_ENV: 'local', APP_DEBUG: 'false', APP_URL: base,
    DB_CONNECTION: 'sqlite', DB_DATABASE: join(dir, 'e2e.sqlite'),
    CACHE_STORE: 'array', SESSION_DRIVER: 'file', QUEUE_CONNECTION: 'sync', MAIL_MAILER: 'log',
    LOG_CHANNEL: 'stderr', LOG_LEVEL: 'error',
  };
  spawnSync('touch', [env.DB_DATABASE]);
  artisan(['migrate:fresh', '--seed', '--force'], env);
  if (admin) artisan(['travelorio:admin', 'e2e@example.com', '--name=E2E', '--password=e2e-password-123'], env);

  const server = spawn('php', ['artisan', 'serve', '--host=127.0.0.1', `--port=${port}`, '--no-reload'], {
    cwd: root, env: { ...process.env, ...env, PHP_CLI_SERVER_WORKERS: '4' }, stdio: 'ignore',
  });
  for (let i = 0; i < 60; i++) {
    try { if ((await fetch(`${base}/up`)).ok) break; } catch { /* not up yet */ }
    await new Promise((r) => setTimeout(r, 250));
  }

  const browser = await chromium.launch({ executablePath: process.env.PLAYWRIGHT_CHROMIUM || undefined });

  return {
    base,
    browser,
    /** A page that records console errors, JS errors and CSP violations; external hosts are blocked. */
    async page({ width = 1440, height = 900, theme = 'light' } = {}) {
      const context = await browser.newContext({ viewport: { width, height } });
      const page = await context.newPage();
      page.problems = [];
      page.on('pageerror', (e) => page.problems.push(`pageerror: ${e.message}`));
      page.on('console', (m) => { if (m.type() === 'error' && !/ERR_FAILED|ERR_BLOCKED|net::/.test(m.text())) page.problems.push(`console: ${m.text()}`); });
      await page.route(/^https?:\/\/(?!127\.0\.0\.1)/, (route) => route.abort());   // fonts, placeholder photos, WhatsApp
      await page.addInitScript((t) => {
        try { if (!localStorage.getItem('travelorio-theme')) localStorage.setItem('travelorio-theme', t); } catch { /* storage blocked */ }
        window.__csp = [];
        document.addEventListener('securitypolicyviolation', (e) => window.__csp.push(`${e.violatedDirective} ${e.blockedURI}`));
        window.__opened = [];
        window.open = (url) => { window.__opened.push(String(url)); return { location: { set href(v) { window.__opened.push(String(v)); } }, close() { window.__opened.push('CLOSED'); }, opener: null }; };
      }, theme);
      return page;
    },
    async close() {
      await browser.close();
      server.kill();
      rmSync(dir, { recursive: true, force: true });
    },
  };
}

export const PAGES = [
  '/', '/packages', '/reviews', '/contact', '/why-us', '/blog', '/blog/cox-bazar-3-days',
  ...['coxs-bazar', 'sundarbans', 'sylhet', 'bandarban', 'saint-martin', 'kuakata'].map((s) => `/destinations/${s}`),
];
