/* =========================================================
   TravelOrio — blog article page
   /blog/<slug> -> article, contents list, share
   buttons, author box, previous/next, related and trip card.
   Load order: blog-core.js, data.js, bn.js, data-bn.js, blog-bn.js, core.js, THIS, main.js
   ========================================================= */
(function () {
  "use strict";

  var host = document.querySelector("#article");
  if (!host || !window.TO || !window.TravelBlog) return;
  var TO = window.TO, t = TO.t, B = window.TravelBlog, esc = B.esc;
  var $ = function (s) { return document.querySelector(s); };

  var slug = (window.TRAVELORIO_PAGE || {}).slug;
  var all = window.TRAVELORIO_POSTS;
  var base = all.filter(function (p) { return p.slug === slug; })[0] || all.slice().sort(function (a, b) { return b.date.localeCompare(a.date); })[0];

  function blocks(p) {
    var n = 0;
    return p.body.map(function (b) {
      if (b.type === "h2") { n += 1; return '<h2 id="sec-' + n + '">' + esc(b.text) + "</h2>"; }
      if (b.type === "ul") return '<ul class="tick-list">' + b.items.map(function (i) { return "<li>" + esc(i) + "</li>"; }).join("") + "</ul>";
      if (b.type === "tip") return '<aside class="callout"><strong>' + esc(t("Tip")) + "</strong><p>" + esc(b.text) + "</p></aside>";
      return "<p>" + esc(b.text) + "</p>";
    }).join("");
  }

  function render() {
    var p = B.get(base.slug);
    var url = location.href.split("#")[0];

    document.title = p.title + " | TravelOrio";
    var meta = function (sel, v) { var e = $(sel); if (e) e.setAttribute("content", v); };
    meta('meta[name="description"]', p.excerpt);
    meta('meta[property="og:title"]', p.title);
    meta('meta[property="og:description"]', p.excerpt);

    $("#post-cat").textContent = t(p.category);
    $("#post-title").textContent = p.title;
    $("#post-meta").textContent = TO.fmtDate(p.date) + " · " + t("{n} min read", { n: TO.num(p.readMins) });
    $("#post-crumb").textContent = p.title;

    var heads = p.body.filter(function (b) { return b.type === "h2"; });
    var toc = heads.length > 1
      ? '<nav class="toc" aria-label="' + esc(t("In this article")) + '"><p class="toc__title">' + esc(t("In this article")) + "</p><ol>" +
        heads.map(function (h, i) { return '<li><a href="#sec-' + (i + 1) + '">' + esc(h.text) + "</a></li>"; }).join("") + "</ol></nav>"
      : "";

    var dest = p.destination ? TO.placeByName(p.destination) : null;
    var trip = dest
      ? '<aside class="trip-card"><div><p class="eyebrow">' + esc(t("Plan this trip")) + "</p><h3>" + esc(dest.name) + "</h3><p>" + esc(dest.tagline) + "</p></div>" +
        '<div class="trip-card__actions"><a class="btn btn--primary" href="' + esc(((window.TRAVELORIO_ROUTES || {}).destination || "/destinations") + "/" + dest.slug) + '">' + esc(t("View trip details")) + '</a><a class="btn btn--outline" href="' + esc(((window.TRAVELORIO_ROUTES || {}).packages || "/packages") + "?place=" + dest.slug) + '">' + esc(t("See packages")) + "</a></div></aside>"
      : "";

    var text = encodeURIComponent(p.title + " " + url);
    var share = '<div class="share"><span class="share__label">' + esc(t("Share this article")) + "</span>" +
      '<a class="share__btn" target="_blank" rel="noopener" href="https://wa.me/?text=' + text + '">WhatsApp</a>' +
      '<a class="share__btn" target="_blank" rel="noopener" href="https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(url) + '">Facebook</a>' +
      '<button type="button" class="share__btn" id="copy-link">' + esc(t("Copy link")) + "</button></div>";

    host.innerHTML =
      '<figure class="article__cover"><img src="' + esc(B.img(p.slug, 1200, 700)) + '" alt="' + esc(p.title) + '" width="1200" height="700"></figure>' +
      toc + '<div class="article__body">' + blocks(p) + "</div>" + trip + share +
      '<aside class="author-box"><span class="author-box__avatar" aria-hidden="true">T</span><div><p class="author-box__name">' + esc(t("TravelOrio Team")) + "</p><p>" +
      esc(t("Local guides and trip planners who write from first-hand experience.")) + "</p></div></aside>";

    var copy = $("#copy-link");
    if (copy) copy.addEventListener("click", function () {
      var done = function () { copy.textContent = t("Link copied"); setTimeout(function () { copy.textContent = t("Copy link"); }, 1800); };
      if (navigator.clipboard && navigator.clipboard.writeText) navigator.clipboard.writeText(url).then(done, done); else done();
    });

    // previous / next + related
    var sorted = B.list();
    var i = sorted.map(function (x) { return x.slug; }).indexOf(p.slug);
    var prev = sorted[i + 1], next = sorted[i - 1];
    $("#post-nav").innerHTML =
      (prev ? '<a class="post-nav__link" href="' + B.href(prev) + '"><span>' + esc(t("Previous article")) + "</span><strong>" + esc(prev.title) + "</strong></a>" : "<span></span>") +
      (next ? '<a class="post-nav__link post-nav__link--next" href="' + B.href(next) + '"><span>' + esc(t("Next article")) + "</span><strong>" + esc(next.title) + "</strong></a>" : "<span></span>");

    var rel = sorted.filter(function (x) { return x.slug !== p.slug; });
    rel.sort(function (a, b) { return (b.category === p.category) - (a.category === p.category); });
    $("#related-posts").innerHTML = rel.slice(0, 3).map(function (x) { return B.card(x); }).join("");
  }

  // thin reading-progress bar
  var bar = $("#read-progress");
  function progress() {
    var h = document.documentElement.scrollHeight - innerHeight;
    if (bar) bar.style.transform = "scaleX(" + (h > 0 ? Math.min(scrollY / h, 1) : 0) + ")";
  }
  window.addEventListener("scroll", progress, { passive: true });

  render(); progress();
  document.addEventListener("travelorio:langchange", render);
})();
