/* =========================================================
   TravelOrio — client-side translation helpers
   ---------------------------------------------------------
   The page language is chosen by the server (/bn/... is Bangla)
   and written to <html lang>. Static text is already translated
   by Blade (lang/bn.json), so this file only serves the sections
   that scripts still build in the browser. Those scripts call
   TO.t / TO.num / TO.money / TO.place with the page language.
   It disappears once Phase 5 renders those sections on the server.
   Load AFTER bn.js / data-bn.js and BEFORE page scripts + main.js.
   ========================================================= */
(function () {
  "use strict";

  var SUPPORTED = ["en", "bn"];
  var dicts = (window.TRAVELORIO_I18N = window.TRAVELORIO_I18N || {});
  var dataBn = window.TRAVELORIO_DATA_BN || {};
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

  /* Merge Bangla content over the English object (arrays of objects merge by index) */
  function overlay(obj, bn) {
    if (!bn) return obj;
    var out = Object.assign({}, obj);
    Object.keys(bn).forEach(function (k) {
      var v = bn[k];
      if (Array.isArray(v) && v.length && typeof v[0] === "object" && Array.isArray(obj[k])) {
        out[k] = v.map(function (o, i) { return Object.assign({}, obj[k][i], o); });
      } else { out[k] = v; }
    });
    return out;
  }
  var bnData = function () { return (dataBn.bn || {}); };

  function place(p) {
    if (lang === "en" || !p) return p;
    return overlay(p, (bnData().places || {})[p.slug]);
  }
  function places() { return (window.TRAVELORIO_DESTINATIONS || []).map(place); }
  function placeByName(slug) { var p = (window.TRAVELORIO_DESTINATIONS || []).filter(function (x) { return x.slug === slug; })[0]; return p ? place(p) : null; }
  function reviews() {
    var list = window.TRAVELORIO_REVIEWS || [];
    return list.map(function (r, i) {
      if (lang === "en") return r;
      var o = overlay(r, (bnData().reviews || [])[i]);
      return o;
    });
  }

    function init() {
    document.dispatchEvent(new CustomEvent("travelorio:langready", { detail: { lang: lang } }));
  }
  if (document.readyState === "loading") document.addEventListener("DOMContentLoaded", init);
  else init();

  window.TO = {
    get lang() { return lang; },
    t: t, num: num, money: money, digits: digits, fmtDate: fmtDate,
    place: place, places: places, placeByName: placeByName, reviews: reviews, localize: overlay,
    locale: locale
  };
})();
