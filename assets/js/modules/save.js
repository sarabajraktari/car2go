var confirmPaymentBtn = document.getElementById('confirm-payment-btn');
if (confirmPaymentBtn) {
    confirmPaymentBtn.addEventListener('click', function(e) {
        e.preventDefault();

       
        const firstNameInput = document.getElementById('first-name');
        const lastNameInput = document.getElementById('last-name');
        const emailInput = document.getElementById('email');
        const phoneInput = document.getElementById('phone');
        const startDateInput = document.getElementById('start-date');
        const endDateInput = document.getElementById('end-date');
        const paymentMethodInput = document.querySelector('input[name="payment-method"]:checked');
        
        const firstName = firstNameInput ? firstNameInput.value.trim() : '';
        const lastName = lastNameInput ? lastNameInput.value.trim() : '';
        const email = emailInput ? emailInput.value.trim() : '';
        const phone = phoneInput ? phoneInput.value.trim() : '';
        const startDate = startDateInput ? startDateInput.value : '';
        const endDate = endDateInput ? endDateInput.value : '';
        const paymentMethod = paymentMethodInput ? paymentMethodInput.value : '';

        
        const cardNumberInput = document.getElementById('card-number');
        const expirationInput = document.getElementById('expiration-date');
        const cvvInput = document.getElementById('cvv');
        const cardHolderInput = document.getElementById('card-holder');
        const postalCodeInput = document.getElementById('postal-code');

       
        if (!firstName || !lastName || !email || !phone || !startDate || !endDate || !paymentMethod) {
            alert('Please fill in all required fields.');
            return;
        }

       
        if (paymentMethod === 'card') {
            if (!cardNumberInput?.value.trim() || !expirationInput?.value.trim() || !cvvInput?.value.trim() ||
                !cardHolderInput?.value.trim() || !postalCodeInput?.value.trim()) {
                alert('Please fill in all the required card details.');
                return;
            }
        }

        const cardNumber = paymentMethod === 'card' ? cardNumberInput?.value.trim() : '';

        const subtotalText = document.getElementById('subtotal') ? document.getElementById('subtotal').textContent : '';
        const totalCost = parseFloat(subtotalText.replace(/[^0-9.-]+/g, "")) || 0;

       
        const formData = {
            carTitle: typeof cartitle !== 'undefined' ? cartitle : '',  
            firstName: firstName,
            lastName: lastName,
            email: email,
            phone: phone,
            dob: {
                day: document.getElementById('day') ? document.getElementById('day').value : '',
                month: document.getElementById('month') ? document.getElementById('month').value : '',
                year: document.getElementById('year') ? document.getElementById('year').value : '',
            },
            country: document.getElementById('country') ? document.getElementById('country').value : '',
            startDate: startDate,
            endDate: endDate,
            addOns: [],
            totalCost: totalCost,
            paymentMethod: paymentMethod,
            cardNumber: cardNumber  
        };

    
        document.querySelectorAll('.addon-checkbox').forEach((checkbox) => {
            if (checkbox.checked) {
                formData.addOns.push({
                    name: checkbox.parentElement.querySelector('label') ? checkbox.parentElement.querySelector('label').textContent : '',
                    cost: checkbox.dataset.cost
                });
            }
        });

       
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
}
