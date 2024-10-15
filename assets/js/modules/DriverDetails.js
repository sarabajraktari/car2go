document.addEventListener('DOMContentLoaded', function () {
    const monthSelect = document.getElementById('month');
    const daySelect = document.getElementById('day');
    const yearSelect = document.getElementById('year');
    const countrySelect = document.getElementById('country');

  
    if (!monthSelect || !daySelect || !yearSelect || !countrySelect) {
     return;
    }

  
    const currentYear = new Date().getFullYear();
    for (let i = currentYear; i >= 1940; i--) {
        let option = document.createElement('option');
        option.value = i;
        option.textContent = i;
        yearSelect.appendChild(option);
    }

    
    const months = [
        "January", "February", "March", "April", "May", "June",
        "July", "August", "September", "October", "November", "December"
    ];
    months.forEach((month, index) => {
        let option = document.createElement('option');
        option.value = index + 1;  
        option.textContent = month;
        monthSelect.appendChild(option);
    });

    
    function populateDays(month, year) {
        daySelect.innerHTML = ''; 

        const daysInMonth = new Date(year, month, 0).getDate(); 
        for (let i = 1; i <= daysInMonth; i++) {
            let option = document.createElement('option');
            option.value = i;
            option.textContent = i;
            daySelect.appendChild(option);
        }
    }

    yearSelect.addEventListener('change', function () {
        populateDays(monthSelect.value, yearSelect.value);
    });
    monthSelect.addEventListener('change', function () {
        populateDays(monthSelect.value, yearSelect.value);
    });

    populateDays(new Date().getMonth() + 1, currentYear);

    const countries = ["Kosovo", "Serbia", "Albania", "Macedonia", "Greece"];
    countries.forEach(country => {
        let option = document.createElement('option');
        option.value = country;
        option.textContent = country;
        countrySelect.appendChild(option);
    });
});
