/* =========================================================
   TravelOrio — reviews page behaviour
   The reviews, filters and sorting are rendered by Laravel.
   This script validates the "write a review" form and hands it to WhatsApp.
   Load order: bn.js, core.js, THIS, main.js
   ========================================================= */
(function () {
  "use strict";

  if (!window.TO) return;
  var TO = window.TO, t = TO.t;

  var $ = function (sel, root) { return (root || document).querySelector(sel); };
  var $$ = function (sel, root) { return Array.prototype.slice.call((root || document).querySelectorAll(sel)); };
  var esc = function (s) { return String(s).replace(/[&<>"']/g, function (c) { return ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" })[c]; }); };

  /* ---------- Write a review ---------- */
  var form = $("#review-form");

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

    var button = $("button[type=submit]", form);
    if (button) button.disabled = true;
    TO.send(form).then(function (res) {
      if (button) button.disabled = false;
      if (!res.ok) {
        var first = null;
        Object.keys(res.errors || {}).forEach(function (name) {
          var input = name === "rating" ? $('input[name="rating"]', form) : form.elements[name];
          if (!input) return;
          setError(name === "rating" ? $("#rating-field") : fieldOf(input), res.errors[name][0]);
          first = first || input;
        });
        if (first) first.focus();
        else { success.hidden = false; success.textContent = res.message; }
        return;
      }
      success.hidden = false;
      var ok = esc(t("Thank you! Please complete sending in WhatsApp and we'll publish your review soon."));
      success.innerHTML = res.opened ? ok : ok + ' <a href="' + esc(res.url) + '" target="_blank" rel="noopener"><strong>' + esc(t("Tap here to send your review on WhatsApp.")) + "</strong></a>";
    });
  });
})();
