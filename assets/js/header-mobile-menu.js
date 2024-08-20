document
  .getElementById("mobile-menu-button")
  .addEventListener("click", function () {
    const mobileMenu = document.getElementById("mobile-menu");
    mobileMenu.classList.toggle("hidden");

    document.body.classList.toggle(
      "overflow-hidden",
      !mobileMenu.classList.contains("hidden")
    );

    const icon = this.querySelector("path");
    icon.setAttribute(
      "d",
      mobileMenu.classList.contains("hidden")
        ? "M4 6h16M4 12h16M4 18h16"
        : "M6 18L18 6M6 6l12 12"
    );
  });

document.querySelectorAll(".toggle-submenu").forEach((button) => {
  button.addEventListener("click", function () {
    const submenu = this.parentElement.nextElementSibling;
    submenu.classList.toggle("hidden");

    const icon = this.querySelector("path");
    icon.setAttribute(
      "d",
      submenu.classList.contains("hidden") ? "M19 9l-7-7-7 7" : "M19 9l-7 7-7-7"
    );
  });
});

document.querySelectorAll("#mobile-menu a").forEach((item) => {
  item.addEventListener("click", () => {
    const mobileMenu = document.getElementById("mobile-menu");
    mobileMenu.classList.add("hidden");
    document.body.classList.remove("overflow-hidden");

    const icon = document
      .getElementById("mobile-menu-button")
      .querySelector("path");
    icon.setAttribute("d", "M4 6h16M4 12h16M4 18h16");
  });
});

window.addEventListener("resize", () => {
  const mobileMenu = document.getElementById("mobile-menu");
  if (window.innerWidth >= 768) {
    mobileMenu.classList.add("hidden");
    document.body.classList.remove("overflow-hidden");

    const icon = document
      .getElementById("mobile-menu-button")
      .querySelector("path");
    icon.setAttribute("d", "M4 6h16M4 12h16M4 18h16");
  }
});
