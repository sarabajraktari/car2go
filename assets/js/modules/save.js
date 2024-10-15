document.getElementById('confirm-payment-btn').addEventListener('click', function(e) {
    e.preventDefault();

    // Gather form data
    const firstName = document.getElementById('first-name').value.trim();
    const lastName = document.getElementById('last-name').value.trim();
    const email = document.getElementById('email').value.trim();
    const phone = document.getElementById('phone').value.trim();
    const startDate = document.getElementById('start-date').value;
    const endDate = document.getElementById('end-date').value;
    const paymentMethod = document.querySelector('input[name="payment-method"]:checked') ? 
                          document.querySelector('input[name="payment-method"]:checked').value : '';

    // Additional fields for Card payment
    const cardNumberInput = document.getElementById('card-number');
    const expirationInput = document.getElementById('expiration-date');
    const cvvInput = document.getElementById('cvv');
    const cardHolderInput = document.getElementById('card-holder');
    const postalCodeInput = document.getElementById('postal-code');

    // Ensure all necessary fields are filled for both payment methods
    if (!firstName || !lastName || !email || !phone || !startDate || !endDate || !paymentMethod) {
        alert('Please fill in all required fields.');
        return; 
    }

    // Additional validation if payment method is card
    if (paymentMethod === 'card') {
        if (!cardNumberInput.value.trim() || !expirationInput.value.trim() || !cvvInput.value.trim() || 
            !cardHolderInput.value.trim() || !postalCodeInput.value.trim()) {
            alert('Please fill in all the required card details.');
            return; 
        }
    }

    const cardNumber = paymentMethod === 'card' ? cardNumberInput.value.trim() : '';

    const subtotalText = document.getElementById('subtotal').textContent;
    const totalCost = parseFloat(subtotalText.replace(/[^0-9.-]+/g, "")) || 0;

    // Gather form data for submission
    const formData = {
        carTitle: `${cartitle}`,
        firstName: firstName,
        lastName: lastName,
        email: email,
        phone: phone,
        dob: {
            day: document.getElementById('day').value,
            month: document.getElementById('month').value,
            year: document.getElementById('year').value,
        },
        country: document.getElementById('country').value,
        startDate: startDate,
        endDate: endDate,
        addOns: [],
        totalCost: totalCost,
        paymentMethod: paymentMethod,
        cardNumber: cardNumber  
    };

    // Collect add-ons from checkboxes
    document.querySelectorAll('.addon-checkbox').forEach((checkbox) => {
        if (checkbox.checked) {
            formData.addOns.push({
                name: checkbox.parentElement.querySelector('label').textContent,
                cost: checkbox.dataset.cost  
            });
        }
    });

    // Check the data being sent
    // console.log(JSON.stringify(formData));

    // Send the data via fetch to the REST API
    fetch('/wp-json/internship/v1/save-booking', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(formData),
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            alert('Booking saved!');
        } else {
            alert('Error saving booking');
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
});
