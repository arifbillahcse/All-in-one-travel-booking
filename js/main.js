/* =========================================================
   TravelOrio — main.js
   1. Helpers
   2. Navbar (scroll state + mobile menu)
   3. Scroll reveal
   4. Hero parallax
   5. Stat counters
   6. Prefill booking form (cards, packages, hero search)
   7. Booking form validation + WhatsApp hand-off
   8. Footer year
   ========================================================= */
(function () {
  "use strict";

  /* ---------- 1. Helpers ---------- */
  const $ = (sel, root = document) => root.querySelector(sel);
  const $$ = (sel, root = document) => Array.from(root.querySelectorAll(sel));

  const prefersReducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
  const canHover = window.matchMedia("(hover: hover) and (pointer: fine)").matches;

  const toISODate = (d) => {
    const pad = (n) => String(n).padStart(2, "0");
    return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`;
  };
  const todayISO = toISODate(new Date());

  // WhatsApp number comes from the floating button, so it's defined in one place (the HTML).
  const waLink = $(".whatsapp-float");
  const WA_NUMBER = waLink ? (waLink.getAttribute("href").match(/wa\.me\/(\d+)/) || [])[1] : "";


  /* ---------- 2. Navbar ---------- */
  const navbar = $("#navbar");
  const navToggle = $("#nav-toggle");
  const navMenu = $("#nav-menu");

  function onScrollNavbar() {
    navbar.classList.toggle("is-scrolled", window.scrollY > 40);
  }
  onScrollNavbar();
  window.addEventListener("scroll", onScrollNavbar, { passive: true });

  function setMenu(open) {
    navbar.classList.toggle("is-open", open);
    navToggle.setAttribute("aria-expanded", String(open));
    navToggle.setAttribute("aria-label", open ? "Close menu" : "Open menu");
    document.body.style.overflow = open ? "hidden" : "";
  }

  navToggle.addEventListener("click", () => setMenu(!navbar.classList.contains("is-open")));
  $$("a", navMenu).forEach((a) => a.addEventListener("click", () => setMenu(false)));
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape" && navbar.classList.contains("is-open")) {
      setMenu(false);
      navToggle.focus();
    }
  });
  // Reset if the viewport grows past the mobile breakpoint while the menu is open
  window.addEventListener("resize", () => {
    if (window.innerWidth > 860 && navbar.classList.contains("is-open")) setMenu(false);
  });


  /* ---------- 3. Scroll reveal ---------- */
  const revealEls = $$("[data-reveal]");
  if ("IntersectionObserver" in window && !prefersReducedMotion) {
    const revealObserver = new IntersectionObserver(
      (entries, obs) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add("is-visible");
            obs.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.12, rootMargin: "0px 0px -6% 0px" }
    );
    revealEls.forEach((el) => revealObserver.observe(el));
  } else {
    revealEls.forEach((el) => el.classList.add("is-visible"));
  }


  /* ---------- 4. Hero parallax ---------- */
  const hero = $("#home");
  const layers = $$(".hero__layer[data-depth]", hero).map((el) => ({
    el,
    depth: parseFloat(el.dataset.depth) || 0,
  }));

  if (layers.length && !prefersReducedMotion) {
    let pointerX = 0;   // -1 .. 1
    let pointerY = 0;
    let scrollY = 0;
    let heroVisible = true;
    let ticking = false;

    const MOUSE_RANGE = 220;   // px of travel at depth = 1
    const SCROLL_RANGE = 90;   // px of travel at depth = 1 per full hero scroll

    function render() {
      ticking = false;
      const progress = Math.min(scrollY / hero.offsetHeight, 1);
      layers.forEach(({ el, depth }) => {
        const x = -pointerX * depth * MOUSE_RANGE;
        const y = -pointerY * depth * MOUSE_RANGE * 0.6 + progress * depth * SCROLL_RANGE * 4;
        el.style.transform = `translate3d(${x.toFixed(1)}px, ${y.toFixed(1)}px, 0)`;
      });
    }
    function request() {
      if (!ticking && heroVisible) {
        ticking = true;
        requestAnimationFrame(render);
      }
    }

    new IntersectionObserver(
      ([entry]) => {
        heroVisible = entry.isIntersecting;
        if (heroVisible) request();
      },
      { threshold: 0 }
    ).observe(hero);

    window.addEventListener("scroll", () => { scrollY = window.scrollY; request(); }, { passive: true });

    if (canHover) {
      hero.addEventListener("pointermove", (e) => {
        const r = hero.getBoundingClientRect();
        pointerX = (e.clientX - r.left) / r.width * 2 - 1;
        pointerY = (e.clientY - r.top) / r.height * 2 - 1;
        request();
      });
      hero.addEventListener("pointerleave", () => { pointerX = 0; pointerY = 0; request(); });
    } else if (window.DeviceOrientationEvent) {
      // Gentle tilt on phones (only fires where permission is not required)
      window.addEventListener("deviceorientation", (e) => {
        if (e.gamma == null || e.beta == null) return;
        pointerX = Math.max(-1, Math.min(1, e.gamma / 30));
        pointerY = Math.max(-1, Math.min(1, (e.beta - 45) / 30));
        request();
      }, { passive: true });
    }

    request();
  }


  /* ---------- 5. Stat counters ---------- */
  const counters = $$("[data-count]");

  function animateCount(el) {
    const target = parseFloat(el.dataset.count);
    const decimals = parseInt(el.dataset.decimals || "0", 10);
    const duration = 1600;
    const start = performance.now();
    const fmt = (n) => n.toLocaleString("en-US", {
      minimumFractionDigits: decimals,
      maximumFractionDigits: decimals,
    });

    function frame(now) {
      const t = Math.min((now - start) / duration, 1);
      const eased = 1 - Math.pow(1 - t, 3);          // easeOutCubic
      el.textContent = fmt(target * eased);
      if (t < 1) requestAnimationFrame(frame);
      else el.textContent = fmt(target);
    }
    requestAnimationFrame(frame);
  }

  if ("IntersectionObserver" in window && !prefersReducedMotion) {
    const countObserver = new IntersectionObserver(
      (entries, obs) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            animateCount(entry.target);
            obs.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.6 }
    );
    counters.forEach((el) => { el.textContent = "0"; countObserver.observe(el); });
  }
  // Otherwise the final numbers already in the HTML stay as they are.


  /* ---------- 6. Prefill booking form (home page) ---------- */
  const form = $("#booking-form");
  const fDestination = $("#b-destination");
  const fPackage = $("#b-package");
  const fDate = $("#b-date");
  const fGuests = $("#b-guests");

  // No past dates on any date field
  $$('input[type="date"]').forEach((input) => input.setAttribute("min", todayISO));

  function setSelect(select, value) {
    if (!select) return;
    const match = Array.from(select.options).find((o) => o.value === value || o.textContent === value);
    if (match) select.value = match.value || match.textContent;
  }

  // Destination links and package buttons fill the booking form
  $$("[data-destination]").forEach((el) =>
    el.addEventListener("click", () => {
      setSelect(fDestination, el.dataset.destination);
      if (fDestination) clearError(fDestination.closest(".form-field"));
    })
  );
  $$("[data-package]").forEach((el) =>
    el.addEventListener("click", () => setSelect(fPackage, el.dataset.package))
  );

  // Hero search fills the booking form and jumps to it
  const searchForm = $("#hero-search");
  if (searchForm && form) {
    searchForm.addEventListener("submit", (e) => {
      e.preventDefault();
      const dest = $("#search-destination").value;
      const date = $("#search-date").value;
      const guests = $("#search-guests").value;

      if (dest) setSelect(fDestination, dest);
      if (date) fDate.value = date;
      if (guests) fGuests.value = guests === "5+" ? 5 : guests;

      $("#booking").scrollIntoView({ behavior: prefersReducedMotion ? "auto" : "smooth" });
      setTimeout(() => (dest ? $("#b-name") : fDestination).focus({ preventScroll: true }), 700);
    });
  }


  /* ---------- 7. WhatsApp booking forms: validation + hand-off ----------
     Works for any form with these field names:
     name, phone, destination, date, guests (+ optional package, message).
     Used by #booking-form (home) and #trip-form (destination page). */
  const rules = {
    name(v) {
      if (!v.trim()) return "Please enter your name.";
      if (v.trim().length < 2) return "Name looks too short.";
      return "";
    },
    phone(v) {
      const digits = v.replace(/[\s\-()]/g, "");
      if (!digits) return "Please enter your phone number.";
      if (!/^\+?\d{10,15}$/.test(digits)) return "Enter a valid number, e.g. +8801XXXXXXXXX.";
      return "";
    },
    destination(v) { return v ? "" : "Please choose a destination."; },
    date(v) {
      if (!v) return "Please pick a travel date.";
      if (v < todayISO) return "Travel date can't be in the past.";
      return "";
    },
    guests(v) {
      const n = Number(v);
      if (!v || !Number.isInteger(n)) return "Enter the number of travelers.";
      if (n < 1 || n > 50) return "Choose between 1 and 50 travelers.";
      return "";
    },
  };

  function showError(field, message) {
    if (!field) return;
    field.classList.add("has-error");
    const err = $(".form-error", field);
    if (err) err.textContent = message;
    const input = $("input, select, textarea", field);
    if (input) input.setAttribute("aria-invalid", "true");
  }
  function clearError(field) {
    if (!field) return;
    field.classList.remove("has-error");
    const err = $(".form-error", field);
    if (err) err.textContent = "";
    const input = $("input, select, textarea", field);
    if (input) input.removeAttribute("aria-invalid");
  }
  function validateField(input) {
    const rule = rules[input.name];
    if (!rule) return true;
    const field = input.closest(".form-field");
    const message = rule(input.value);
    if (message) { showError(field, message); return false; }
    clearError(field);
    return true;
  }

  function initWhatsAppForm(formEl) {
    $$("input, select, textarea", formEl).forEach((input) => {
      input.addEventListener("blur", () => { if (input.value || input.closest(".has-error")) validateField(input); });
      input.addEventListener("input", () => { if (input.closest(".has-error")) validateField(input); });
      input.addEventListener("change", () => { if (input.closest(".has-error")) validateField(input); });
    });

    formEl.addEventListener("submit", (e) => {
      e.preventDefault();
      const success = $(".form-success", formEl);
      if (success) success.hidden = true;

      const inputs = $$("input, select, textarea", formEl).filter((i) => rules[i.name]);
      const results = inputs.map((i) => ({ input: i, ok: validateField(i) }));
      const firstBad = results.find((r) => !r.ok);
      if (firstBad) {
        if (firstBad.input.type !== "hidden") firstBad.input.focus();
        return;
      }

      const data = Object.fromEntries(new FormData(formEl).entries());
      const lines = [
        "Hello TravelOrio! I'd like to book a trip.",
        "",
        `Name: ${data.name.trim()}`,
        `Phone: ${data.phone.trim()}`,
        `Destination: ${data.destination}`,
        `Package: ${data.package || "Not sure yet"}`,
        `Travel date: ${data.date}`,
        `Travelers: ${data.guests}`,
      ];
      if (data.message && data.message.trim()) lines.push("", `Message: ${data.message.trim()}`);

      const url = `https://wa.me/${WA_NUMBER}?text=${encodeURIComponent(lines.join("\n"))}`;
      const win = window.open(url, "_blank", "noopener");
      if (success) {
        success.hidden = false;
        success.innerHTML = win
          ? "Thank you! Your request is ready. Complete it in WhatsApp and we'll reply shortly."
          : `Thank you! Your request is ready. <a href="${url}" target="_blank" rel="noopener"><strong>Tap here to send it on WhatsApp.</strong></a>`;
        success.scrollIntoView({ behavior: prefersReducedMotion ? "auto" : "smooth", block: "nearest" });
      }
    });
  }

  $$("#booking-form, #trip-form").forEach(initWhatsAppForm);


  /* ---------- 8. Footer year ---------- */
  const year = $("#year");
  if (year) year.textContent = new Date().getFullYear();
})();
