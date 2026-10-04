/* =========================================================
   TravelOrio — contact page
   Validates the message form and hands it to WhatsApp.
   ========================================================= */
(function () {
  "use strict";

  const $ = (sel, root = document) => root.querySelector(sel);
  const form = $("#contact-form");
  if (!form) return;

  const WA = ((($(".whatsapp-float") || {}).href || "").match(/wa\.me\/(\d+)/) || [])[1] || "";

  const rules = {
    name: (v) => (v.trim().length < 2 ? "Please enter your name." : ""),
    phone: (v) => {
      const d = v.replace(/[\s\-()]/g, "");
      if (!d) return "Please enter your phone number.";
      return /^\+?\d{10,15}$/.test(d) ? "" : "Enter a valid number, e.g. +8801XXXXXXXXX.";
    },
    email: (v) => (!v.trim() || /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(v.trim()) ? "" : "Enter a valid email address."),
    message: (v) => (v.trim().length < 10 ? "Please write at least 10 characters." : ""),
  };

  const check = (input) => {
    const rule = rules[input.name];
    if (!rule) return true;
    const field = input.closest(".form-field");
    const msg = rule(input.value);
    field.classList.toggle("has-error", !!msg);
    $(".form-error", field).textContent = msg;
    if (msg) input.setAttribute("aria-invalid", "true"); else input.removeAttribute("aria-invalid");
    return !msg;
  };

  Array.from(form.elements).forEach((el) => {
    if (!rules[el.name]) return;
    el.addEventListener("blur", () => { if (el.value || el.closest(".has-error")) check(el); });
    el.addEventListener("input", () => { if (el.closest(".has-error")) check(el); });
  });

  form.addEventListener("submit", (e) => {
    e.preventDefault();
    const success = $("#contact-success");
    success.hidden = true;

    const inputs = Array.from(form.elements).filter((el) => rules[el.name]);
    const bad = inputs.filter((el) => !check(el));
    if (bad.length) { bad[0].focus(); return; }

    const d = Object.fromEntries(new FormData(form).entries());
    const lines = [
      "Hello TravelOrio! I have a question.",
      "",
      `Name: ${d.name.trim()}`,
      `Phone: ${d.phone.trim()}`,
    ];
    if (d.email.trim()) lines.push(`Email: ${d.email.trim()}`);
    lines.push(`Topic: ${d.topic}`, "", d.message.trim());

    const url = `https://wa.me/${WA}?text=${encodeURIComponent(lines.join("\n"))}`;
    const win = window.open(url, "_blank", "noopener");
    success.hidden = false;
    success.innerHTML = win
      ? "Thank you! Please complete sending in WhatsApp and we'll reply shortly."
      : `Thank you! <a href="${url}" target="_blank" rel="noopener"><strong>Tap here to send it on WhatsApp.</strong></a>`;
  });
})();
