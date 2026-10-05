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

  /* Post a form to Laravel (JSON), then open WhatsApp with the message the server built.
     The tab is opened inside the click so popup blockers allow it, then pointed at WhatsApp. */
  function send(form) {
    var win = window.open("", "_blank");
    if (win) win.opener = null;
    var token = (document.querySelector('meta[name="csrf-token"]') || {}).content || "";
    var fail = function (message, errors) {
      if (win) win.close();
      return { ok: false, message: message, errors: errors || null };
    };
    return fetch(form.action, {
      method: "POST",
      credentials: "same-origin",
      headers: { Accept: "application/json", "X-CSRF-TOKEN": token, "X-Requested-With": "XMLHttpRequest" },
      body: new FormData(form)
    }).then(function (res) {
      return res.json().catch(function () { return {}; }).then(function (body) {
        if (res.ok && body.whatsapp_url) {
          if (win) win.location.href = body.whatsapp_url;
          return { ok: true, url: body.whatsapp_url, opened: !!win };
        }
        if (res.status === 422) return fail("", body.errors || {});
        if (res.status === 419) return fail(t("Your session expired. Please reload the page and try again."));
        if (res.status === 429) return fail(t("Please wait a minute and try again, or message us on WhatsApp."));
        return fail(t("We could not send your request. Please try again or message us on WhatsApp."));
      });
    }, function () {
      return fail(t("We could not send your request. Please try again or message us on WhatsApp."));
    });
  }

  function init() {
    document.dispatchEvent(new CustomEvent("travelorio:langready", { detail: { lang: lang } }));
  }
  if (document.readyState === "loading") document.addEventListener("DOMContentLoaded", init);
  else init();

  window.TO = {
    get lang() { return lang; },
    t: t, num: num, money: money, digits: digits, fmtDate: fmtDate, locale: locale, send: send
  };
})();
