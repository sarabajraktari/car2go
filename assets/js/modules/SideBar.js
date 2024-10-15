document.addEventListener('DOMContentLoaded', function () {

  const startDateElement = document.getElementById('start-date');
  const endDateElement = document.getElementById('end-date');
  const addonCheckboxes = document.querySelectorAll('.addon-checkbox');
  const quantityButtons = document.querySelectorAll('.quantity-button');
  let unavailableDates = [];

 
  fetchUnavailableDates();

  disableAddOns(true);


  function initializeDatePickers() {
      flatpickr(startDateElement, {
          minDate: 'today',
          disable: unavailableDates.map(date => new Date(date)), 
          onChange: validateDates
      });

      flatpickr(endDateElement, {
          minDate: 'today',
          disable: unavailableDates.map(date => new Date(date)), 
          onChange: validateDates
      });
  }

  // Fetch unavailable dates from the API
  function fetchUnavailableDates() {
      fetch('http://internship.test/wp-json/internship/v1/unavailable-dates')
          .then(response => response.json())
          .then(data => {
              if (data.status === 'success') {
                  const carTitle = `${cartitle}`; 
                  unavailableDates = data.unavailable_dates[carTitle] || []; 
                  initializeDatePickers(); 
              }
          })
          .catch(error => console.error('Error fetching unavailable dates:', error));
  }


  function validateDates(selectedDates, dateStr, instance) {
      let startDate = startDateElement.value;
      let endDate = endDateElement.value;

      if (startDate && endDate) {
          disableAddOns(false); 
          calculateTotal(); 
      } else {
          disableAddOns(true); 
      }
  }


  function disableAddOns(disable) {
      addonCheckboxes.forEach(function (checkbox) {
          checkbox.disabled = disable;
          if (disable) {
              checkbox.addEventListener('click', preventInteraction);
          } else {
              checkbox.removeEventListener('click', preventInteraction);
          }
      });

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

function preventInteraction(e) {
  e.preventDefault();
}

addonCheckboxes.forEach(function (checkbox) {
    checkbox.addEventListener('change', calculateTotal);
});

document.querySelectorAll('[id$="-quantity"]').forEach(function(quantityElement) {
    const parentContainer = quantityElement.closest('div');
    parentContainer.querySelectorAll('button').forEach(function(button) {
        button.addEventListener('click', function() {
            calculateTotal();  
        });
    });
});

const mapSection = document.getElementById('map-section');
const button = document.getElementById('view-larger-map-btn');

if (mapSection && button) {
  if (typeof carLatitude !== 'undefined' && typeof carLongitude !== 'undefined' && carLatitude !== 0 && carLongitude !== 0) {
    const map = L.map('map').setView([carLatitude, carLongitude], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    L.marker([carLatitude, carLongitude]).addTo(map)
      .bindPopup(`<b>Car Location</b><br>${cartitle}`)
      .openPopup();
  } else {
    mapSection.style.display = 'none';  
    button.style.display = 'none';     
  }
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
