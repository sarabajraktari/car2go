const clickMe = document.querySelector('.click-me');

if (clickMe) {
    clickMe.addEventListener('click', function () {
        if (clickMe.classList.contains('text-red-200')) {
            clickMe.classList.remove('text-red-200');
            clickMe.classList.add('text-black');
        } else {
            clickMe.classList.remove('text-black');
            clickMe.classList.add('text-red-200');
        }
    });
}
