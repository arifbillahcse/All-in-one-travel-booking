/* =========================================================
   TravelOrio — reviews page
   Summary, featured quote, filters, sorting, "show more" and
   the review form. Everything follows the active language.
   Load order: data.js, bn.js, data-bn.js, core.js, THIS, main.js
   ========================================================= */
(function () {
  "use strict";

  if (!window.TO) return;
  var TO = window.TO, t = TO.t;

  var $ = function (sel, root) { return (root || document).querySelector(sel); };
  var $$ = function (sel, root) { return Array.prototype.slice.call((root || document).querySelectorAll(sel)); };
  var esc = function (s) { return String(s).replace(/[&<>"']/g, function (c) { return ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" })[c]; }); };

  var PAGE_SIZE = 6;
  var active = "all";
  var shown = PAGE_SIZE;

  var stars = function (n) { return "★".repeat(n) + "☆".repeat(5 - n); };
  var initials = function (name) {
    // Latin names -> first letters of two words; Bangla -> first character of up to two words
    return name.split(/\s+/).map(function (w) { return Array.from(w)[0]; }).slice(0, 2).join("").toUpperCase();
  };
  var monthLabel = function (ym) {
    var p = ym.split("-").map(Number);
    return new Date(p[0], p[1] - 1, 1).toLocaleDateString(TO.locale(), { month: "short", year: "numeric" });
  };
  var placeName = function (slug) { var p = TO.placeByName(slug); return p ? p.name : ""; };

  var grid = $("#reviews-grid");
  var filters = $("#review-filters");
  var sortSel = $("#review-sort");
  var moreBtn = $("#reviews-more");
  var status = $("#review-status");

  /* ---------- Summary + featured quote ---------- */
  function renderSummary() {
    var list = TO.reviews();
    if (!list.length) return;
    var avg = list.reduce(function (a, r) { return a + r.rating; }, 0) / list.length;
    $("#rating-avg").textContent = TO.num(avg, { minimumFractionDigits: 1, maximumFractionDigits: 1 });
    $("#rating-stars").textContent = stars(Math.round(avg));
    $("#rating-count").textContent = t(list.length === 1 ? "Based on {n} traveler review" : "Based on {n} traveler reviews", { n: TO.num(list.length) });

    $("#rating-bars").innerHTML = [5, 4, 3, 2, 1].map(function (n) {
      var count = list.filter(function (r) { return r.rating === n; }).length;
      var pct = Math.round((count / list.length) * 100);
      return '<li><span class="rating-bars__label">' + TO.num(n) + ' ★</span>' +
        '<span class="rating-bars__track"><span class="rating-bars__fill" style="width:' + pct + '%"></span></span>' +
        '<span class="rating-bars__pct">' + TO.num(pct) + "%</span></li>";
    }).join("");

    var f = list.filter(function (r) { return r.featured; })[0] || list[0];
    $("#featured-text").textContent = f.text;
    $("#featured-author").innerHTML =
      "<strong>" + esc(f.name) + "</strong> · " + esc(f.city) + "<br><span>" + esc(placeName(f.slug)) + " · " + esc(t(f.type)) + "</span>";
  }

  /* ---------- Filters + list ---------- */
  function renderFilters() {
    var list = TO.reviews();
    var used = (window.TRAVELORIO_DESTINATIONS || []).filter(function (p) { return list.some(function (r) { return r.slug === p.slug; }); });
    filters.innerHTML = ['<button type="button" class="chip" data-filter="all" aria-pressed="' + (active === "all") + '">' + esc(t("All ({n})", { n: TO.num(list.length) })) + "</button>"]
      .concat(used.map(function (p) {
        var n = list.filter(function (r) { return r.slug === p.slug; }).length;
        return '<button type="button" class="chip" data-filter="' + esc(p.slug) + '" aria-pressed="' + (active === p.slug) + '">' + esc(placeName(p.slug)) + " (" + TO.num(n) + ")</button>";
      })).join("");
  }

  function card(r) {
    return '<figure class="review review--card review-enter">' +
      '<div class="review__top"><div class="review__stars" role="img" aria-label="' + esc(t("{n} out of 5 stars", { n: TO.num(r.rating) })) + '">' + stars(r.rating) + "</div>" +
      '<span class="review__verified">' + esc(t("Verified traveler")) + "</span></div>" +
      '<h3 class="review__title">' + esc(r.title) + "</h3>" +
      '<blockquote class="review__text">' + esc(r.text) + "</blockquote>" +
      '<figcaption class="review__author"><span class="avatar" aria-hidden="true">' + esc(initials(r.name)) + "</span>" +
      "<span><strong>" + esc(r.name) + "</strong><small>" + esc(r.city) + " · " + esc(placeName(r.slug)) + " · " + esc(t(r.type)) + " · " + esc(monthLabel(r.date)) + "</small></span></figcaption></figure>";
  }

  function renderList() {
    var list = TO.reviews().filter(function (r) { return active === "all" || r.slug === active; });
    list.sort(function (a, b) {
      return sortSel.value === "highest"
        ? b.rating - a.rating || b.date.localeCompare(a.date)
        : b.date.localeCompare(a.date) || b.rating - a.rating;
    });
    var visible = list.slice(0, shown);
    grid.innerHTML = visible.map(card).join("") ||
      '<p class="reviews-empty">' + esc(t("No reviews for this destination yet. Be the first to write one!")) + "</p>";
    status.textContent = list.length
      ? t(list.length === 1 ? "Showing {a} of {b} review" : "Showing {a} of {b} reviews", { a: TO.num(visible.length), b: TO.num(list.length) })
      : "";
    moreBtn.hidden = visible.length >= list.length;
  }

  filters.addEventListener("click", function (e) {
    var btn = e.target.closest(".chip");
    if (!btn) return;
    active = btn.dataset.filter;
    shown = PAGE_SIZE;
    renderFilters();
    renderList();
  });
  sortSel.addEventListener("change", function () { shown = PAGE_SIZE; renderList(); });
  moreBtn.addEventListener("click", function () { shown += PAGE_SIZE; renderList(); });

  /* ---------- Write a review ---------- */
  var form = $("#review-form");
  var WA = (((($(".whatsapp-float") || {}).href || "").match(/wa\.me\/(\d+)/)) || [])[1] || "";

  var rules = {
    name: function (v) { return v.trim().length < 2 ? t("Please enter your name.") : ""; },
    destination: function (v) { return v ? "" : t("Please choose a destination."); },
    rating: function (v) { return v ? "" : t("Please choose a star rating."); },
    text: function (v) { return v.trim().length < 20 ? t("Please write at least 20 characters.") : ""; }
  };
  var fieldOf = function (el) { return el.closest(".form-field"); };
  function setError(field, msg) {
    field.classList.toggle("has-error", !!msg);
    var out = $(".form-error", field);
    if (out) out.textContent = msg;
  }
  function valueOf(name) {
    if (name === "rating") { var c = $('input[name="rating"]:checked', form); return c ? c.value : ""; }
    return form.elements[name].value;
  }
  function check(name) {
    var msg = rules[name](valueOf(name));
    setError(name === "rating" ? $("#rating-field") : fieldOf(form.elements[name]), msg);
    return !msg;
  }

  Object.keys(rules).forEach(function (name) {
    var els = name === "rating" ? $$('input[name="rating"]', form) : [form.elements[name]];
    els.forEach(function (el) {
      ["input", "change"].forEach(function (evt) {
        el.addEventListener(evt, function () { if (fieldOf(el).classList.contains("has-error")) check(name); });
      });
    });
  });

  form.addEventListener("submit", function (e) {
    e.preventDefault();
    var success = $("#review-success");
    success.hidden = true;

    var results = Object.keys(rules).map(function (n) { return [n, check(n)]; });
    var bad = results.filter(function (r) { return !r[1]; })[0];
    if (bad) {
      (bad[0] === "rating" ? $('input[name="rating"]', form) : form.elements[bad[0]]).focus();
      return;
    }

    var msg = [
      t("New review for TravelOrio (please moderate):"),
      "",
      t("Name: {v}", { v: form.elements.name.value.trim() }),
      t("Destination: {v}", { v: t(form.elements.destination.value) }),
      t("Rating: {v} / 5", { v: TO.num(valueOf("rating")) }),
      "",
      form.elements.text.value.trim()
    ].join("\n");

    var url = "https://wa.me/" + WA + "?text=" + encodeURIComponent(msg);
    var win = window.open(url, "_blank", "noopener");
    success.hidden = false;
    success.innerHTML = win
      ? esc(t("Thank you! Please complete sending in WhatsApp and we'll publish your review soon."))
      : esc(t("Thank you! Please complete sending in WhatsApp and we'll publish your review soon.")) + ' <a href="' + url + '" target="_blank" rel="noopener"><strong>' + esc(t("Tap here to send your review on WhatsApp.")) + "</strong></a>";
  });

  /* ---------- go ---------- */
  function renderAll() { renderSummary(); renderFilters(); renderList(); }
  renderAll();
  document.addEventListener("travelorio:langchange", function () {
    renderAll();
    $$(".has-error", form).forEach(function (f) { setError(f, ""); });
    $("#review-success").hidden = true;
  });
})();
