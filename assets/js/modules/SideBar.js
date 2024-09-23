let today = new Date().toISOString().split('T')[0];
document.getElementById('start-date').setAttribute('min', today);
document.getElementById('end-date').setAttribute('min', today);

function calculateTotal() {
    // Use the carPrice defined in the Twig template
    let deliveryFee = 60;
    let protectionFee = 25;
    let convenienceFee = 2;
    let tax = 2;
    let refundablePercent = 5;

    let startDate = document.getElementById('start-date').value;
    let endDate = document.getElementById('end-date').value;

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
