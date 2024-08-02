document.querySelectorAll('[id^="question"]').forEach(function(button, index) {
    button.addEventListener('click', function() {
        var answer = document.getElementById('answer' + (index + 1));
        var arrow = document.getElementById('arrow' + (index + 1));

        if (answer.style.display === 'none' || answer.style.display === '') {
            answer.style.display = 'block';
            arrow.style.transform = 'rotate(0deg)';
        } else {
            answer.style.display = 'none';
            arrow.style.transform = 'rotate(-180deg)';
        }
    });
});