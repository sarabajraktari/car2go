document
  .getElementById("mobile-menu-button")
  .addEventListener("click", function () {
    const mobileMenu = document.getElementById("mobile-menu");
    const icon = this.querySelector("path");

    mobileMenu.classList.toggle("hidden");

    if (mobileMenu.classList.contains("hidden")) {
      icon.setAttribute("d", "M4 6h16M4 12h16M4 18h16");
    } else {
      icon.setAttribute("d", "M6 18L18 6M6 6l12 12");
    }
  });

function handleResize() {
  const mobileMenu = document.getElementById("mobile-menu");
  const icon = document
    .getElementById("mobile-menu-button")
    .querySelector("path");

  if (window.innerWidth >= 768) {
    mobileMenu.classList.add("hidden");
    icon.setAttribute("d", "M4 6h16M4 12h16M4 18h16");
  }
}

window.addEventListener("resize", handleResize);
