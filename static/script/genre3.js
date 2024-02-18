document.getElementById('buttonVoirPlus3').addEventListener('click', function() {
    var discContainers = document.querySelectorAll('.disc-container3');
    var button = document.getElementById('voir3');
    var icon = document.getElementById('icon3');

    if (button.textContent === 'Voir plus') {
        for (var i = 5; i < discContainers.length; i++) {
            discContainers[i].style.display = 'block';
        }
        button.textContent = 'Voir moins';
        icon.textContent = '-';
    } else {
        for (var i = 5; i < discContainers.length; i++) {
            discContainers[i].style.display = 'none';
        }
        button.textContent = 'Voir plus';
        icon.textContent = '+';
    }
});