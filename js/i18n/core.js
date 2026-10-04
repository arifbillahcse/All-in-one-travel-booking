/* =========================================================
   TravelOrio — language engine (English default, Bangla)
   ---------------------------------------------------------
   - English is the source text in the HTML. Bangla lives in
     js/i18n/bn.js (key = the English text) and js/i18n/data-bn.js.
   - Static text is swapped by a DOM walker, so no per-element
     markup is needed. Elements that mix text and tags (<em>…)
     can opt in with data-i18n-html; skip with data-no-i18n.
   - Dynamic scripts call TO.t / TO.money / TO.place and re-render
     when the "travelorio:langchange" event fires.
   - Choice is saved in localStorage and can be forced with ?lang=bn
   Load AFTER bn.js / data-bn.js and BEFORE page scripts + main.js.
   ========================================================= */
(function () {
  "use strict";

  var STORE = "travelorio-lang";
  var SUPPORTED = ["en", "bn"];
  var dicts = (window.TRAVELORIO_I18N = window.TRAVELORIO_I18N || {});
  var dataBn = window.TRAVELORIO_DATA_BN || {};
  var BN_DIGITS = "০১২৩৪৫৬৭৮৯";

  /* ---------- current language ---------- */
  function detect() {
    var l = null;
    try {
      var q = new URLSearchParams(window.location.search).get("lang");
      if (q && SUPPORTED.indexOf(q) > -1) { localStorage.setItem(STORE, q); return q; }
      l = localStorage.getItem(STORE);
    } catch (e) { /* storage blocked: stay on English */ }
    return SUPPORTED.indexOf(l) > -1 ? l : "en";
  }
  var lang = detect();

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

  /* ---------- DOM walker ---------- */
  var ATTRS = ["placeholder", "aria-label", "title", "alt"];
  var origText = new WeakMap();      // text node  -> English
  var appliedText = new WeakMap();   // text node  -> last value we wrote
  var origHtml = new WeakMap();      // element    -> English innerHTML
  var appliedHtml = new WeakMap();
  var origAttr = new WeakMap();      // element    -> { attr: English }
  var appliedAttr = new WeakMap();   // element    -> { attr: last value we wrote }
  var SKIP_TAGS = { SCRIPT: 1, STYLE: 1, NOSCRIPT: 1, TEXTAREA: 1 };

  function skipped(el, forAttrs) {
    for (var n = el; n; n = n.parentElement) {
      if ((!forAttrs && SKIP_TAGS[n.tagName]) || n.hasAttribute("data-no-i18n") || (!forAttrs && n.hasAttribute("data-count"))) return true;
      if (forAttrs && (n.tagName === "SCRIPT" || n.tagName === "STYLE")) return true;
    }
    return false;
  }

  function lookup(key) {
    if (lang === "en") return key;
    var d = dicts[lang];
    return d && d[key] !== undefined ? d[key] : key;
  }

  function doText(node) {
    var raw = node.nodeValue, cur = norm(raw);
    if (!cur) return;
    var orig = origText.get(node);
    if (orig === undefined || (cur !== orig && cur !== appliedText.get(node))) {
      orig = cur;                       // first sight, or changed by another script
      origText.set(node, orig);
    }
    var target = lookup(orig);
    if (target !== cur) {
      var lead = raw.match(/^\s*/)[0], trail = raw.match(/\s*$/)[0];
      node.nodeValue = lead + target + trail;
    }
    appliedText.set(node, target);
  }

  function doHtml(el) {
    var cur = norm(el.innerHTML);
    var orig = origHtml.get(el);
    if (orig === undefined || (cur !== orig && cur !== appliedHtml.get(el))) {
      orig = cur; origHtml.set(el, orig);
    }
    var target = lookup(orig);
    if (target !== cur) el.innerHTML = target;
    appliedHtml.set(el, norm(el.innerHTML));
  }

  function doAttrs(el) {
    var o = origAttr.get(el) || {}, a = appliedAttr.get(el) || {};
    ATTRS.forEach(function (name) {
      if (!el.hasAttribute(name)) return;
      var cur = norm(el.getAttribute(name));
      if (!cur) return;
      if (o[name] === undefined || (cur !== o[name] && cur !== a[name])) o[name] = cur;
      var target = lookup(o[name]);
      if (target !== cur) el.setAttribute(name, target);
      a[name] = target;
    });
    origAttr.set(el, o); appliedAttr.set(el, a);
  }

  function doMeta() {
    var sel = 'meta[name="description"], meta[property="og:title"], meta[property="og:description"]';
    Array.prototype.forEach.call(document.querySelectorAll(sel), function (m) {
      var cur = norm(m.getAttribute("content") || "");
      var o = origAttr.get(m) || {}, a = appliedAttr.get(m) || {};
      if (o.content === undefined || (cur !== o.content && cur !== a.content)) o.content = cur;
      var target = lookup(o.content);
      if (target !== cur) m.setAttribute("content", target);
      a.content = target;
      origAttr.set(m, o); appliedAttr.set(m, a);
    });
  }

  function apply(root) {
    root = root || document.documentElement;
    var htmlEls = root.querySelectorAll("[data-i18n-html]");
    Array.prototype.forEach.call(htmlEls, function (el) { if (!skipped(el)) doHtml(el); });

    var walker = document.createTreeWalker(root, NodeFilter.SHOW_TEXT, {
      acceptNode: function (n) {
        var p = n.parentElement;
        if (!p || !n.nodeValue.trim()) return NodeFilter.FILTER_REJECT;
        if (skipped(p) || p.closest("[data-i18n-html]")) return NodeFilter.FILTER_REJECT;
        return NodeFilter.FILTER_ACCEPT;
      }
    });
    var nodes = [], n;
    while ((n = walker.nextNode())) nodes.push(n);
    nodes.forEach(doText);

    Array.prototype.forEach.call(root.querySelectorAll("[placeholder],[aria-label],[title],[alt]"), function (el) {
      if (!skipped(el, true)) doAttrs(el);
    });
    doMeta();
  }

  /* ---------- switching ---------- */
  function syncSwitch() {
    document.documentElement.lang = lang;
    Array.prototype.forEach.call(document.querySelectorAll(".lang-switch [data-lang]"), function (b) {
      b.setAttribute("aria-pressed", String(b.getAttribute("data-lang") === lang));
    });
  }

  function setLang(next, persist) {
    if (SUPPORTED.indexOf(next) < 0) return;
    lang = next;
    if (persist !== false) { try { localStorage.setItem(STORE, lang); } catch (e) {} }
    syncSwitch();
    // 1) dynamic renderers rebuild their own content, 2) then the walker handles static text
    document.dispatchEvent(new CustomEvent("travelorio:langchange", { detail: { lang: lang } }));
    apply();
  }

  function init() {
    syncSwitch();
    Array.prototype.forEach.call(document.querySelectorAll(".lang-switch [data-lang]"), function (b) {
      b.addEventListener("click", function () { setLang(b.getAttribute("data-lang")); });
    });
    if (lang !== "en") apply();
    document.dispatchEvent(new CustomEvent("travelorio:langready", { detail: { lang: lang } }));
  }

  if (document.readyState === "loading") document.addEventListener("DOMContentLoaded", init);
  else init();

  /* Dev helper: ?i18n-debug lists English strings that have no Bangla yet */
  if (/[?&]i18n-debug/.test(window.location.search)) {
    document.addEventListener("travelorio:langready", function () {
      var missing = {};
      var w = document.createTreeWalker(document.body, NodeFilter.SHOW_TEXT);
      var n;
      while ((n = w.nextNode())) {
        var s = norm(n.nodeValue), p = n.parentElement;
        if (s && /[A-Za-z]/.test(s) && !skipped(p) && lang === "bn" && !/[ঀ-৿]/.test(s) && !SKIP_TAGS[p.tagName]) missing[s] = 1;
      }
      console.warn("Untranslated strings:", Object.keys(missing));
    });
  }

  window.TO = {
    get lang() { return lang; },
    t: t, num: num, money: money, digits: digits, fmtDate: fmtDate,
    place: place, places: places, placeByName: placeByName, reviews: reviews,
    setLang: setLang, apply: apply, locale: locale
  };
})();
