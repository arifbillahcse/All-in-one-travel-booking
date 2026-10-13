/* TravelOrio – premium motion layer.
   Purely decorative: scroll progress, card tilt + light sheen, magnetic buttons, button ripple,
   heading underline draw and image reveal. Nothing here is needed to use the site, and
   everything is skipped for people who ask for reduced motion. */
(() => {
  "use strict";
  const root = document.documentElement;
  if (window.matchMedia("(prefers-reduced-motion: reduce)").matches) return;
  const fine = window.matchMedia("(hover: hover) and (pointer: fine)").matches;
  root.classList.add("motion");

  /* ---- 1. Scroll progress bar ---- */
  const bar = document.createElement("div");
  bar.className = "scroll-progress";
  bar.setAttribute("aria-hidden", "true");
  document.body.prepend(bar);
  let ticking = false;
  const paint = () => {
    const max = root.scrollHeight - window.innerHeight;
    bar.style.transform = `scaleX(${max > 0 ? Math.min(1, window.scrollY / max) : 0})`;
    ticking = false;
  };
  window.addEventListener("scroll", () => { if (!ticking) { ticking = true; requestAnimationFrame(paint); } }, { passive: true });
  paint();

  /* ---- 2. Headings draw their underline, images un-blur when they enter view ---- */
  const seen = new IntersectionObserver((entries) => {
    entries.forEach((e) => { if (e.isIntersecting) { e.target.classList.add("in-view"); seen.unobserve(e.target); } });
  }, { threshold: 0.35 });
  document.querySelectorAll(".section__title, .card__media, .article__cover, .dest-gallery a").forEach((el) => seen.observe(el));

  /* ---- 3. Card tilt + moving light (mouse only) ---- */
  if (fine) {
    document.querySelectorAll(".card, .package, .review, .blog-card").forEach((card) => {
      let frame = 0;
      card.addEventListener("pointermove", (e) => {
        if (e.pointerType !== "mouse") return;
        cancelAnimationFrame(frame);
        frame = requestAnimationFrame(() => {
          const r = card.getBoundingClientRect();
          const x = (e.clientX - r.left) / r.width;
          const y = (e.clientY - r.top) / r.height;
          card.style.setProperty("--mx", `${(x * 100).toFixed(1)}%`);
          card.style.setProperty("--my", `${(y * 100).toFixed(1)}%`);
          card.style.transform = `perspective(900px) rotateX(${((0.5 - y) * 5).toFixed(2)}deg) rotateY(${((x - 0.5) * 6).toFixed(2)}deg) translateY(-6px)`;
        });
      });
      card.addEventListener("pointerleave", () => { cancelAnimationFrame(frame); card.style.transform = ""; });
    });

    /* ---- 4. Magnetic primary buttons ---- */
    document.querySelectorAll(".btn--primary, .btn--lg").forEach((btn) => {
      btn.addEventListener("pointermove", (e) => {
        if (e.pointerType !== "mouse") return;
        const r = btn.getBoundingClientRect();
        const dx = (e.clientX - (r.left + r.width / 2)) * 0.18;
        const dy = (e.clientY - (r.top + r.height / 2)) * 0.28;
        btn.style.transform = `translate(${dx.toFixed(1)}px, ${(dy - 2).toFixed(1)}px)`;
      });
      btn.addEventListener("pointerleave", () => { btn.style.transform = ""; });
    });
  }

  /* ---- 5. Ripple on buttons ---- */
  document.addEventListener("pointerdown", (e) => {
    const btn = e.target.closest && e.target.closest(".btn");
    if (!btn || btn.disabled) return;
    const r = btn.getBoundingClientRect();
    const size = Math.max(r.width, r.height) * 2;
    const dot = document.createElement("span");
    dot.className = "ripple";
    dot.style.cssText = `width:${size}px;height:${size}px;left:${e.clientX - r.left - size / 2}px;top:${e.clientY - r.top - size / 2}px`;
    btn.appendChild(dot);
    dot.addEventListener("animationend", () => dot.remove());
  });
})();
