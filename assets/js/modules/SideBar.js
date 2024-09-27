document.addEventListener('DOMContentLoaded', function () {

    const startDateElement = document.getElementById('start-date');
    const endDateElement = document.getElementById('end-date');
    
    if (startDateElement && endDateElement) {
        let today = new Date().toISOString().split('T')[0];
        
        startDateElement.setAttribute('min', today);
        endDateElement.setAttribute('min', today);
    }

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
        alert("Please select both start and end dates.");
        return;
    }

    let start = new Date(startDate);
    let end = new Date(endDate);
    let diffTime = Math.abs(end - start);
    let diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;

    let baseTotal = (carPrice + deliveryFee + protectionFee + convenienceFee + tax) * diffDays;
    let refundableDeposit = baseTotal * (refundablePercent / 100);
    let total = baseTotal + refundableDeposit;

    document.getElementById('total-price').textContent = '+ $' + total.toFixed(2);
    document.getElementById('refundable-deposit').textContent = '+ $' + refundableDeposit.toFixed(2);
    document.getElementById('subtotal').textContent = '+ $' + baseTotal.toFixed(2);
}

    function viewLargerMap() {
        if (carLatitude !== 0 && carLongitude !== 0) {
            const mapUrl = `https://www.openstreetmap.org/?mlat=${carLatitude}&mlon=${carLongitude}&zoom=12&marker=${carLatitude},${carLongitude}`;
            window.open(mapUrl, '_blank');
        } else {
            console.error('Car Latitude and Longitude are missing.');
        }
    }

