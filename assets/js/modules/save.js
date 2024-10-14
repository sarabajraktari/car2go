document.getElementById('confirm-payment-btn').addEventListener('click', function(e) {
    e.preventDefault();

    
    const subtotalText = document.getElementById('subtotal').textContent;
    const totalCost = parseFloat(subtotalText.replace(/[^0-9.-]+/g, "")) || 0;

    // Gather form data
    const formData = {
        carTitle : `${cartitle}`,  
        firstName: document.getElementById('first-name').value,
        lastName: document.getElementById('last-name').value,
        email: document.getElementById('email').value,
        phone: document.getElementById('phone').value,
        dob: {
            day: document.getElementById('day').value,
            month: document.getElementById('month').value,
            year: document.getElementById('year').value,
        },
        country: document.getElementById('country').value,
        startDate: document.getElementById('start-date').value,
        endDate: document.getElementById('end-date').value,
        addOns: [],  
        totalCost: totalCost,  
        paymentMethod: document.querySelector('input[name="payment-method"]:checked').value,
        cardNumber: document.querySelector('input[name="payment-method"]:checked').value === 'card' 
                    ? document.getElementById('card-number').value 
                    : ''  
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
