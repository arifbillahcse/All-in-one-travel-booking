/* =========================================================
   TravelOrio — reviews page
   Renders summary, featured quote, filters, sorting and
   "show more" from window.TRAVELORIO_REVIEWS (js/data.js),
   and sends a new review to WhatsApp for moderation.
   Must load AFTER data.js and BEFORE main.js.
   ========================================================= */
(function () {
  "use strict";

  const $ = (sel, root = document) => root.querySelector(sel);
  const $$ = (sel, root = document) => Array.from(root.querySelectorAll(sel));
  const esc = (s) => String(s).replace(/[&<>"']/g, (c) => ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" }[c]));

  const reviews = window.TRAVELORIO_REVIEWS || [];
  const places = window.TRAVELORIO_DESTINATIONS || [];
  const placeName = (slug) => (places.find((p) => p.slug === slug) || {}).name || "";
  const PAGE_SIZE = 6;

  const monthLabel = (ym) => {
    const [y, m] = ym.split("-").map(Number);
    return new Date(y, m - 1, 1).toLocaleDateString("en-GB", { month: "short", year: "numeric" });
  };
  const stars = (n) => "★".repeat(n) + "☆".repeat(5 - n);
  const initials = (name) => name.split(/\s+/).map((w) => w[0]).slice(0, 2).join("").toUpperCase();

  /* ---------- Summary ---------- */
  if (reviews.length) {
    const avg = reviews.reduce((a, r) => a + r.rating, 0) / reviews.length;
    $("#rating-avg").textContent = avg.toFixed(1);
    $("#rating-stars").textContent = stars(Math.round(avg));
    $("#rating-count").textContent = `Based on ${reviews.length} traveler review${reviews.length === 1 ? "" : "s"}`;

    $("#rating-bars").innerHTML = [5, 4, 3, 2, 1].map((n) => {
      const count = reviews.filter((r) => r.rating === n).length;
      const pct = Math.round((count / reviews.length) * 100);
      return `<li><span class="rating-bars__label">${n} ★</span>
        <span class="rating-bars__track"><span class="rating-bars__fill" style="width:${pct}%"></span></span>
        <span class="rating-bars__pct">${pct}%</span></li>`;
    }).join("");

    const f = reviews.find((r) => r.featured) || reviews[0];
    $("#featured-text").textContent = f.text;
    $("#featured-author").innerHTML =
      `<strong>${esc(f.name)}</strong> · ${esc(f.city)}<br><span>${esc(placeName(f.slug))} · ${esc(f.type)} trip</span>`;
  }

  /* ---------- Filters, sort, list ---------- */
  const grid = $("#reviews-grid");
  const filters = $("#review-filters");
  const sortSel = $("#review-sort");
  const moreBtn = $("#reviews-more");
  const status = $("#review-status");

  let active = "all";
  let shown = PAGE_SIZE;

  const used = places.filter((p) => reviews.some((r) => r.slug === p.slug));
  filters.innerHTML = [`<button type="button" class="chip" data-filter="all" aria-pressed="true">All (${reviews.length})</button>`]
    .concat(used.map((p) => {
      const n = reviews.filter((r) => r.slug === p.slug).length;
      return `<button type="button" class="chip" data-filter="${esc(p.slug)}" aria-pressed="false">${esc(p.name)} (${n})</button>`;
    })).join("");

  function card(r) {
    return `
    <figure class="review review--card review-enter">
      <div class="review__top">
        <div class="review__stars" role="img" aria-label="${r.rating} out of 5 stars">${stars(r.rating)}</div>
        <span class="review__verified">Verified traveler</span>
      </div>
      <h3 class="review__title">${esc(r.title)}</h3>
      <blockquote class="review__text">${esc(r.text)}</blockquote>
      <figcaption class="review__author">
        <span class="avatar" aria-hidden="true">${esc(initials(r.name))}</span>
        <span><strong>${esc(r.name)}</strong><small>${esc(r.city)} · ${esc(placeName(r.slug))} · ${esc(r.type)} · ${esc(monthLabel(r.date))}</small></span>
      </figcaption>
    </figure>`;
  }

  function render() {
    let list = reviews.filter((r) => active === "all" || r.slug === active);
    list = list.slice().sort((a, b) =>
      sortSel.value === "highest"
        ? b.rating - a.rating || b.date.localeCompare(a.date)
        : b.date.localeCompare(a.date) || b.rating - a.rating
    );

    const visible = list.slice(0, shown);
    grid.innerHTML = visible.map(card).join("") ||
      `<p class="reviews-empty">No reviews for this destination yet. Be the first to write one!</p>`;

    status.textContent = list.length
      ? `Showing ${visible.length} of ${list.length} review${list.length === 1 ? "" : "s"}`
      : "";
    moreBtn.hidden = visible.length >= list.length;
  }

  filters.addEventListener("click", (e) => {
    const btn = e.target.closest(".chip");
    if (!btn) return;
    active = btn.dataset.filter;
    shown = PAGE_SIZE;
    $$(".chip", filters).forEach((c) => c.setAttribute("aria-pressed", String(c === btn)));
    render();
  });
  sortSel.addEventListener("change", () => { shown = PAGE_SIZE; render(); });
  moreBtn.addEventListener("click", () => { shown += PAGE_SIZE; render(); });
  render();


  /* ---------- Write a review ---------- */
  const form = $("#review-form");
  const WA = ((($(".whatsapp-float") || {}).href || "").match(/wa\.me\/(\d+)/) || [])[1] || "";

  const rules = {
    name: (v) => (v.trim().length < 2 ? "Please enter your name." : ""),
    destination: (v) => (v ? "" : "Please choose a destination."),
    rating: (v) => (v ? "" : "Please choose a star rating."),
    text: (v) => (v.trim().length < 20 ? "Please write at least 20 characters." : ""),
  };
  const fieldOf = (el) => el.closest(".form-field");
  const setError = (field, msg) => {
    field.classList.toggle("has-error", !!msg);
    const out = $(".form-error", field);
    if (out) out.textContent = msg;
  };
  const valueOf = (name) => {
    if (name === "rating") { const c = $('input[name="rating"]:checked', form); return c ? c.value : ""; }
    return form.elements[name].value;
  };
  const check = (name) => {
    const msg = rules[name](valueOf(name));
    setError(name === "rating" ? $("#rating-field") : fieldOf(form.elements[name]), msg);
    return !msg;
  };

  Object.keys(rules).forEach((name) => {
    const els = name === "rating" ? $$('input[name="rating"]', form) : [form.elements[name]];
    els.forEach((el) => {
      ["input", "change"].forEach((evt) =>
        el.addEventListener(evt, () => { if (fieldOf(el).classList.contains("has-error")) check(name); })
      );
    });
  });

  form.addEventListener("submit", (e) => {
    e.preventDefault();
    const success = $("#review-success");
    success.hidden = true;

    const results = Object.keys(rules).map((n) => [n, check(n)]);
    const bad = results.find((r) => !r[1]);
    if (bad) {
      const target = bad[0] === "rating" ? $('input[name="rating"]', form) : form.elements[bad[0]];
      target.focus();
      return;
    }

    const msg = [
      "New review for TravelOrio (please moderate):",
      "",
      `Name: ${form.elements.name.value.trim()}`,
      `Destination: ${form.elements.destination.value}`,
      `Rating: ${valueOf("rating")} / 5`,
      "",
      form.elements.text.value.trim(),
    ].join("\n");

    const url = `https://wa.me/${WA}?text=${encodeURIComponent(msg)}`;
    const win = window.open(url, "_blank", "noopener");
    success.hidden = false;
    success.innerHTML = win
      ? "Thank you! Please complete sending in WhatsApp and we'll publish your review soon."
      : `Thank you! <a href="${url}" target="_blank" rel="noopener"><strong>Tap here to send your review on WhatsApp.</strong></a>`;
  });
})();
