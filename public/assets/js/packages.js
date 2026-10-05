/* =========================================================
   TravelOrio — packages page
   1. Renders the "by destination" cards from js/data.js
   2. Prefills the form from ?plan=<name>&place=<slug>
   3. Live estimate (plan price x travelers)
   All text follows the active language (re-renders on change).
   Load order: data.js, bn.js, data-bn.js, core.js, THIS, main.js
   ========================================================= */
(function () {
  "use strict";

  if (!window.TO) return;
  var TO = window.TO, t = TO.t;

  var $ = function (sel, root) { return (root || document).querySelector(sel); };
  var esc = function (s) { return String(s).replace(/[&<>"']/g, function (c) { return ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" })[c]; }); };

  var places = window.TRAVELORIO_DESTINATIONS || [];

  /* ---------- 1. Destination cards ---------- */
  var cards = $("#destination-cards");
  function renderCards() {
    if (!cards || !places.length) return;
    cards.innerHTML = places.map(function (src) {
      var p = TO.place(src);
      var href = esc(((window.TRAVELORIO_ROUTES || {}).destination || "/destinations") + "/" + p.slug);
      return '<article class="card destination" data-reveal>' +
        '<a href="' + href + '" class="card__media" aria-label="' + esc(t("View {name} trip details", { name: p.name })) + '">' +
        '<img src="' + esc(p.cardImage) + '" alt="' + esc(p.name) + '" loading="lazy" width="800" height="600">' +
        '<span class="card__tag">' + esc(t("From {price}", { price: TO.money(p.price) })) + "</span></a>" +
        '<div class="card__body"><p class="card__meta">' + esc(p.region) + " · " + esc(p.duration) + "</p>" +
        '<h3 class="card__title">' + esc(p.name) + "</h3>" +
        '<p class="card__text">' + esc(p.tagline) + "</p>" +
        '<a href="' + href + '" class="link-arrow">' + esc(t("View trip details")) + ' <span aria-hidden="true">→</span></a></div></article>';
    }).join("");
    // cards rendered after main.js started observing would stay hidden, so show them directly
    Array.prototype.forEach.call(cards.querySelectorAll("[data-reveal]"), function (el) { el.classList.add("is-visible"); });
  }

  /* ---------- 2. Prefill from the URL ---------- */
  var pkgSelect = $("#b-package");
  var destSelect = $("#b-destination");
  var guestsInput = $("#b-guests");
  var params = new URLSearchParams(window.location.search);

  function pick(select, value) {
    if (!select || !value) return;
    var opt = Array.prototype.filter.call(select.options, function (o) { return o.value === value; })[0];
    if (opt) select.value = opt.value;
  }
  pick(pkgSelect, params.get("plan"));
  var placeSlug = params.get("place");
  var placeObj = places.filter(function (p) { return p.slug === placeSlug; })[0];
  if (placeObj) pick(destSelect, placeObj.name);
  if (guestsInput && params.get("guests")) {
    var n = parseInt(params.get("guests"), 10);
    if (n >= 1 && n <= 50) guestsInput.value = n;
  }

  /* ---------- 3. Live estimate ---------- */
  var totalEl = $("#estimate-total");
  var noteEl = $("#estimate-note");
  var hidden = $("#b-estimate");
  var noteDefault = noteEl ? noteEl.textContent : "";      // English source text (walker translates it)

  function updateEstimate() {
    if (!pkgSelect || !totalEl) return;
    var price = parseInt(pkgSelect.selectedOptions[0].dataset.price || "", 10);
    var guests = parseInt(guestsInput.value, 10);
    var label = "";

    if (!price) {
      totalEl.textContent = t("Choose a plan");
      if (noteEl) noteEl.textContent = t(noteDefault);
    } else if (!guests || guests < 1) {
      totalEl.textContent = t("{price} per person", { price: TO.money(price) });
      if (noteEl) noteEl.textContent = t(noteDefault);
    } else {
      var total = price * guests;
      label = t("{total} ({n} × {price})", { total: TO.money(total), n: TO.num(guests), price: TO.money(price) });
      totalEl.textContent = TO.money(total);
      if (noteEl) noteEl.textContent = t(guests === 1
        ? "{n} traveler × {price}. A rough guide only, as your final quote may differ by destination and season."
        : "{n} travelers × {price}. A rough guide only, as your final quote may differ by destination and season.",
        { n: TO.num(guests), price: TO.money(price) });
    }
    if (hidden) hidden.value = label;
  }

  if (pkgSelect && guestsInput) {
    ["change", "input"].forEach(function (evt) {
      pkgSelect.addEventListener(evt, updateEstimate);
      guestsInput.addEventListener(evt, updateEstimate);
    });
  }

  renderCards();
  updateEstimate();
  document.addEventListener("travelorio:langchange", function () { renderCards(); updateEstimate(); });
})();
