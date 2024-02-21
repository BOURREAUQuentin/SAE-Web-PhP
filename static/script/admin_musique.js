function confirmSuppressionMusique(id_musique) {
    // Affiche une boîte de dialogue de confirmation
    var confirmation = confirm("Êtes-vous sûr de vouloir supprimer cette musique ?");
    // Si l'utilisateur clique sur OK, retourne true (continue la suppression)
    // Sinon, retourne false (arrête la suppression)
    if (confirmation) {
        window.location.href = "?action=supprimer_musique&id_musique=" + id_musique;
    }
    return false;
}
function showEditForm(id_musique) {
    // Récupérer le formulaire de modification correspondant à l'ID de la musique
    var editForm = document.getElementById("editForm_" + id_musique);
    // Afficher le formulaire de modification en le rendant visible
    editForm.style.display = "block";
    // Retourner false pour éviter que le lien ne déclenche une action supplémentaire
    return false;
}
function cancelEdit(id_musique) {
    // Récupérer le formulaire de modification correspondant à l'ID de la musique
    var editForm = document.getElementById("editForm_" + id_musique);
    // Masquer le formulaire de modification
    editForm.style.display = "none";

    // Récupérer le champ de saisie correspondant
    var nomMusiqueInput = document.getElementById("nouveau_nom_musique" + id_musique);

    // Masquer le champ de saisie correspondant
    nomMusiqueInput.style.display = "none";
}


// partie pour le calcul de la durée de la nouvelle musique à ajouter

// Ajout d'un écouteur d'événements au champ de fichier MP3 pour détecter les changements
document.getElementById('fichier_mp3').addEventListener('change', function() {
    obtenirDureeAudio(this);
});

// Fonction pour obtenir la durée formatée du fichier audio
function obtenirDureeFormattee(duree) {
    var minutes = Math.floor(duree / 60);
    var secondes = Math.floor(duree % 60);
    // Formatage de la durée au format MM:SS
    var duree_formattee = (minutes < 10 ? '0' : '') + minutes + ':' + (secondes < 10 ? '0' : '') + secondes;
    return duree_formattee;
}

// Fonction pour obtenir la durée du fichier audio
function obtenirDureeAudio(input) {
    if (input.files && input.files[0]) {
        var audio = document.getElementById('audioPreview');
        var file = input.files[0];
        var reader = new FileReader();

        reader.onload = function(e) {
            // Charge le fichier audio pour obtenir ses informations
            audio.src = e.target.result;
        };

        // Attente que les métadonnées du fichier audio soient chargées
        audio.onloadedmetadata = function() {
            // Affiche la durée du fichier audio formatée dans l'élément HTML
            var duree_formattee = obtenirDureeFormattee(audio.duration);
            document.getElementById('duree_audio').value = duree_formattee;
            console.log(duree_formattee);
        };

        reader.readAsDataURL(file);
    }
}