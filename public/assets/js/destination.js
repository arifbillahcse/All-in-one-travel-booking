/* =========================================================
   TravelOrio — destination page behaviour
   The page itself is rendered by Laravel (resources/views/pages/destination.blade.php).
   This script only adds the photo lightbox for the gallery.
   Load order: bn.js, core.js, THIS, main.js
   ========================================================= */
(function () {
  "use strict";

  if (!window.TO) return;
  var TO = window.TO;
  var t = TO.t;

  var $ = function (sel, root) { return (root || document).querySelector(sel); };
  var $$ = function (sel, root) { return Array.prototype.slice.call((root || document).querySelectorAll(sel)); };

  /* ---------- Gallery lightbox (built once, items read on click) ---------- */
  function setupLightbox() {
    if (typeof HTMLDialogElement === "undefined") return;
    var box = document.createElement("dialog");
    box.className = "lightbox";
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

  setupLightbox();
})();
