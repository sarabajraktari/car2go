document.addEventListener('DOMContentLoaded', function () {

    function isValidAge() {
        const monthInput = document.getElementById('month');
        const dayInput = document.getElementById('day');
        const yearInput = document.getElementById('year');
        

        if (!monthInput || !dayInput || !yearInput) return false;

        const month = parseInt(monthInput.value);
        const day = parseInt(dayInput.value);
        const year = parseInt(yearInput.value);

        if (!month || !day || !year) {
            return false;
        }

        const today = new Date();
        const birthDate = new Date(year, month - 1, day);
        const age = today.getFullYear() - birthDate.getFullYear();
        const monthDiff = today.getMonth() - birthDate.getMonth();

        return (age > 21 || (age === 21 && monthDiff >= 0 && today.getDate() >= day));
    }

    function isValidName(name) {
        const namePattern = /^[A-Za-z]{1,10}$/;
        return namePattern.test(name);
    }

    function isValidImage() {
        const fileInput = document.getElementById('drivers-license');
        if (!fileInput) return false; 

        const file = fileInput.files[0];
        if (!file) return false;

        const validImageTypes = ['image/jpeg', 'image/png'];
        return validImageTypes.includes(file.type);
    }

    const bookNowBtn = document.getElementById('book-now-btn');
    if (bookNowBtn) {
        bookNowBtn.addEventListener('click', function (e) {
            let valid = true;

            const firstNameInput = document.getElementById('first-name');
            const lastNameInput = document.getElementById('last-name');
            const emailInput = document.getElementById('email');
            const phoneInput = document.getElementById('phone');
            const countryInput = document.getElementById('country');
            const termsAgreementInput = document.getElementById('terms-agreement');
            
            const firstName = firstNameInput ? firstNameInput.value.trim() : '';
            const lastName = lastNameInput ? lastNameInput.value.trim() : '';
            const email = emailInput ? emailInput.value.trim() : '';
            const phone = phoneInput ? phoneInput.value.trim() : '';
            const country = countryInput ? countryInput.value : '';
            const termsAgreement = termsAgreementInput ? termsAgreementInput.checked : false;

            document.querySelectorAll('.text-red-500').forEach(function (el) {
                el.classList.add('hidden');
            });

         
            if (!isValidName(firstName)) {
                const firstNameError = document.getElementById('first-name-error');
                if (firstNameError) firstNameError.classList.remove('hidden');
                valid = false;
            }

            if (!isValidName(lastName)) {
                const lastNameError = document.getElementById('last-name-error');
                if (lastNameError) lastNameError.classList.remove('hidden');
                valid = false;
            }

            const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            if (!emailPattern.test(email)) {
                const emailError = document.getElementById('email-error');
                if (emailError) emailError.classList.remove('hidden');
                valid = false;
            }

            if (!phone || phone.length < 10) {
                const phoneError = document.getElementById('phone-error');
                if (phoneError) phoneError.classList.remove('hidden');
                valid = false;
            }

            if (!isValidAge()) {
                const dobError = document.getElementById('dob-error');
                if (dobError) dobError.classList.remove('hidden');
                valid = false;
            }

            if (!isValidImage()) {
                const licenseError = document.getElementById('license-error');
                if (licenseError) licenseError.classList.remove('hidden');
                valid = false;
            }

            if (!country) {
                const countryError = document.getElementById('country-error');
                if (countryError) countryError.classList.remove('hidden');
                valid = false;
            }

            if (!termsAgreement) {
                const termsError = document.getElementById('terms-error');
                if (termsError) termsError.classList.remove('hidden');
                valid = false;
            }

            if (valid) {
                openPaymentModal();
            } else {
                e.preventDefault();
            }
        });
    }

    const formInputs = document.querySelectorAll('#DriverDetails input, #DriverDetails select');
    formInputs.forEach(input => {
        input.addEventListener('focus', () => {
            input.classList.remove('touched');
        });

        input.addEventListener('blur', () => {
            input.classList.add('touched');
        });
    });
});


function openPaymentModal() {
    const paymentModal = document.getElementById('payment-modal');
    if (paymentModal) {
        paymentModal.classList.remove('hidden');
    }
}
