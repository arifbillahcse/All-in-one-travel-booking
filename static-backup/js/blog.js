/* =========================================================
   TravelOrio — blog list page
   Category chips, search, featured article, "show more".
   Load order: blog-core.js, bn.js, blog-bn.js, core.js, THIS, main.js
   ========================================================= */
(function () {
  "use strict";

  var grid = document.querySelector("#blog-grid");
  if (!grid || !window.TO || !window.TravelBlog) return;
  var TO = window.TO, t = TO.t, B = window.TravelBlog;

  var $ = function (s) { return document.querySelector(s); };
  var esc = B.esc;
  var chips = $("#blog-filters"), search = $("#blog-search"), status = $("#blog-status"), more = $("#blog-more"), featured = $("#blog-featured");
  var PAGE = 6, active = "all", shown = PAGE;

  function renderChips() {
    var all = B.list();
    var cats = B.categories();
    chips.innerHTML = ['<button type="button" class="chip" data-cat="all" aria-pressed="' + (active === "all") + '">' + esc(t("All")) + " (" + TO.num(all.length) + ")</button>"]
      .concat(cats.map(function (c) {
        var n = all.filter(function (p) { return p.category === c; }).length;
        return '<button type="button" class="chip" data-cat="' + esc(c) + '" aria-pressed="' + (active === c) + '">' + esc(t(c)) + " (" + TO.num(n) + ")</button>";
      })).join("");
  }

  function matches(p, q) {
    if (!q) return true;
    var hay = (p.title + " " + p.excerpt + " " + t(p.category)).toLowerCase();
    return hay.indexOf(q) > -1;
  }

  function render() {
    var q = search.value.trim().toLowerCase();
    var list = B.list().filter(function (p) { return (active === "all" || p.category === active) && matches(p, q); });
    var showFeatured = active === "all" && !q && list.length > 0;

    featured.innerHTML = showFeatured ? B.card(list[0], { featured: true }) : "";
    featured.hidden = !showFeatured;
    var rest = showFeatured ? list.slice(1) : list;

    var visible = rest.slice(0, shown);
    grid.innerHTML = visible.map(function (p) { return B.card(p); }).join("") ||
      (list.length ? "" : '<p class="reviews-empty">' + esc(t("No articles match your search.")) + "</p>");
    status.textContent = list.length ? t(list.length === 1 ? "{n} article" : "{n} articles", { n: TO.num(list.length) }) : "";
    more.hidden = visible.length >= rest.length;
  }

  chips.addEventListener("click", function (e) {
    var b = e.target.closest(".chip");
    if (!b) return;
    active = b.dataset.cat; shown = PAGE;
    renderChips(); render();
  });
  var timer;
  search.addEventListener("input", function () { clearTimeout(timer); timer = setTimeout(function () { shown = PAGE; render(); }, 120); });
  more.addEventListener("click", function () { shown += PAGE; render(); });

  renderChips(); render();
  document.addEventListener("travelorio:langchange", function () { renderChips(); render(); });
})();
