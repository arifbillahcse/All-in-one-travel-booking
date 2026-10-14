/* =========================================================
   TravelOrio — contact page
   Validates the message form and hands it to WhatsApp, in the
   active language. Load order: bn.js, core.js, THIS, main.js
   ========================================================= */
(function () {
  "use strict";

  var form = document.querySelector("#contact-form");
  if (!form || !window.TO) return;
  var TO = window.TO, t = TO.t;

  var $ = function (sel, root) { return (root || document).querySelector(sel); };
  var esc = function (s) { return String(s).replace(/[&<>"']/g, function (c) { return ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" })[c]; }); };
  var WA = (((($(".whatsapp-float") || {}).href || "").match(/wa\.me\/(\d+)/)) || [])[1] || "";

  var rules = {
    name: function (v) { return v.trim().length < 2 ? t("Please enter your name.") : ""; },
    phone: function (v) {
      var d = v.replace(/[\s\-()]/g, "");
      if (!d) return t("Please enter your phone number.");
      return /^\+?\d{10,15}$/.test(d) ? "" : t("Enter a valid number, e.g. +8801XXXXXXXXX.");
    },
    email: function (v) { return !v.trim() || /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(v.trim()) ? "" : t("Enter a valid email address."); },
    message: function (v) { return v.trim().length < 10 ? t("Please write at least 10 characters.") : ""; }
  };

  function check(input) {
    var rule = rules[input.name];
    if (!rule) return true;
    var field = input.closest(".form-field");
    var msg = rule(input.value);
    field.classList.toggle("has-error", !!msg);
    $(".form-error", field).textContent = msg;
    if (msg) input.setAttribute("aria-invalid", "true"); else input.removeAttribute("aria-invalid");
    return !msg;
  }

  Array.prototype.forEach.call(form.elements, function (el) {
    if (!rules[el.name]) return;
    el.addEventListener("blur", function () { if (el.value || el.closest(".has-error")) check(el); });
    el.addEventListener("input", function () { if (el.closest(".has-error")) check(el); });
  });

  form.addEventListener("submit", function (e) {
    e.preventDefault();
    var success = $("#contact-success");
    success.hidden = true;

    var inputs = Array.prototype.filter.call(form.elements, function (el) { return rules[el.name]; });
    var bad = inputs.filter(function (el) { return !check(el); });
    if (bad.length) { bad[0].focus(); return; }

    var d = Object.fromEntries(new FormData(form).entries());
    var lines = [t("Hello TravelOrio! I have a question."), "", t("Name: {v}", { v: d.name.trim() }), t("Phone: {v}", { v: d.phone.trim() })];
    if (d.email.trim()) lines.push(t("Email: {v}", { v: d.email.trim() }));
    lines.push(t("Topic: {v}", { v: t(d.topic) }), "", d.message.trim());

    var url = "https://wa.me/" + WA + "?text=" + encodeURIComponent(lines.join("\n"));
    var win = window.open(url, "_blank", "noopener");
    success.hidden = false;
    var ok = esc(t("Thank you! Please complete sending in WhatsApp and we'll reply shortly."));
    success.innerHTML = win ? ok : ok + ' <a href="' + url + '" target="_blank" rel="noopener"><strong>' + esc(t("Tap here to send it on WhatsApp.")) + "</strong></a>";
  });

  document.addEventListener("travelorio:langchange", function () {
    Array.prototype.forEach.call(form.querySelectorAll(".has-error"), function (f) {
      f.classList.remove("has-error");
      var o = $(".form-error", f); if (o) o.textContent = "";
    });
    $("#contact-success").hidden = true;
  });
})();
