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
    if (mobileMenu.classList.contains("hidden")) {
      icon.setAttribute("d", "M4 6h16M4 12h16M4 18h16");
    } else {
      icon.setAttribute("d", "M6 18L18 6M6 6l12 12");
    }
  });

const menuItems = document.querySelectorAll("#mobile-menu a");
menuItems.forEach((item) => {
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
