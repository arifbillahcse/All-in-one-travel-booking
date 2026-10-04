/* =========================================================
   TravelOrio — destination page
   Reads ?place=<slug>, fills destination.html from js/data.js,
   updates the page title/meta, and adds the gallery lightbox.
   Must load AFTER data.js and BEFORE main.js (so main.js sees
   the rendered [data-reveal] elements and the #trip-form).
   ========================================================= */
(function () {
  "use strict";

  const places = window.TRAVELORIO_DESTINATIONS;
  if (!places || !places.length) return;

  const $ = (sel, root = document) => root.querySelector(sel);
  const $$ = (sel, root = document) => Array.from(root.querySelectorAll(sel));
  const esc = (s) => String(s).replace(/[&<>"']/g, (c) => ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" }[c]));
  const money = (n) => "৳" + Number(n).toLocaleString("en-US");

  const slug = new URLSearchParams(window.location.search).get("place");
  const d = places.find((p) => p.slug === slug) || places[0];
  const bySlug = (s) => places.find((p) => p.slug === s);

  const main = $("main[data-dest]");
  if (main) main.dataset.dest = d.slug;

  /* ---------- Page meta ---------- */
  document.title = `${d.name} Tour Packages | TravelOrio`;
  const setMeta = (sel, value) => { const el = $(sel); if (el) el.setAttribute("content", value); };
  setMeta('meta[name="description"]', `Plan your ${d.name} trip with TravelOrio: itinerary, what's included, best time to visit, travel tips and instant booking on WhatsApp. ${d.tagline}`);
  setMeta('meta[property="og:title"]', `${d.name} Tour Packages | TravelOrio`);
  setMeta('meta[property="og:description"]', d.tagline);

  /* ---------- Simple text fields ---------- */
  const text = {
    name: d.name,
    region: d.region,
    tagline: d.tagline,
    duration: d.duration,
    bestTime: d.bestTime,
    distance: d.distance,
    style: d.style,
  };
  Object.entries(text).forEach(([key, value]) => {
    $$(`[data-field="${key}"]`).forEach((el) => { el.textContent = value; });
  });

  const priceFact = $('[data-field="priceFact"]');
  if (priceFact) priceFact.innerHTML = `${money(d.price)} <small>/ person</small>`;
  const priceCard = $('[data-field="priceCard"]');
  if (priceCard) priceCard.innerHTML = `<span class="package__currency">৳</span>${Number(d.price).toLocaleString("en-US")}<small> / person</small>`;

  const hero = $('[data-field="heroImage"]');
  if (hero) hero.src = d.heroImage;

  const destInput = $('[data-field="destinationInput"]');
  if (destInput) destInput.value = d.name;

  /* ---------- Lists and blocks ---------- */
  const fill = (key, html) => { const el = $(`[data-field="${key}"]`); if (el) el.innerHTML = html; };
  const li = (items) => items.map((t) => `<li>${esc(t)}</li>`).join("");

  fill("overview", d.overview.map((p) => `<p>${esc(p)}</p>`).join(""));
  fill("highlights", li(d.highlights));
  fill("included", li(d.included));
  fill("excluded", li(d.excluded));
  fill("tips", li(d.tips));
  fill("transport", d.transport.map((t) => `<li>${t}</li>`).join(""));   // trusted markup from data.js (<strong>)

  fill("attractions", d.attractions.map((a) => `
    <article class="attraction">
      <img src="${esc(a.img)}" alt="${esc(a.name)}" loading="lazy" width="640" height="420">
      <div class="attraction__body">
        <h3>${esc(a.name)}</h3>
        <p>${esc(a.text)}</p>
      </div>
    </article>`).join(""));

  fill("itinerary", d.itinerary.map((day, i) => `
    <details class="itinerary__day"${i === 0 ? " open" : ""}>
      <summary>
        <span class="itinerary__num">Day ${i + 1}</span>
        <span class="itinerary__title">${esc(day.title)}</span>
      </summary>
      <div class="itinerary__body"><ul>${li(day.items)}</ul></div>
    </details>`).join(""));

  fill("seasons", d.seasons.map((s) => `
    <div class="season season--${esc(s.tone)}">
      <span class="season__badge">${esc(s.badge)}</span>
      <h3>${esc(s.range)}</h3>
      <p>${esc(s.text)}</p>
    </div>`).join(""));

  fill("faq", d.faq.map((f) => `
    <details class="faq__item">
      <summary>${esc(f.q)}</summary>
      <p>${esc(f.a)}</p>
    </details>`).join(""));

  const IMG = window.TRAVELORIO_IMG;
  fill("gallery", d.gallery.map((alt, i) => {
    const wide = i === 0 || i === 5 ? " gallery__item--wide" : "";
    const small = i === 0 || i === 5 ? [800, 520] : [520, 520];
    const big = i === 0 || i === 5 ? [1600, 1000] : [1200, 1200];
    return `
    <a class="gallery__item${wide}" href="${esc(IMG(d.slug, "g" + (i + 1), big[0], big[1]))}">
      <img src="${esc(IMG(d.slug, "g" + (i + 1), small[0], small[1]))}" alt="${esc(alt)}" loading="lazy" width="${small[0]}" height="${small[1]}">
    </a>`;
  }).join(""));

  fill("related", d.related.map(bySlug).filter(Boolean).map((r) => `
    <article class="card destination" data-reveal>
      <a href="destination.html?place=${esc(r.slug)}" class="card__media" aria-label="View ${esc(r.name)} trip details">
        <img src="${esc(r.cardImage)}" alt="${esc(r.name)}" loading="lazy" width="800" height="600">
        <span class="card__tag">From ${money(r.price)}</span>
      </a>
      <div class="card__body">
        <p class="card__meta">${esc(r.region)} · ${esc(r.duration)}</p>
        <h3 class="card__title">${esc(r.name)}</h3>
        <a href="destination.html?place=${esc(r.slug)}" class="link-arrow">View details <span aria-hidden="true">→</span></a>
      </div>
    </article>`).join(""));

  /* ---------- Footer / CTA tweak ---------- */
  const trustWa = $(".cta-band .btn");
  if (trustWa) {
    const base = trustWa.getAttribute("href");
    const msg = encodeURIComponent(`Hello TravelOrio! I'm interested in a ${d.name} trip.`);
    trustWa.setAttribute("href", `${base.split("?")[0]}?text=${msg}`);
  }


  /* ---------- Gallery lightbox ---------- */
  const items = $$(".gallery__item");
  if (items.length && typeof HTMLDialogElement !== "undefined") {
    const box = document.createElement("dialog");
    box.className = "lightbox";
    box.setAttribute("aria-label", "Photo viewer");
    box.innerHTML = `
      <button class="lightbox__btn lightbox__close" type="button" aria-label="Close">&times;</button>
      <button class="lightbox__btn lightbox__prev" type="button" aria-label="Previous photo">&#8249;</button>
      <figure class="lightbox__fig">
        <img class="lightbox__img" alt="">
        <figcaption class="lightbox__cap"></figcaption>
      </figure>
      <button class="lightbox__btn lightbox__next" type="button" aria-label="Next photo">&#8250;</button>`;
    document.body.appendChild(box);

    const imgEl = $(".lightbox__img", box);
    const capEl = $(".lightbox__cap", box);
    let current = 0;

    const show = (i) => {
      current = (i + items.length) % items.length;
      const a = items[current];
      imgEl.src = a.getAttribute("href");
      imgEl.alt = $("img", a).alt;
      capEl.textContent = `${imgEl.alt} · ${current + 1} / ${items.length}`;
    };

    items.forEach((a, i) => a.addEventListener("click", (e) => {
      e.preventDefault();
      show(i);
      box.showModal();
    }));
    $(".lightbox__close", box).addEventListener("click", () => box.close());
    $(".lightbox__prev", box).addEventListener("click", () => show(current - 1));
    $(".lightbox__next", box).addEventListener("click", () => show(current + 1));
    box.addEventListener("click", (e) => { if (e.target === box) box.close(); });   // click on backdrop
    box.addEventListener("keydown", (e) => {
      if (e.key === "ArrowLeft") show(current - 1);
      if (e.key === "ArrowRight") show(current + 1);
    });
  }
})();
