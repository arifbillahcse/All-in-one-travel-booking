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

  // Language helpers (js/i18n/core.js defines window.TO; fall back to plain English if it is missing)
  const TO = window.TO || null;
  const T = (key, vars) => (TO ? TO.t(key, vars) : (vars ? key.replace(/\{(\w+)\}/g, (m, k) => (k in vars ? vars[k] : m)) : key));
  const NUM = (n, opts) => (TO ? TO.num(n, opts) : Number(n).toLocaleString("en-US", opts));

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
    navToggle.setAttribute("aria-label", open ? T("Close menu") : T("Open menu"));
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


  /* ---------- Selects that reload the page when changed (e.g. review sort) ---------- */
  $$("select[data-autosubmit]").forEach((sel) => sel.addEventListener("change", () => sel.form && sel.form.submit()));


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
  const layers = (hero ? $$(".hero__layer[data-depth]", hero) : []).map((el) => ({
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
    const fmt = (n) => NUM(n, { minimumFractionDigits: decimals, maximumFractionDigits: decimals });

    function frame(now) {
      const t = Math.min((now - start) / duration, 1);
      const eased = 1 - Math.pow(1 - t, 3);          // easeOutCubic
      el.textContent = fmt(target * eased);
      if (t < 1) requestAnimationFrame(frame);
      else { el.textContent = fmt(target); el.dataset.done = "1"; }
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

  // Re-format counters when the language changes (the language walker skips [data-count])
  function refreshCounters() {
    counters.forEach((el) => {
      const decimals = parseInt(el.dataset.decimals || "0", 10);
      const target = parseFloat(el.dataset.count);
      const finished = el.dataset.done === "1" || prefersReducedMotion || !("IntersectionObserver" in window);
      el.textContent = NUM(finished ? target : 0, { minimumFractionDigits: decimals, maximumFractionDigits: decimals });
    });
  }
  if (TO) {
    refreshCounters();
    document.addEventListener("travelorio:langchange", refreshCounters);
  }


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
    if (match) {
      select.value = match.value || match.textContent;
      select.dispatchEvent(new Event("change", { bubbles: true }));   // lets estimate/validation react
    }
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
      if (!v.trim()) return T("Please enter your name.");
      if (v.trim().length < 2) return T("Name looks too short.");
      return "";
    },
    phone(v) {
      const digits = v.replace(/[\s\-()]/g, "");
      if (!digits) return T("Please enter your phone number.");
      if (!/^\+?\d{10,15}$/.test(digits)) return T("Enter a valid number, e.g. +8801XXXXXXXXX.");
      return "";
    },
    destination(v) { return v ? "" : "Please choose a destination."; },
    date(v) {
      if (!v) return T("Please pick a travel date.");
      if (v < todayISO) return T("Travel date can't be in the past.");
      return "";
    },
    guests(v) {
      const n = Number(v);
      if (!v || !Number.isInteger(n)) return T("Enter the number of travelers.");
      if (n < 1 || n > 50) return T("Choose between 1 and 50 travelers.");
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
        T("Hello TravelOrio! I'd like to book a trip."),
        "",
        T("Name: {v}", { v: data.name.trim() }),
        T("Phone: {v}", { v: data.phone.trim() }),
        T("Destination: {v}", { v: T(data.destination) }),
        T("Package: {v}", { v: T(data.package || "Not sure yet") }),
        T("Travel date: {v}", { v: TO ? TO.fmtDate(data.date) : data.date }),
        T("Travelers: {v}", { v: NUM(data.guests) }),
      ];
      if (data.estimate) lines.push(T("Estimated total: {v}", { v: data.estimate }));
      if (data.message && data.message.trim()) lines.push("", T("Message: {v}", { v: data.message.trim() }));

      const url = `https://wa.me/${WA_NUMBER}?text=${encodeURIComponent(lines.join("\n"))}`;
      const win = window.open(url, "_blank", "noopener");
      if (success) {
        success.hidden = false;
        const ok = T("Thank you! Your request is ready. Complete it in WhatsApp and we'll reply shortly.");
        success.innerHTML = win
          ? ok
          : `${ok} <a href="${url}" target="_blank" rel="noopener"><strong>${T("Tap here to send it on WhatsApp.")}</strong></a>`;
        success.scrollIntoView({ behavior: prefersReducedMotion ? "auto" : "smooth", block: "nearest" });
      }
    });
  }

  $$("#booking-form, #trip-form").forEach(initWhatsAppForm);

  // A language switch clears stale error messages and success notes
  document.addEventListener("travelorio:langchange", () => {
    $$("#booking-form, #trip-form").forEach((f) => {
      $$(".has-error", f).forEach((field) => clearError(field));
      const ok = $(".form-success", f);
      if (ok) ok.hidden = true;
    });
  });


  /* ---------- 8. Footer year ---------- */
  const year = $("#year");
  const setYear = () => { if (year) year.textContent = NUM(new Date().getFullYear(), { useGrouping: false }); };
  setYear();
  if (TO) document.addEventListener("travelorio:langchange", setYear);

  /* ---------- 9. Dark mode toggle ---------- */
  const root = document.documentElement;
  const themeBtn = $("#theme-toggle");
  const isDark = () => root.getAttribute("data-theme") === "dark";

  function syncTheme() {
    const dark = isDark();
    const meta = $('meta[name="theme-color"]');
    if (meta) meta.setAttribute("content", dark ? "#0a1a2a" : "#0a6ea8");
    if (!themeBtn) return;
    themeBtn.setAttribute("aria-pressed", String(dark));
    themeBtn.setAttribute("aria-label", T(dark ? "Switch to light mode" : "Switch to dark mode"));
  }
  function setTheme(next, save) {
    if (next === "dark") root.setAttribute("data-theme", "dark"); else root.removeAttribute("data-theme");
    if (save) { try { localStorage.setItem("travelorio-theme", next); } catch (e) { /* storage blocked */ } }
    syncTheme();
  }
  if (themeBtn) themeBtn.addEventListener("click", () => setTheme(isDark() ? "light" : "dark", true));
  // Follow the device setting until the visitor makes their own choice
  if (window.matchMedia) {
    const mq = window.matchMedia("(prefers-color-scheme: dark)");
    const onSystem = (e) => { let saved = null; try { saved = localStorage.getItem("travelorio-theme"); } catch (err) {} if (!saved) setTheme(e.matches ? "dark" : "light", false); };
    if (mq.addEventListener) mq.addEventListener("change", onSystem);
  }
  syncTheme();
  document.addEventListener("travelorio:langchange", syncTheme);


  /* ---------- 10. Back to top ---------- */
  const toTop = document.createElement("button");
  toTop.type = "button";
  toTop.className = "to-top";
  toTop.setAttribute("data-no-i18n", "");
  toTop.innerHTML = '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 19V5M5 12l7-7 7 7"/></svg>';
  const labelTop = () => toTop.setAttribute("aria-label", T("Back to top"));
  labelTop();
  document.body.appendChild(toTop);
  const showTop = () => toTop.classList.toggle("is-visible", window.scrollY > 700);
  window.addEventListener("scroll", showTop, { passive: true });
  showTop();
  toTop.addEventListener("click", () => window.scrollTo({ top: 0, behavior: prefersReducedMotion ? "auto" : "smooth" }));
  document.addEventListener("travelorio:langchange", labelTop);

  /* ---------- 11. Destinations dropdown (touch + keyboard) ---------- */
  const menuItems = $$(".has-menu");
  const closeSubmenus = (except) => menuItems.forEach((li) => {
    if (li === except) return;
    li.classList.remove("is-open");
    const b = $(".submenu-toggle", li);
    if (b) b.setAttribute("aria-expanded", "false");
  });
  menuItems.forEach((li) => {
    const btn = $(".submenu-toggle", li);
    if (!btn) return;
    btn.addEventListener("click", (e) => {
      e.stopPropagation();
      const open = !li.classList.contains("is-open");
      closeSubmenus(li);
      li.classList.toggle("is-open", open);
      btn.setAttribute("aria-expanded", String(open));
    });
  });
  document.addEventListener("click", (e) => { if (!e.target.closest(".has-menu")) closeSubmenus(); });
  document.addEventListener("keydown", (e) => { if (e.key === "Escape") closeSubmenus(); });
  // closing the mobile menu also folds the dropdown
  if (navToggle) navToggle.addEventListener("click", () => closeSubmenus());

})();
