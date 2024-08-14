document
  .getElementById("mobile-menu-button")
  .addEventListener("click", function () {
    const mobileMenu = document.getElementById("mobile-menu");
    const icon = this.querySelector("path");

    mobileMenu.classList.toggle("hidden");

    // Toggle between burger and close icon
    if (mobileMenu.classList.contains("hidden")) {
      // Change to burger icon
      icon.setAttribute("d", "M4 6h16M4 12h16M4 18h16"); // Burger icon path
    } else {
      // Change to close (X) icon
      icon.setAttribute("d", "M6 18L18 6M6 6l12 12"); // Close icon path
    }
  });
