document.addEventListener("DOMContentLoaded", function() {
  var modal = document.getElementById("verifyage");
  var okBtn = document.getElementById("refresh-page");
  var exitBtn = document.getElementById("reset-session");


  // Check if the user has already clicked OK
  if (!localStorage.getItem('modalSeen')) {
      // Show the modal after 0.3 seconds with a fade-in effect
      setTimeout(function() {
          modal.classList.remove("hidden");
          setTimeout(function() {
              modal.classList.add("modal-visible");
          }, 10); // Adding a slight delay to trigger the transition
      }, 300);
  }

  // Function to hide the modal and store the user's choice
  function hideModal() {
      modal.classList.remove("modal-visible");
      modal.classList.add("modal-hidden");
      setTimeout(function() {
          modal.style.display = "none";
      }, 200); // Wait for the fade-out transition to complete
      localStorage.setItem('modalSeen', 'true');
      document.querySelector('.box').classList.remove('hidden');
  }

  // Redirect to Google when the user clicks the exit button
  exitBtn.onclick = function() {
      window.location.href = "https://www.google.com";
      //window.history.back();
      sessionStorage.setItem('modalSeen', '');
  };

  // Close the modal and show the main content when the user clicks the OK button
  okBtn.onclick = function() {
      hideModal();
  };
});
