document.addEventListener('DOMContentLoaded', function () {
    const resetButton = document.getElementById('reset-button');
    if (resetButton) {
      resetButton.addEventListener('click', function () {
        window.location.href = 'http://internship.test/cars/?search=&brand=&city=';
      });
    }
  });
  