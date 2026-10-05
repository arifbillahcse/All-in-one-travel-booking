/* =========================================================
   TravelOrio — client-side translation helpers
   ---------------------------------------------------------
   The page language is chosen by the server (/bn/... is Bangla)
   and written to <html lang>. Static text is already translated
   by Blade (lang/bn.json), so this file only serves the sections
   that run in the browser (form messages, estimate, lightbox). Those scripts call
   TO.t / TO.num / TO.money with the page language.
   Load AFTER bn.js and BEFORE page scripts + main.js.
   ========================================================= */
(function () {
  "use strict";

  var SUPPORTED = ["en", "bn"];
  var dicts = (window.TRAVELORIO_I18N = window.TRAVELORIO_I18N || {});
  var BN_DIGITS = "০১২৩৪৫৬৭৮৯";

  /* ---------- current language (set by the server) ---------- */
  var lang = (document.documentElement.lang || "en").slice(0, 2);
  if (SUPPORTED.indexOf(lang) < 0) lang = "en";

  /* ---------- helpers ---------- */
  var norm = function (s) { return s.replace(/\s+/g, " ").trim(); };
  var locale = function () { return lang === "bn" ? "bn-BD" : "en-US"; };

  function t(key, vars) {
    var s = key;
    if (lang !== "en" && dicts[lang] && dicts[lang][key] !== undefined) s = dicts[lang][key];
    if (vars) s = s.replace(/\{(\w+)\}/g, function (m, k) { return k in vars ? vars[k] : m; });
    return s;
  }
  function num(n, opts) { return Number(n).toLocaleString(locale(), opts); }
  function money(n) { return "৳" + num(n); }
  function digits(str) {
    return lang === "bn" ? String(str).replace(/\d/g, function (d) { return BN_DIGITS[d]; }) : String(str);
  }
  function fmtDate(iso, opts) {
    var p = iso.split("-").map(Number);
    return new Date(p[0], p[1] - 1, p[2] || 1).toLocaleDateString(locale(), opts || { day: "numeric", month: "long", year: "numeric" });
  }

  function init() {
    document.dispatchEvent(new CustomEvent("travelorio:langready", { detail: { lang: lang } }));
  }
  if (document.readyState === "loading") document.addEventListener("DOMContentLoaded", init);
  else init();

  window.TO = {
    get lang() { return lang; },
    t: t, num: num, money: money, digits: digits, fmtDate: fmtDate, locale: locale
  };
})();
