(function () {
  const body = document.body;

  // creează butonul automat
  const btn = document.createElement("button");
  btn.id = "themeToggle";
  btn.textContent = "Light Mode";
  document.addEventListener("DOMContentLoaded", () => {
    document.body.appendChild(btn);

    // aplică tema salvată
    if (localStorage.getItem("theme") === "light") {
      body.classList.add("light-mode");
      btn.textContent = "Dark Mode";
    }

    // toggle
    btn.addEventListener("click", () => {
      body.classList.toggle("light-mode");

      if (body.classList.contains("light-mode")) {
        localStorage.setItem("theme", "light");
        btn.textContent = "Dark Mode";
      } else {
        localStorage.setItem("theme", "dark");
        btn.textContent = "Light Mode";
      }
    });
  });
})();
