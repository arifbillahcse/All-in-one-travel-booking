/* =========================================================
   TravelOrio — article page behaviour
   The article is rendered by Laravel. This adds the reading-progress
   bar and the "copy link" button.
   Load order: bn.js, core.js, THIS, main.js
   ========================================================= */
(function () {
  "use strict";

  var $ = function (s) { return document.querySelector(s); };
  var t = window.TO ? window.TO.t : function (s) { return s; };

  var bar = $("#read-progress");
  function progress() {
    var h = document.documentElement.scrollHeight - innerHeight;
    if (bar) bar.style.transform = "scaleX(" + (h > 0 ? Math.min(scrollY / h, 1) : 0) + ")";
  }
  window.addEventListener("scroll", progress, { passive: true });
  progress();

  var copy = $("#copy-link");
  if (copy) copy.addEventListener("click", function () {
    var url = location.href.split("#")[0];
    var done = function () {
      copy.textContent = t("Link copied");
      setTimeout(function () { copy.textContent = t("Copy link"); }, 1800);
    };
    if (navigator.clipboard && navigator.clipboard.writeText) navigator.clipboard.writeText(url).then(done, done);
    else done();
  });
})();
