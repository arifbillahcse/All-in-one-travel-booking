/* =========================================================
   TravelOrio — Why Us page
   Renders three traveler stories from js/data.js (Bangla overlay
   included) and re-renders when the language changes.
   Load order: data.js, bn.js, data-bn.js, core.js, THIS, main.js
   ========================================================= */
(function () {
  "use strict";

  var box = document.querySelector("#why-reviews");
  if (!box || !window.TO) return;
  var TO = window.TO, t = TO.t;

  var esc = function (s) { return String(s).replace(/[&<>"']/g, function (c) { return ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" })[c]; }); };
  var stars = function (n) { return "★".repeat(n) + "☆".repeat(5 - n); };
  var initials = function (name) { return name.split(/\s+/).map(function (w) { return Array.from(w)[0]; }).slice(0, 2).join("").toUpperCase(); };
  var monthLabel = function (ym) { var p = ym.split("-").map(Number); return new Date(p[0], p[1] - 1, 1).toLocaleDateString(TO.locale(), { month: "short", year: "numeric" }); };
  var placeName = function (slug) { var p = TO.placeByName(slug); return p ? p.name : ""; };

  function render() {
    // first three stories with five stars, spread across different destinations
    var seen = {}, picks = [];
    TO.reviews().forEach(function (r) {
      if (picks.length < 3 && r.rating === 5 && !seen[r.slug]) { seen[r.slug] = 1; picks.push(r); }
    });
    box.innerHTML = picks.map(function (r) {
      return '<figure class="review review--card" data-reveal>' +
        '<div class="review__top"><div class="review__stars" role="img" aria-label="' + esc(t("{n} out of 5 stars", { n: TO.num(r.rating) })) + '">' + stars(r.rating) + "</div>" +
        '<span class="review__verified">' + esc(t("Verified traveler")) + "</span></div>" +
        '<h3 class="review__title">' + esc(r.title) + "</h3>" +
        '<blockquote class="review__text">' + esc(r.text) + "</blockquote>" +
        '<figcaption class="review__author"><span class="avatar" aria-hidden="true">' + esc(initials(r.name)) + "</span>" +
        "<span><strong>" + esc(r.name) + "</strong><small>" + esc(r.city) + " · " + esc(placeName(r.slug)) + " · " + esc(monthLabel(r.date)) + "</small></span></figcaption></figure>";
    }).join("");
    // main.js has already set up its reveal observer, so show these directly
    Array.prototype.forEach.call(box.querySelectorAll("[data-reveal]"), function (el) { el.classList.add("is-visible"); });
  }

  render();
  document.addEventListener("travelorio:langchange", render);
})();
