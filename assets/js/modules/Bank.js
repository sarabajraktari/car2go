document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('payment-modal');
    const cardNumberInput = document.getElementById('card-number');
    const expirationInput = document.getElementById('expiration-date');
    const cvvInput = document.getElementById('cvv');
    const cardHolderInput = document.getElementById('card-holder');
    const postalCodeInput = document.getElementById('postal-code');

    
    function applyValidationStyle(inputElement, isValid) {
        const errorElement = document.getElementById(`${inputElement.id}-error`);
        if (!inputElement) return; 
        
        if (isValid) {
            inputElement.classList.remove('invalid');
            inputElement.classList.add('valid');
            if (errorElement) { 
                errorElement.classList.add('hidden');
            }
        } else {
            inputElement.classList.remove('valid');
            inputElement.classList.add('invalid');
            if (errorElement) {
                errorElement.classList.remove('hidden');
            }
        }
    }

    function validateCardNumber() {
        const cardNumberPattern = /^[0-9]{13,16}$/;
        const isValidFormat = cardNumberPattern.test(cardNumberInput.value.replace(/\s+/g, '')); 

        if (isValidFormat) {
            const cardNumber = cardNumberInput.value.replace(/\s+/g, ''); 
            // Make an API call to Algobook to verify the card number
            return fetch(`https://api.algobook.info/v1/card/verify?number=${cardNumber}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json'
                    // Add authorization header if needed
                    // 'Authorization': 'Bearer YOUR_API_KEY'  // If the API requires an API key
                }
            })
            .then(response => response.json())
            .then(cardData => {
                if (cardData.valid) {
                    applyValidationStyle(cardNumberInput, true);
                    return true;
                } else {
                    applyValidationStyle(cardNumberInput, false);
                    document.getElementById('card-number-error').classList.remove('hidden');
                    return false;
                }
            })
            .catch(error => {
                console.error('Error validating card:', error);
                applyValidationStyle(cardNumberInput, false);
                return false;
            });
        } else {
            applyValidationStyle(cardNumberInput, false);
            return false;
        }
    }

    function validateExpirationDate() {
        const expirationPattern = /^(0[1-9]|1[0-2])\/\d{2}$/;
        const isValid = expirationPattern.test(expirationInput.value);
        applyValidationStyle(expirationInput, isValid);
        return isValid;
    }

    function validateCVV() {
        const cvvPattern = /^[0-9]{3,4}$/;
        const isValid = cvvPattern.test(cvvInput.value);
        applyValidationStyle(cvvInput, isValid);
        return isValid;
    }

    function validateCardHolder() {
        const isValid = cardHolderInput.value.trim() !== '';
        applyValidationStyle(cardHolderInput, isValid);
        return isValid;
    }

    function validatePostalCode() {
        const postalCodePattern = /^[0-9]{5}$/;
        const isValid = postalCodePattern.test(postalCodeInput.value);
        applyValidationStyle(postalCodeInput, isValid);
        return isValid;
    }

    // Automatically format card number into blocks of 4
    cardNumberInput.addEventListener('input', function () {
        let cardNumber = cardNumberInput.value.replace(/\s+/g, ''); 
        if (cardNumber.length > 0) {
            cardNumber = cardNumber.match(/.{1,4}/g).join(' '); 
        }
        cardNumberInput.value = cardNumber;

        validateCardNumber(); 
    });

    expirationInput.addEventListener('input', validateExpirationDate);
    cvvInput.addEventListener('input', validateCVV);
    cardHolderInput.addEventListener('input', validateCardHolder);
    postalCodeInput.addEventListener('input', validatePostalCode);

    
    document.getElementById('confirm-payment-btn').addEventListener('click', async function (e) {
        e.preventDefault(); 

        let isValid = true;

        // Check if the user selected the "Card" payment option
        const selectedPaymentMethod = document.querySelector('input[name="payment-method"]:checked').value;

        if (selectedPaymentMethod === 'cash') {
            alert('Payment confirmed!');
            resetFormFields(); 
            closePaymentModal(); 
            return;
        }

       
        if (!validateCardNumber()) {
            document.getElementById('card-number-error').classList.remove('hidden');
            isValid = false;
        }

        if (!validateExpirationDate()) {
            document.getElementById('expiration-error').classList.remove('hidden');
            isValid = false;
        }

        if (!validateCVV()) {
            document.getElementById('cvv-error').classList.remove('hidden');
            isValid = false;
        }

        if (!validateCardHolder()) {
            document.getElementById('card-holder-error').classList.remove('hidden');
            isValid = false;
        }

        if (!validatePostalCode()) {
            document.getElementById('postal-error').classList.remove('hidden');
            isValid = false;
        }

   
        if (isValid) {
            alert('Payment confirmed!');
            resetFormFields(); 
            closePaymentModal(); 
        }
    });

   
    modal.addEventListener('click', function (e) {
        if (e.target === modal) {
            closePaymentModal();
        }
    });

    function closePaymentModal() {
        modal.classList.add('hidden');
    }

   
    function resetFormFields() {
    
        cardNumberInput.value = '';
        expirationInput.value = '';
        cvvInput.value = '';
        cardHolderInput.value = '';
        postalCodeInput.value = '';

        cardNumberInput.classList.remove('valid', 'invalid');
        expirationInput.classList.remove('valid', 'invalid');
        cvvInput.classList.remove('valid', 'invalid');
        cardHolderInput.classList.remove('valid', 'invalid');
        postalCodeInput.classList.remove('valid', 'invalid');

        document.querySelectorAll('.text-red-500').forEach(el => el.classList.add('hidden'));
    }
});
