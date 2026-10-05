/* =========================================================
   TravelOrio — destination page
   Reads TRAVELORIO_PAGE.slug (set by the Blade view), fills the page from data.js
   (+ Bangla overlay from js/i18n/data-bn.js), updates the page
   title/meta and runs the gallery lightbox. Re-renders when the
   language changes.
   Load order: data.js, bn.js, data-bn.js, core.js, THIS, main.js
   ========================================================= */
(function () {
  "use strict";

  var places = window.TRAVELORIO_DESTINATIONS;
  if (!places || !places.length || !window.TO) return;
  var TO = window.TO;
  var t = TO.t;

  var $ = function (sel, root) { return (root || document).querySelector(sel); };
  var $$ = function (sel, root) { return Array.prototype.slice.call((root || document).querySelectorAll(sel)); };
  var esc = function (s) { return String(s).replace(/[&<>"']/g, function (c) { return ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" })[c]; }); };

  var slug = (window.TRAVELORIO_PAGE || {}).slug;
  var base = places.filter(function (p) { return p.slug === slug; })[0] || places[0];
  var IMG = window.TRAVELORIO_IMG;

  var main = $("main[data-dest]");
  if (main) main.dataset.dest = base.slug;

  var fill = function (key, html) { var el = $('[data-field="' + key + '"]'); if (el) el.innerHTML = html; };
  var li = function (items) { return items.map(function (x) { return "<li>" + esc(x) + "</li>"; }).join(""); };

  function render() {
    var d = TO.place(base);

    /* ----- page meta ----- */
    document.title = t("{name} Tour Packages | TravelOrio", { name: d.name });
    var setMeta = function (sel, v) { var el = $(sel); if (el) el.setAttribute("content", v); };
    setMeta('meta[name="description"]', t("Plan your {name} trip with TravelOrio: itinerary, what's included, best time to visit, travel tips and instant booking on WhatsApp. {tagline}", { name: d.name, tagline: d.tagline }));
    setMeta('meta[property="og:title"]', t("{name} Tour Packages | TravelOrio", { name: d.name }));
    setMeta('meta[property="og:description"]', d.tagline);

    /* ----- simple text ----- */
    var text = { name: d.name, region: d.region, tagline: d.tagline, overviewTitle: d.overviewTitle, duration: d.duration, bestTime: d.bestTime, distance: d.distance, style: d.style };
    Object.keys(text).forEach(function (k) { $$('[data-field="' + k + '"]').forEach(function (el) { el.textContent = text[k]; }); });

    var priceFact = $('[data-field="priceFact"]');
    if (priceFact) priceFact.innerHTML = TO.money(d.price) + " <small>" + esc(t("/ person")) + "</small>";
    var priceCard = $('[data-field="priceCard"]');
    if (priceCard) priceCard.innerHTML = '<span class="package__currency">৳</span>' + TO.num(d.price) + "<small> " + esc(t("/ person")) + "</small>";

    var hero = $('[data-field="heroImage"]');
    if (hero) hero.src = d.heroImage;
    var destInput = $('[data-field="destinationInput"]');
    if (destInput) destInput.value = base.name;                     // stays English; translated when the WhatsApp message is built

    var cta = $('[data-field="ctaTitle"]');
    if (cta) cta.innerHTML = t("Ready for your {name} escape?", { name: "<em>" + esc(d.name) + "</em>" });

    /* ----- lists and blocks ----- */
    fill("overview", d.overview.map(function (p) { return "<p>" + esc(p) + "</p>"; }).join(""));
    fill("highlights", li(d.highlights));
    fill("included", li(d.included));
    fill("excluded", li(d.excluded));
    fill("tips", li(d.tips));
    fill("transport", d.transport.map(function (x) { return "<li>" + x + "</li>"; }).join(""));   // trusted markup (<strong>) from data

    fill("attractions", d.attractions.map(function (a) {
      return '<article class="attraction"><img src="' + esc(a.img) + '" alt="' + esc(a.name) + '" loading="lazy" width="640" height="420">' +
        '<div class="attraction__body"><h3>' + esc(a.name) + "</h3><p>" + esc(a.text) + "</p></div></article>";
    }).join(""));

    fill("itinerary", d.itinerary.map(function (day, i) {
      return '<details class="itinerary__day"' + (i === 0 ? " open" : "") + "><summary>" +
        '<span class="itinerary__num">' + esc(t("Day {n}", { n: TO.num(i + 1) })) + "</span>" +
        '<span class="itinerary__title">' + esc(day.title) + "</span></summary>" +
        '<div class="itinerary__body"><ul>' + li(day.items) + "</ul></div></details>";
    }).join(""));

    fill("seasons", d.seasons.map(function (s) {
      return '<div class="season season--' + esc(s.tone) + '"><span class="season__badge">' + esc(s.badge) + "</span><h3>" + esc(s.range) + "</h3><p>" + esc(s.text) + "</p></div>";
    }).join(""));

    fill("faq", d.faq.map(function (f) {
      return '<details class="faq__item"><summary>' + esc(f.q) + "</summary><p>" + esc(f.a) + "</p></details>";
    }).join(""));

    fill("gallery", d.gallery.map(function (alt, i) {
      var wide = i === 0 || i === 5;
      var sm = wide ? [800, 520] : [520, 520], big = wide ? [1600, 1000] : [1200, 1200];
      return '<a class="gallery__item' + (wide ? " gallery__item--wide" : "") + '" href="' + esc(IMG(base.slug, "g" + (i + 1), big[0], big[1])) + '">' +
        '<img src="' + esc(IMG(base.slug, "g" + (i + 1), sm[0], sm[1])) + '" alt="' + esc(alt) + '" loading="lazy" width="' + sm[0] + '" height="' + sm[1] + '"></a>';
    }).join(""));

    fill("related", base.related.map(function (s) {
      var src = places.filter(function (p) { return p.slug === s; })[0];
      return src ? TO.place(src) : null;
    }).filter(Boolean).map(function (r) {
      var href = esc(((window.TRAVELORIO_ROUTES || {}).destination || "/destinations") + "/" + r.slug);
      return '<article class="card destination" data-reveal>' +
        '<a href="' + href + '" class="card__media" aria-label="' + esc(t("View {name} trip details", { name: r.name })) + '">' +
        '<img src="' + esc(r.cardImage) + '" alt="' + esc(r.name) + '" loading="lazy" width="800" height="600">' +
        '<span class="card__tag">' + esc(t("From {price}", { price: TO.money(r.price) })) + "</span></a>" +
        '<div class="card__body"><p class="card__meta">' + esc(r.region) + " · " + esc(r.duration) + "</p>" +
        '<h3 class="card__title">' + esc(r.name) + "</h3>" +
        '<a href="' + href + '" class="link-arrow">' + esc(t("View details")) + ' <span aria-hidden="true">→</span></a></div></article>';
    }).join(""));

    /* ----- WhatsApp call-to-action ----- */
    var wa = $(".cta-band .btn");
    if (wa) {
      var href = (wa.getAttribute("href") || "").split("?")[0];
      wa.setAttribute("href", href + "?text=" + encodeURIComponent(t("Hello TravelOrio! I'm interested in a {name} trip.", { name: d.name })));
    }

    /* reveal for freshly rendered cards (main.js has already set up its observer) */
    $$("[data-field=related] [data-reveal]").forEach(function (el) { el.classList.add("is-visible"); });
  }

  /* ---------- Gallery lightbox (built once, items read on click) ---------- */
  function setupLightbox() {
    if (typeof HTMLDialogElement === "undefined") return;
    var box = document.createElement("dialog");
    box.className = "lightbox";
    box.setAttribute("data-no-i18n", "");
    box.innerHTML =
      '<button class="lightbox__btn lightbox__close" type="button">&times;</button>' +
      '<button class="lightbox__btn lightbox__prev" type="button">&#8249;</button>' +
      '<figure class="lightbox__fig"><img class="lightbox__img" alt=""><figcaption class="lightbox__cap"></figcaption></figure>' +
      '<button class="lightbox__btn lightbox__next" type="button">&#8250;</button>';
    document.body.appendChild(box);

    var imgEl = $(".lightbox__img", box), capEl = $(".lightbox__cap", box);
    var current = 0;
    var items = function () { return $$(".gallery__item"); };

    function labels() {
      box.setAttribute("aria-label", t("Photo viewer"));
      $(".lightbox__close", box).setAttribute("aria-label", t("Close"));
      $(".lightbox__prev", box).setAttribute("aria-label", t("Previous photo"));
      $(".lightbox__next", box).setAttribute("aria-label", t("Next photo"));
    }
    function show(i) {
      var list = items();
      current = (i + list.length) % list.length;
      var a = list[current];
      imgEl.src = a.getAttribute("href");
      imgEl.alt = $("img", a).alt;
      capEl.textContent = imgEl.alt + " · " + TO.num(current + 1) + " / " + TO.num(list.length);
    }
    labels();
    document.addEventListener("travelorio:langchange", function () { labels(); if (box.open) show(current); });

    var gallery = $("#gallery");
    if (gallery) gallery.addEventListener("click", function (e) {
      var a = e.target.closest(".gallery__item");
      if (!a) return;
      e.preventDefault();
      show(items().indexOf(a));
      box.showModal();
    });
    $(".lightbox__close", box).addEventListener("click", function () { box.close(); });
    $(".lightbox__prev", box).addEventListener("click", function () { show(current - 1); });
    $(".lightbox__next", box).addEventListener("click", function () { show(current + 1); });
    box.addEventListener("click", function (e) { if (e.target === box) box.close(); });
    box.addEventListener("keydown", function (e) {
      if (e.key === "ArrowLeft") show(current - 1);
      if (e.key === "ArrowRight") show(current + 1);
    });
  }

  render();
  setupLightbox();
  document.addEventListener("travelorio:langchange", render);
})();
