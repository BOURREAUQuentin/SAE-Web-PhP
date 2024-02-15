document.getElementById('buttonVoirPlus').addEventListener('click', function() {
    var discContainers = document.querySelectorAll('.disc-container');
    var button = document.getElementById('voir');
    var icon = document.getElementById('icon');

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
