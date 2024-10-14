document.addEventListener('DOMContentLoaded', function () {
   
    function isValidAge() {
        const month = parseInt(document.getElementById('month').value);
        const day = parseInt(document.getElementById('day').value);
        const year = parseInt(document.getElementById('year').value);

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
        const file = fileInput.files[0];

        if (!file) {
            return false;
        }

        const validImageTypes = ['image/jpeg', 'image/png'];
        if (!validImageTypes.includes(file.type)) {
            return false;
        }

        return true;
    }


    document.getElementById('book-now-btn').addEventListener('click', function (e) {
        // console.log('Button clicked! Starting validation...');
        let valid = true;


        const firstName = document.getElementById('first-name').value.trim();
        const lastName = document.getElementById('last-name').value.trim();
        const email = document.getElementById('email').value.trim();
        const phone = document.getElementById('phone').value.trim();
        const country = document.getElementById('country').value; // Added country
        const termsAgreement = document.getElementById('terms-agreement').checked; // Added terms agreement



        document.querySelectorAll('.text-red-500').forEach(function(el) {
            el.classList.add('hidden');
        });


        if (!isValidName(firstName)) {
            document.getElementById('first-name-error').classList.remove('hidden');
            valid = false;
        }


        if (!isValidName(lastName)) {
            document.getElementById('last-name-error').classList.remove('hidden');
            valid = false;
        }

        const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        if (!emailPattern.test(email)) {
            document.getElementById('email-error').classList.remove('hidden');
            valid = false;
        }

 
        if (!phone || phone.length < 10) {
            document.getElementById('phone-error').classList.remove('hidden');
            valid = false;
        }


        if (!isValidAge()) {
            document.getElementById('dob-error').classList.remove('hidden');
            valid = false;
        }


        if (!isValidImage()) {
            document.getElementById('license-error').classList.remove('hidden');
            valid = false;
        }
        if (country === '') {
            document.getElementById('country-error').classList.remove('hidden');
            valid = false;
        }

        if (!termsAgreement) {
            document.getElementById('terms-error').classList.remove('hidden');
            valid = false;
        }


        if (valid) {
            // console.log('Driver details form is valid!');
            openPaymentModal(); 
        } else {
            e.preventDefault();
        }
    });
});

function openPaymentModal() {
    document.getElementById('payment-modal').classList.remove('hidden');
}

document.addEventListener('DOMContentLoaded', function () {
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
