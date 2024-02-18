document.getElementById('buttonVoirPlus2').addEventListener('click', function() {
    var song = document.querySelectorAll('.song');
    var button = document.getElementById('voir2');
    var icon = document.getElementById('icon2');

    if (button.textContent === 'Voir plus') {
        for (var i = 5; i < song.length; i++) {
            song[i].style.display = 'block';
        }
        button.textContent = 'Voir moins';
        icon.textContent = '-';
    } else {
        for (var i = 5; i < song.length; i++) {
            song[i].style.display = 'none';
        }
        button.textContent = 'Voir plus';
        icon.textContent = '+';
    }
});