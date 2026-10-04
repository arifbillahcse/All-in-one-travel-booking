/* =========================================================
   TravelOrio — packages page
   1. Renders the "by destination" cards from js/data.js
   2. Prefills the form from ?plan=<name>&place=<slug>
   3. Live estimate (plan price x travelers)
   Must load AFTER data.js and BEFORE main.js.
   ========================================================= */
(function () {
  "use strict";

  const $ = (sel, root = document) => root.querySelector(sel);
  const esc = (s) => String(s).replace(/[&<>"']/g, (c) => ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" }[c]));
  const money = (n) => "৳" + Number(n).toLocaleString("en-US");

  const places = window.TRAVELORIO_DESTINATIONS || [];

  /* ---------- 1. Destination cards ---------- */
  const cards = $("#destination-cards");
  if (cards && places.length) {
    cards.innerHTML = places.map((p) => `
      <article class="card destination" data-reveal>
        <a href="destination.html?place=${esc(p.slug)}" class="card__media" aria-label="View ${esc(p.name)} trip details">
          <img src="${esc(p.cardImage)}" alt="${esc(p.name)}" loading="lazy" width="800" height="600">
          <span class="card__tag">From ${money(p.price)}</span>
        </a>
        <div class="card__body">
          <p class="card__meta">${esc(p.region)} · ${esc(p.duration)}</p>
          <h3 class="card__title">${esc(p.name)}</h3>
          <p class="card__text">${esc(p.tagline)}</p>
          <a href="destination.html?place=${esc(p.slug)}" class="link-arrow">View trip details <span aria-hidden="true">→</span></a>
        </div>
      </article>`).join("");
  }

  /* ---------- 2. Prefill from the URL ---------- */
  const pkgSelect = $("#b-package");
  const destSelect = $("#b-destination");
  const guestsInput = $("#b-guests");
  const params = new URLSearchParams(window.location.search);

  const pick = (select, value) => {
    if (!select || !value) return;
    const opt = Array.from(select.options).find((o) => o.value === value || o.textContent.trim() === value);
    if (opt) select.value = opt.value || opt.textContent.trim();
  };

  pick(pkgSelect, params.get("plan"));
  const place = places.find((p) => p.slug === params.get("place"));
  if (place) pick(destSelect, place.name);
  if (guestsInput && params.get("guests")) {
    const n = parseInt(params.get("guests"), 10);
    if (n >= 1 && n <= 50) guestsInput.value = n;
  }

  /* ---------- 3. Live estimate ---------- */
  const totalEl = $("#estimate-total");
  const hidden = $("#b-estimate");

  function updateEstimate() {
    if (!pkgSelect || !totalEl) return;
    const price = parseInt(pkgSelect.selectedOptions[0].dataset.price || "", 10);
    const guests = parseInt(guestsInput.value, 10);
    let label = "";

    if (!price) {
      totalEl.textContent = "Choose a plan";
    } else if (!guests || guests < 1) {
      totalEl.textContent = money(price) + " per person";
    } else {
      const total = price * guests;
      label = `${money(total)} (${guests} × ${money(price)})`;
      totalEl.textContent = money(total);
      totalEl.dataset.detail = `${guests} traveler${guests > 1 ? "s" : ""} × ${money(price)}`;
    }
    if (hidden) hidden.value = label;
    const note = $("#estimate-note");
    if (note && price && guests > 0) {
      note.textContent = `${guests} traveler${guests > 1 ? "s" : ""} × ${money(price)}. A rough guide only, as your final quote may differ by destination and season.`;
    }
  }

  if (pkgSelect && guestsInput) {
    ["change", "input"].forEach((evt) => {
      pkgSelect.addEventListener(evt, updateEstimate);
      guestsInput.addEventListener(evt, updateEstimate);
    });
    updateEstimate();
  }
})();
