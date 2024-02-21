function confirmSuppressionArtiste(id_artiste) {
    // Affiche une boîte de dialogue de confirmation
    console.log(id_artiste);
    var confirmation = confirm("Êtes-vous sûr de vouloir supprimer l'artiste ?");
    // Si l'utilisateur clique sur OK, retourne true (continue la suppression)
    // Sinon, retourne false (arrête la suppression)
    if (confirmation) {
        window.location.href = "?action=supprimer_artiste&id_artiste=" + id_artiste;
    }
    return false;
}


function showEditForm(artisteId) {
    // Récupérer le formulaire de modification correspondant à l'ID de l'artiste
    var editForm = document.getElementById("editForm_" + artisteId);
    // Afficher le formulaire de modification en le rendant visible
    editForm.style.display = "block";
    // Retourner false pour éviter que le lien ne déclenche une action supplémentaire
    return false;
}

function cancelEdit(artisteId) {
    // Récupérer le formulaire de modification correspondant à l'ID de l'artiste
    var editForm = document.getElementById("editForm_" + artisteId);
    // Masquer le formulaire de modification
    editForm.style.display = "none";
    // Retourner false pour éviter que le lien ne déclenche une action supplémentaire
    return false;
}