function confirmSuppressionPlaylist(id_playlist) {
    // Affiche une boîte de dialogue de confirmation
    var confirmation = confirm("Êtes-vous sûr de vouloir supprimer cette playlist ?");
    // Si l'utilisateur clique sur OK, retourne true (continue la suppression)
    // Sinon, retourne false (arrête la suppression)
    if (confirmation) {
        window.location.href = "?action=supprimer_playlist&id_playlist=" + id_playlist;
    }
    return false;
}
function showEditForm(id_playlist) {
    // Récupérer le formulaire de modification correspondant à l'ID de la playlist
    var editForm = document.getElementById("editForm_" + id_playlist);
    // Afficher le formulaire de modification en le rendant visible
    editForm.style.display = "block";
    // Retourner false pour éviter que le lien ne déclenche une action supplémentaire
    return false;
}
function cancelEdit(id_playlist) {
    // Récupérer le formulaire de modification correspondant à l'ID de la playlist
    var editForm = document.getElementById("editForm_" + id_playlist);
    // Masquer le formulaire de modification
    editForm.style.display = "none";
}