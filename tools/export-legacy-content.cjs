/*
 * One-time exporter: reads the legacy browser data files in public/assets/js
 * and writes English + Bangla content as JSON for the database seeders.
 *
 *   node tools/export-legacy-content.cjs
 *
 * Output: database/seeders/data/{destinations,reviews,posts,bn-dictionary}.json
 */
const fs = require('fs');
const path = require('path');
const vm = require('vm');

const JS = path.join(__dirname, '..', 'public', 'assets', 'js');
const OUT = path.join(__dirname, '..', 'database', 'seeders', 'data');

const ctx = { window: {}, console };
ctx.window.window = ctx.window;
vm.createContext(ctx);
for (const f of ['data.js', 'blog-core.js', 'i18n/bn.js', 'i18n/data-bn.js', 'i18n/blog-bn.js']) {
  try {
    vm.runInContext(fs.readFileSync(path.join(JS, f), 'utf8'), ctx, { filename: f });
  } catch (e) {
    // blog-core.js touches the DOM-less helpers only at call time; ignore load-time browser APIs
    if (!/document|TO is not defined|TO\./.test(String(e))) throw e;
  }
}
const w = ctx.window;

const bnPlaces = w.TRAVELORIO_DATA_BN.bn.places;
const bnReviews = w.TRAVELORIO_DATA_BN.bn.reviews;

const destinations = w.TRAVELORIO_DESTINATIONS.map((p, i) => {
  const { img, ...en } = p;
  return { sort: i + 1, en, bn: bnPlaces[p.slug] };
});

const reviews = w.TRAVELORIO_REVIEWS.map((r, i) => ({ sort: i + 1, en: r, bn: bnReviews[i] }));

const bnPosts = w.TRAVELORIO_BLOG_BN.bn.posts;
const posts = w.TRAVELORIO_POSTS.map((p) => ({ en: p, bn: bnPosts[p.slug] }));

fs.mkdirSync(OUT, { recursive: true });
const write = (n, d) => fs.writeFileSync(path.join(OUT, n), JSON.stringify(d, null, 1) + '\n');
write('destinations.json', destinations);
write('reviews.json', reviews);
write('posts.json', posts);
write('bn-dictionary.json', w.TRAVELORIO_I18N.bn);
console.log({ destinations: destinations.length, reviews: reviews.length, posts: posts.length, dictionary: Object.keys(w.TRAVELORIO_I18N.bn).length });
