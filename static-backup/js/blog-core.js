/* =========================================================
   TravelOrio — blog data + shared helpers
   ---------------------------------------------------------
   TO ADD AN ARTICLE: add an object to the `posts` array below
   (newest first is not required, they are sorted by date).
   Body blocks: {type:"p"|"h2"|"tip", text} or {type:"ul", items:[]}.
   Add the Bangla version in js/i18n/blog-bn.js using the same slug
   and the same number/order of body blocks.
   Optional: destination = a destination slug (shows a "Plan this trip" card).
   Images are placeholders from picsum.photos; swap blogImg() for real photos.
   ========================================================= */
(function () {
  "use strict";

  var posts = [
 {
  "slug": "cox-bazar-3-days",
  "category": "Guides",
  "date": "2026-03-12",
  "readMins": 6,
  "destination": "coxs-bazar",
  "title": "Cox's Bazar in 3 days: a relaxed itinerary",
  "excerpt": "How to see the beach, the hills and a quiet island without rushing, with timing tips from our local guides.",
  "body": [
   {
    "type": "p",
    "text": "Three days is enough to feel the rhythm of Cox's Bazar without turning the trip into a checklist. This plan keeps the mornings slow, saves the best light for the evenings and leaves room for a long seafood dinner."
   },
   {
    "type": "h2",
    "text": "Day 1: Settle in and meet the sea"
   },
   {
    "type": "p",
    "text": "Arrive, check in and walk straight to Laboni Beach. Swim in the flagged zone, then stay for sunset, when the sky turns orange and the crowd thins out. Dinner at one of the seaside grills is the perfect first night."
   },
   {
    "type": "h2",
    "text": "Day 2: Hills and quiet beaches"
   },
   {
    "type": "p",
    "text": "Drive along Marine Drive in the morning, stop at Himchari for the waterfall and the viewpoint, and have lunch at Inani, where the rocky shore is calmer and less crowded."
   },
   {
    "type": "tip",
    "text": "Start before 8 AM. The road is empty, the light is soft and you will beat the tour buses to Himchari."
   },
   {
    "type": "h2",
    "text": "Day 3: A day on Sonadia"
   },
   {
    "type": "p",
    "text": "Take an early boat to Sonadia Island for birds, shells and a picnic on an almost empty beach. Come back by afternoon, buy dried fish or pearls at the market and catch one last sunset."
   },
   {
    "type": "h2",
    "text": "What to pack"
   },
   {
    "type": "ul",
    "items": [
     "Sunscreen and a hat",
     "Light, quick-dry clothes",
     "Water shoes for rocky beaches",
     "Cash for small shops and tuk-tuks"
    ]
   },
   {
    "type": "p",
    "text": "Want this plan arranged for you, with hotel, transport and guide? Use the button below and we will build it around your dates."
   }
  ]
 },
 {
  "slug": "sundarbans-what-to-expect",
  "category": "Travel tips",
  "date": "2026-02-20",
  "readMins": 7,
  "destination": "sundarbans",
  "title": "Sundarbans: what to expect (and what not to)",
  "excerpt": "Tigers are shy, mornings are magical and the mosquitoes are real. An honest guide to your first mangrove cruise.",
  "body": [
   {
    "type": "p",
    "text": "The Sundarbans is unlike anywhere else in Bangladesh: a maze of tidal creeks, mangrove roots and silence. A little preparation makes the difference between a good trip and a great one."
   },
   {
    "type": "h2",
    "text": "The tiger question"
   },
   {
    "type": "p",
    "text": "Seeing a Royal Bengal tiger is very rare, because the animals avoid people. What you will see are fresh tracks in the mud, spotted deer, monkeys, crocodiles and a lot of birds. Go for the forest, not for a checklist."
   },
   {
    "type": "h2",
    "text": "A day on the launch"
   },
   {
    "type": "p",
    "text": "Mornings start with a quiet creek cruise in a small boat, when the animals come to the water. After breakfast you walk short forest trails with an armed forest guard, then climb a watchtower for views over the canopy."
   },
   {
    "type": "ul",
    "items": [
     "Early creek cruises are the best time for wildlife",
     "Stay quiet on deck and in the small boats",
     "Never step away from the guard on land"
    ]
   },
   {
    "type": "tip",
    "text": "Pack mosquito repellent, a hat and a light rain jacket. Leave plastic behind or bring it all back with you."
   },
   {
    "type": "h2",
    "text": "When to go"
   },
   {
    "type": "p",
    "text": "November to February is the most comfortable season. In the wettest months the forest is often closed to visitors, so always check current rules before you book. We handle permits for every trip."
   }
  ]
 },
 {
  "slug": "best-time-to-visit-bangladesh",
  "category": "Planning",
  "date": "2026-01-15",
  "readMins": 5,
  "title": "The best time to visit Bangladesh's six destinations",
  "excerpt": "Winter is the sweet spot for most places, but there are good reasons to travel in the green season too.",
  "body": [
   {
    "type": "p",
    "text": "Bangladesh has three seasons that matter to travelers: a cool, dry winter, a hot pre-monsoon and a green, rainy monsoon. Where you are going decides when you should go."
   },
   {
    "type": "h2",
    "text": "Winter is the sweet spot"
   },
   {
    "type": "p",
    "text": "From November to February the weather is cool and clear almost everywhere. This is also the busiest time, so book hotels early, especially around long weekends."
   },
   {
    "type": "ul",
    "items": [
     "Cox's Bazar: November to March",
     "Sundarbans: November to February",
     "Sylhet: October to March",
     "Bandarban: October to March",
     "Saint Martin's Island: November to February, when ships run",
     "Kuakata: November to March"
    ]
   },
   {
    "type": "h2",
    "text": "Do not write off the monsoon"
   },
   {
    "type": "p",
    "text": "Sylhet's Ratargul swamp forest is at its best from June to September, when the water is high enough for boats to glide between the trees. The hills are also at their greenest, and prices are lower. Just pack a raincoat and stay flexible."
   },
   {
    "type": "tip",
    "text": "Book early for Eid and winter weekends. The good rooms go first."
   }
  ]
 },
 {
  "slug": "saint-martin-ship-rules-packing",
  "category": "Guides",
  "date": "2025-12-18",
  "readMins": 6,
  "destination": "saint-martin",
  "title": "Saint Martin's Island: the ship, the rules and what to pack",
  "excerpt": "Everything first-time visitors ask about getting to Bangladesh's only coral island, and how to behave once you are there.",
  "body": [
   {
    "type": "p",
    "text": "Saint Martin's is a small island with a big reputation. Getting there takes a bit of planning, but the clear water and slow pace are worth it."
   },
   {
    "type": "h2",
    "text": "Getting there"
   },
   {
    "type": "p",
    "text": "Most trips go by road from Cox's Bazar to Teknaf along Marine Drive, then by ship across the bay. The ship takes roughly two to three hours and usually runs only in the cooler months."
   },
   {
    "type": "tip",
    "text": "Ship schedules and overnight-stay rules can change from season to season. We confirm the latest information with you before you book."
   },
   {
    "type": "h2",
    "text": "Respect the island"
   },
   {
    "type": "p",
    "text": "The coral is fragile. Please do not collect shells, corals or live creatures, do not litter and keep music low at night. The village depends on the same clean beach you came to see."
   },
   {
    "type": "h2",
    "text": "What to pack"
   },
   {
    "type": "ul",
    "items": [
     "Cash, since there are no ATMs on the island",
     "Sunscreen, sunglasses and a hat",
     "A power bank",
     "A light jacket for the ship and windy evenings"
    ]
   },
   {
    "type": "p",
    "text": "If you would like the whole trip arranged, including the road, the ship and the stay, message us and we will plan it around the sailing dates."
   }
  ]
 },
 {
  "slug": "bandarban-first-timers",
  "category": "Guides",
  "date": "2025-11-30",
  "readMins": 6,
  "destination": "bandarban",
  "title": "A first-timer's guide to Bandarban",
  "excerpt": "Cloud-level sunrises, waterfalls and warm hill communities: how to plan a first trip to the hills.",
  "body": [
   {
    "type": "p",
    "text": "Bandarban is Bangladesh's high country, with steep roads, deep valleys and mornings that begin above the clouds. It rewards a little preparation."
   },
   {
    "type": "h2",
    "text": "Getting around"
   },
   {
    "type": "p",
    "text": "Hill roads are narrow and steep, so locals use sturdy open jeeps, known as Chander Gari. We book them with an experienced driver. Expect winding journeys, and bring motion-sickness tablets if you need them."
   },
   {
    "type": "h2",
    "text": "Must-do stops"
   },
   {
    "type": "ul",
    "items": [
     "Nilgiri for a sunrise above the clouds",
     "Nafakhum waterfall, reached by boat and a short walk",
     "The Golden Temple, one of the largest Buddhist temples in the country",
     "A village visit with a local guide"
    ]
   },
   {
    "type": "tip",
    "text": "Ask before photographing people, and dress modestly in villages and temples."
   },
   {
    "type": "h2",
    "text": "Permits and safety"
   },
   {
    "type": "p",
    "text": "Some areas and treks need permission from the local administration. We arrange this for you and confirm the latest rules before your trip."
   },
   {
    "type": "p",
    "text": "Plan on three days so you are not rushing the roads, and travel between October and March for the clearest skies."
   }
  ]
 },
 {
  "slug": "sylhet-tea-and-food",
  "category": "Food & culture",
  "date": "2025-11-05",
  "readMins": 4,
  "destination": "sylhet",
  "title": "Sylhet on a plate: seven-layer tea and more",
  "excerpt": "A short taste tour of the north-east: what to drink, what to order and where the tea comes from.",
  "body": [
   {
    "type": "p",
    "text": "Sylhet is green, misty and delicious. Between tea gardens and swamp forests, food is half the reason people come back."
   },
   {
    "type": "h2",
    "text": "Start with the tea"
   },
   {
    "type": "p",
    "text": "The famous seven-layer tea is served in a tall glass with colourful bands that stay separate until you stir. It is sweet, spiced and best enjoyed after a morning walk through a tea estate."
   },
   {
    "type": "h2",
    "text": "What to eat"
   },
   {
    "type": "ul",
    "items": [
     "Shatkora beef, a tangy curry flavoured with a local citrus fruit",
     "Akhni, a fragrant Sylheti rice dish",
     "Fresh river fish at a riverside restaurant",
     "Homemade pitha in winter"
    ]
   },
   {
    "type": "tip",
    "text": "Ask your guide for their favourite small restaurant. The best meals are often in the plainest places."
   },
   {
    "type": "h2",
    "text": "Visit the source"
   },
   {
    "type": "p",
    "text": "Tea estates like Malnicherra are open to visitors, with winding paths and viewpoints. Go early, when the hills are wrapped in mist."
   }
  ]
 }
];

  var blogImg = function (slug, w, h) { return "https://picsum.photos/seed/blog-" + slug + "/" + w + "/" + h; };
  var esc = function (s) { return String(s).replace(/[&<>"']/g, function (c) { return ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" })[c]; }); };

  function bnFor(slug) {
    var all = (window.TRAVELORIO_BLOG_BN || {}).bn || {};
    return (all.posts || {})[slug];
  }

  /* Localised copy of one post (Bangla overlay merged by index over the English blocks) */
  function localize(p) {
    var TO = window.TO;
    if (!TO || TO.lang === "en") return p;
    return TO.localize(p, bnFor(p.slug));
  }

  /* All posts, newest first, localised */
  function list() {
    return posts.slice().sort(function (a, b) { return b.date.localeCompare(a.date); }).map(localize);
  }
  function get(slug) {
    var p = posts.filter(function (x) { return x.slug === slug; })[0];
    return p ? localize(p) : null;
  }
  function categories() {
    var seen = {}, out = [];
    posts.forEach(function (p) { if (!seen[p.category]) { seen[p.category] = 1; out.push(p.category); } });
    return out;
  }
  var href = function (p) { return "blog-post.html?post=" + encodeURIComponent(p.slug); };

  /* Card used on the blog page, in "related" and on the home page */
  function card(p, opts) {
    var TO = window.TO, t = TO.t;
    opts = opts || {};
    return '<article class="blog-card' + (opts.featured ? " blog-card--featured" : "") + '">' +
      '<a class="blog-card__media" href="' + href(p) + '" tabindex="-1" aria-hidden="true">' +
        '<img src="' + esc(blogImg(p.slug, opts.featured ? 1000 : 720, opts.featured ? 700 : 480)) + '" alt="" loading="lazy" width="' + (opts.featured ? 1000 : 720) + '" height="' + (opts.featured ? 700 : 480) + '">' +
        '<span class="blog-card__cat">' + esc(t(p.category)) + "</span></a>" +
      '<div class="blog-card__body">' +
        '<p class="blog-card__meta">' + esc(TO.fmtDate(p.date)) + " · " + esc(t("{n} min read", { n: TO.num(p.readMins) })) + "</p>" +
        '<h3 class="blog-card__title"><a href="' + href(p) + '">' + esc(p.title) + "</a></h3>" +
        '<p class="blog-card__excerpt">' + esc(p.excerpt) + "</p>" +
        '<a class="link-arrow" href="' + href(p) + '">' + esc(t("Read article")) + ' <span aria-hidden="true">→</span></a>' +
      "</div></article>";
  }

  window.TRAVELORIO_POSTS = posts;
  window.TravelBlog = { list: list, get: get, categories: categories, card: card, href: href, img: blogImg, esc: esc };
})();
