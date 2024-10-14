document.addEventListener('DOMContentLoaded', function () {

  const startDateElement = document.getElementById('start-date');
  const endDateElement = document.getElementById('end-date');
  const addonCheckboxes = document.querySelectorAll('.addon-checkbox');
  const quantityButtons = document.querySelectorAll('.quantity-button');

  // Disable all add-ons and quantity buttons initially
  disableAddOns(true);

  if (startDateElement && endDateElement) {
      let today = new Date().toISOString().split('T')[0];
      
      startDateElement.setAttribute('min', today);
      endDateElement.setAttribute('min', today);

    
      startDateElement.addEventListener('change', validateDates);
      endDateElement.addEventListener('change', validateDates);
  }

  // Function to validate dates and enable/disable add-ons accordingly
  function validateDates() {
      let startDate = startDateElement.value;
      let endDate = endDateElement.value;

      if (startDate && endDate) {
          disableAddOns(false); // Enable add-ons if both dates are selected
          calculateTotal(); 
      } else {
          disableAddOns(true); // Disable add-ons if dates are missing
      }
  }

  // Function to enable/disable add-ons
  function disableAddOns(disable) {
      addonCheckboxes.forEach(function (checkbox) {
          checkbox.disabled = disable;
          if (disable) {
              // Add alert if user tries to select an add-on without picking dates
              checkbox.addEventListener('click', preventInteraction);
          } else {
              // Remove the event listener once add-ons are enabled
              checkbox.removeEventListener('click', preventInteraction);
          }
      });

      // Disable the quantity buttons and their containers (quantity spans)
      document.querySelectorAll('[id$="-quantity"]').forEach(function(quantityElement) {
          const parentContainer = quantityElement.closest('div');
          parentContainer.querySelectorAll('button').forEach(function(button) {
              button.disabled = disable;
              if (disable) {
                  button.addEventListener('click', preventInteraction);
              } else {
                  button.removeEventListener('click', preventInteraction);
              }
          });
      });
  }


  // Event listeners for add-ons checkboxes (Calculate total on change)
  addonCheckboxes.forEach(function (checkbox) {
      checkbox.addEventListener('change', calculateTotal);
  });

  // Event listeners for quantity change buttons
  document.querySelectorAll('[id$="-quantity"]').forEach(function(quantityElement) {
      const parentContainer = quantityElement.closest('div');
      parentContainer.querySelectorAll('button').forEach(function(button) {
          button.addEventListener('click', function() {
              calculateTotal();  
          });
      });
  });

  if (typeof carLatitude !== 'undefined' && typeof carLongitude !== 'undefined' && carLatitude !== 0 && carLongitude !== 0) {
    const map = L.map('map').setView([carLatitude, carLongitude], 13);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);
    
    L.marker([carLatitude, carLongitude]).addTo(map)
        .bindPopup(`<b>Car Location</b><br>${cartitle}`)
        .openPopup();
}

});

function calculateTotal() {
  const startDateElement = document.getElementById('start-date');
  const endDateElement = document.getElementById('end-date');
  
  let deliveryFee = 60;
  let protectionFee = 25;
  let convenienceFee = 2;
  let tax = 2;
  let refundablePercent = 5;

  let startDate = startDateElement.value;
  let endDate = endDateElement.value;

  if (!startDate || !endDate) {
      return;
  }

  let start = new Date(startDate);
  let end = new Date(endDate);
  let diffTime = Math.abs(end - start);
  let diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;

  let baseTotal = (carPrice * diffDays) + deliveryFee + protectionFee + convenienceFee + tax;
  let refundableDeposit = baseTotal * (refundablePercent / 100);


  const addOnsTotal = calculateAddOnTotal();

 
  document.getElementById('refundable-deposit').textContent = '+ $' + refundableDeposit.toFixed(2);
  document.getElementById('subtotal').textContent = '+ $' + (baseTotal + addOnsTotal).toFixed(2);
}

// Function to calculate add-ons total
function calculateAddOnTotal() {

  const gps = document.getElementById('gps').checked ? 60 : 0;
  const babySeat = document.getElementById('baby-seat').checked ? 30 : 0;
  const extraInsurance = document.getElementById('extra-insurance').checked ? 100 : 0;
  const wifi = document.getElementById('wifi').checked ? 50 : 0;
  const additionalDriver = document.getElementById('additional-driver').checked ? 20 : 0;


  const infantSeatQuantity = parseInt(document.getElementById('infant-seat-quantity').textContent) || 0;
  const toddlerSeatQuantity = parseInt(document.getElementById('toddler-seat-quantity').textContent) || 0;
  const snowChainsQuantity = parseInt(document.getElementById('snow-chains-quantity').textContent) || 0;
  const winterTiresQuantity = parseInt(document.getElementById('winter-tires-quantity').textContent) || 0;

 
  const infantSeatTotal = infantSeatQuantity * 50;
  const toddlerSeatTotal = toddlerSeatQuantity * 50;
  const snowChainsTotal = snowChainsQuantity * 100;
  const winterTiresTotal = winterTiresQuantity * 100;

  // Calculate the total add-ons cost
  const addOnsTotal = gps + babySeat + extraInsurance + wifi + additionalDriver +
                      infantSeatTotal + toddlerSeatTotal + snowChainsTotal + winterTiresTotal;

  return addOnsTotal;
}

function increaseQuantity(id) {
  let element = document.getElementById(id);
  let currentQuantity = parseInt(element.textContent) || 0;
  element.textContent = currentQuantity + 1;
}

function decreaseQuantity(id) {
  let element = document.getElementById(id);
  let currentQuantity = parseInt(element.textContent) || 0;
  if (currentQuantity > 0) {
      element.textContent = currentQuantity - 1;
  }
}

function viewLargerMap() {
  if (carLatitude !== 0 && carLongitude !== 0) {
    const mapUrl = `https://www.openstreetmap.org/?mlat=${carLatitude}&mlon=${carLongitude}&zoom=12&marker=${carLatitude},${carLongitude}`;
    window.open(mapUrl, "_blank");
  }
}
