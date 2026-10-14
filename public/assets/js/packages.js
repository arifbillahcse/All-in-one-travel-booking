/* =========================================================
   TravelOrio — packages page
   1. Prefills the form from ?plan=<name>&place=<slug>
   2. Live estimate (plan price x travelers)
   All text follows the active language (re-renders on change).
   The page is rendered by Laravel. Load order: bn.js, core.js, THIS, main.js
   ========================================================= */
(function () {
  "use strict";

  if (!window.TO) return;
  var TO = window.TO, t = TO.t;

  var $ = function (sel, root) { return (root || document).querySelector(sel); };
  var esc = function (s) { return String(s).replace(/[&<>"']/g, function (c) { return ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" })[c]; }); };

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
  if (destSelect && placeSlug) {
    var match = Array.prototype.filter.call(destSelect.options, function (o) { return o.getAttribute("data-slug") === placeSlug; })[0];
    if (match) destSelect.value = match.value;
  }
  if (guestsInput && params.get("guests")) {
    var n = parseInt(params.get("guests"), 10);
    if (n >= 1 && n <= 50) guestsInput.value = n;
  }

  /* ---------- 3. Live estimate ---------- */
  var totalEl = $("#estimate-total");
  var noteEl = $("#estimate-note");
  var hidden = $("#b-estimate");
  var noteDefault = noteEl ? noteEl.textContent : "";      // server-rendered text in the page language

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

  updateEstimate();
})();
