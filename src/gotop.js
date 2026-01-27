(function () {
  function initGoTop() {
    if (document.getElementById("goTopBtn")) return;

    const btn = document.createElement("button");
    btn.id = "goTopBtn";
    // Refolosim stilul butoanelor existente (ex: "Vezi detalii")
    btn.className = "btn blue goTopBtn";
    btn.type = "button";
    btn.setAttribute("aria-label", "Mergi sus");
    btn.textContent = "↑ Sus";

    // Stiluri inline (fallback), ca să arate bine chiar dacă CSS-ul e cache-uit
    btn.style.position = "fixed";
    btn.style.right = "18px";
    btn.style.bottom = "18px";
    btn.style.zIndex = "99999";
    btn.style.borderRadius = "10px";
    btn.style.boxShadow = "0 10px 24px rgba(0,0,0,.35)";

    document.body.appendChild(btn);

    // arătăm butonul mereu (dar îl facem mai discret când ești sus)
    function updateBtn() {
      btn.style.display = "block";
      if (window.scrollY > 120) {
        btn.style.opacity = "1";
      } else {
        btn.style.opacity = "0.5";
      }
    }

    window.addEventListener("scroll", updateBtn);
    updateBtn(); // rulează și la load

    btn.addEventListener("click", () => {
      window.scrollTo({ top: 0, behavior: "smooth" });
    });
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initGoTop);
  } else {
    initGoTop();
  }
})();
