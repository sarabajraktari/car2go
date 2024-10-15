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
            if (errorElement) errorElement.classList.add('hidden');
        } else {
            inputElement.classList.remove('valid');
            inputElement.classList.add('invalid');
            if (errorElement) errorElement.classList.remove('hidden');
        }
    }

  
    async function validateCardNumber() {
        const cardNumberPattern = /^[0-9]{13,16}$/;
        const cardNumber = cardNumberInput.value.replace(/\s+/g, ''); 
        const isValidFormat = cardNumberPattern.test(cardNumber);

        if (isValidFormat) {
            try {
                const response = await fetch(`https://api.algobook.info/v1/card/verify?number=${cardNumber}`, {
                    method: 'GET',
                    headers: { 'Content-Type': 'application/json' }
                });
                const cardData = await response.json();

                if (cardData.valid) {
                    applyValidationStyle(cardNumberInput, true);
                    return true;
                } else {
                    applyValidationStyle(cardNumberInput, false);
                    return false;
                }
            } catch (error) {
                console.error('Error validating card:', error);
                applyValidationStyle(cardNumberInput, false);
                return false;
            }
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

   
    if (cardNumberInput) {
        cardNumberInput.addEventListener('input', function () {
            let cardNumber = cardNumberInput.value.replace(/\s+/g, ''); 
            if (cardNumber.length > 0) {
                cardNumber = cardNumber.match(/.{1,4}/g)?.join(' ') || ''; 
            }
            cardNumberInput.value = cardNumber;

          
            validateCardNumber();
        });
    }

    if (expirationInput) expirationInput.addEventListener('input', validateExpirationDate);
    if (cvvInput) cvvInput.addEventListener('input', validateCVV);
    if (cardHolderInput) cardHolderInput.addEventListener('input', validateCardHolder);
    if (postalCodeInput) postalCodeInput.addEventListener('input', validatePostalCode);

   
    const confirmPaymentBtn = document.getElementById('confirm-payment-btn');
    if (confirmPaymentBtn) {
        confirmPaymentBtn.addEventListener('click', async function (e) {
            e.preventDefault(); 

            let isValid = true;
            const selectedPaymentMethod = document.querySelector('input[name="payment-method"]:checked').value;

            if (selectedPaymentMethod === 'cash') {
                resetFormFields(); 
                closePaymentModal(); 
                return;
            }

            if (!await validateCardNumber()) isValid = false;
            if (!validateExpirationDate()) isValid = false;
            if (!validateCVV()) isValid = false;
            if (!validateCardHolder()) isValid = false;
            if (!validatePostalCode()) isValid = false;

            if (isValid) {
                resetFormFields(); 
                closePaymentModal(); 
            }
        });
    }

   
    if (modal) {
        modal.addEventListener('click', function (e) {
            if (e.target === modal) closePaymentModal();
        });
    }

   
    function closePaymentModal() {
        if (modal) modal.classList.add('hidden');
    }

    
    function resetFormFields() {
        cardNumberInput.value = '';
        expirationInput.value = '';
        cvvInput.value = '';
        cardHolderInput.value = '';
        postalCodeInput.value = '';

        [cardNumberInput, expirationInput, cvvInput, cardHolderInput, postalCodeInput].forEach(input => {
            if (input) input.classList.remove('valid', 'invalid');
        });

        document.querySelectorAll('.text-red-500').forEach(el => el.classList.add('hidden'));
    }
});
