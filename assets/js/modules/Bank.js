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
                generatePDF();
                resetForm();
                resetBookingForm(); 
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
                generatePDF();
                resetForm();
                resetBookingForm(); 
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

    function generatePDF() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();
    
        const firstName = document.getElementById('first-name').value;
        const lastName = document.getElementById('last-name').value;
        const email = document.getElementById('email').value;
        const phone = document.getElementById('phone').value;
        const country = document.getElementById('country').value;
        const month = document.getElementById('month').value;
        const day = document.getElementById('day').value;
        const year = document.getElementById('year').value;
        const paymentType = document.querySelector('input[name="payment-method"]:checked').value;
        
       
        const subtotalElement = document.getElementById('subtotal');
        const subtotalText = subtotalElement ? subtotalElement.textContent : '';
        const subtotal = parseFloat(subtotalText.replace(/[^0-9.-]+/g, "")) || 0;
    
       
        doc.setFontSize(22);
        doc.text("Car2Go", 105, 20, null, null, 'center');
        doc.setFontSize(12);
        doc.text("Agreement: By signing this document, you agree to pay the following amount according to the law.", 105, 30, null, null, 'center');
    
       
        const startX = 20;
        let startY = 50;
    
   
        doc.setFontSize(14);
        doc.text("Field", startX, startY);
        doc.text("Details", startX + 60, startY);
        doc.line(startX, startY + 2, startX + 160, startY + 2); 
    
       
        doc.setFontSize(12);
        startY += 10;
        const lineHeight = 8;
    
        const rows = [
            ["First Name", firstName],
            ["Last Name", lastName],
            ["Email", email],
            ["Phone", phone],
            ["Country", country],
            ["Date of Birth", `${month}/${day}/${year}`],
            ["Payment Type", paymentType],
            ["Cost", `$${subtotal.toFixed(2)}`]
        ];
    
        rows.forEach(([field, value]) => {
            doc.text(field, startX, startY);
            doc.text(value, startX + 60, startY);
            doc.line(startX, startY + 2, startX + 160, startY + 2); 
            startY += lineHeight;
        });
    
        // Capture driver's license image
        const fileInput = document.getElementById('drivers-license');
        const file = fileInput.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                const imgData = e.target.result;
                
                // Add driver's license image larger and centered
                doc.addImage(imgData, 'JPEG', 65, startY + 10, 80, 80);
                startY += 100;
    
                // Signature line
                doc.setFontSize(14);
                doc.text("Signature:", 105, startY + 20, null, null, 'center');
                doc.line(85, startY + 25, 125, startY + 25); 
                doc.setFontSize(10);
                doc.text("Car2Go", 105, startY + 35, null, null, 'center');
    
                doc.save("BookingDetails.pdf");
            };
            reader.readAsDataURL(file);
        } else {
            // Signature line
            doc.setFontSize(14);
            doc.text("Signature:", 105, startY + 10, null, null, 'center');
            doc.line(85, startY + 15, 125, startY + 15);
            doc.text("Car2Go", 105, startY + 25, null, null, 'center');
    
          
            doc.save("BookingDetails.pdf");
        }
    }
    

    function resetBookingForm() {
   
    const firstNameInput = document.getElementById('first-name');
    const lastNameInput = document.getElementById('last-name');
    const emailInput = document.getElementById('email');
    const phoneInput = document.getElementById('phone');
    const countryInput = document.getElementById('country');
    const termsAgreementInput = document.getElementById('terms-agreement');
    const monthInput = document.getElementById('month');
    const dayInput = document.getElementById('day');
    const yearInput = document.getElementById('year');
    const fileInput = document.getElementById('drivers-license');

    if (firstNameInput) firstNameInput.value = '';
    if (lastNameInput) lastNameInput.value = '';
    if (emailInput) emailInput.value = '';
    if (phoneInput) phoneInput.value = '';
    if (countryInput) countryInput.value = '';
    if (termsAgreementInput) termsAgreementInput.checked = false;
    if (monthInput) monthInput.value = '';
    if (dayInput) dayInput.value = '';
    if (yearInput) yearInput.value = '';
    if (fileInput) fileInput.value = '';

    // Remove validation error messages
    document.querySelectorAll('.text-red-500').forEach(el => el.classList.add('hidden'));

    // Reset touched classes
    document.querySelectorAll('.touched').forEach(el => el.classList.remove('touched'));
}


 function resetForm() {

    const startDateElement = document.getElementById('start-date');
    const endDateElement = document.getElementById('end-date');
    if (startDateElement) startDateElement.value = '';
    if (endDateElement) endDateElement.value = '';

 
    const addonCheckboxes = document.querySelectorAll('.addon-checkbox');
    addonCheckboxes.forEach(function (checkbox) {
        checkbox.checked = false;
    });

  
    document.querySelectorAll('[id$="-quantity"]').forEach(function (quantityElement) {
        quantityElement.textContent = '0'; 
    });

   
    document.getElementById('subtotal').textContent = '+ $0.00';
    document.getElementById('refundable-deposit').textContent = '+ $0.00';
}


});
